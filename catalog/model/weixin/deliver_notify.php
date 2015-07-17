<?php
include_once(DIR_APPLICATION."controller/weixin/weixin.php");

class ModelWeixinDeliverNotify extends Model {
	
	public function notify($access_token, $order_info) {
		
		if ($access_token == null || $order_info == null)
			return false;

		$content = $this->make_request($order_info);
			
		$url = "https://api.weixin.qq.com/pay/orderquery?access_token=$access_token";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.1 Safari/537.11');
		$res = curl_exec($ch);
		$rescode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		$result = json_decode($res);
		
		return $result;
	}
	
	private function make_request($order_info) {
		$wxPayHelper = new WxPayHelper($this);
		
		$wxPayHelper->setParameter("appid", $this->config->get('weixin_appid'));
		$wxPayHelper->setParameter("openid", $this->session->data['openid']);
		$wxPayHelper->setParameter("transid", $order_info['order_id']);
		$wxPayHelper->setParameter("partner", $this->config->get('weixin_partnerid'));
		$wxPayHelper->setParameter("key", $this->config->get('weixin_partnerkey'));
		$sign = strtoupper(md5($wxPayHelper->sort_param()));
		
		$json = new stdClass();
		$json->appid = $this->appid;
		$json->openid = $this->session->data['openid'];
		$json->package = "out_trade_no=".$order_info['order_id'].
			"&partner=".$this->config->get('weixin_partnerid').
			"&sign=".$sign;
		$json->timestamp = time();
		$json->sign_method = "sha1";
	
		$wxPayHelper = new WxPayHelper($this);
		$wxPayHelper->setParameter("appid", $this->config->get('weixin_appid'));
		$wxPayHelper->setParameter("appkey", $this->config->get('weixin_paysignkey'));
		$wxPayHelper->setParameter("package", $json->package);
		$wxPayHelper->setParameter("timestamp", $json->timestamp);
		
		$sign = $wxPayHelper->create_addr_sign();
		
		$json->app_signature = $sign['sha1'];
		
		return json_encode($json);
	}

}
?>