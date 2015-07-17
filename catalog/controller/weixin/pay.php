<?php
include_once(DIR_APPLICATION."controller/weixin/weixin.php");

class ControllerWeixinPay extends ControllerWeixinWeixin { 
	public function index() {
		
		if (!isset($this->session->data['order_info'])) {
			$this->log->write("SESSION中没有要支付的订单");
			$this->redirect($this->url->link('mobile_store/home'));
		}
		$order_info = $this->session->data['order_info'];
		
		if ($order_info['order_status_id'] != 2) { //不是 待付款 状态不进行支付流程
			$this->log->write("订单状态不是可支付状态：".$order_info["order_id"]);
			$this->session->data['error_msg'] = '订单暂不能支付，可能订单未准备好';
			$this->session->data['url_continue'] = $this->url->link('mobile_store/order');
			$this->session->data['text_continue'] = '马上查看订单';
			$this->redirect($this->url->link('weixin/error'));
			return;
		}
		
		$pay_total = $order_info['total'];
		if (isset($this->session->data['coupon'])) {
			$coupon = $this->session->data['coupon'];
			if ($pay_total == $coupon['order_total']) {
				$pay_total = $coupon['remain'];
			}
		}
		
		if ($pay_total <= 0) {
			$this->load->model('checkout/order');
			$this->model_checkout_order->orderChangeStatus($order_info);
			$this->model_checkout_order->fastupdate($order_info['order_id'], $order_info);
			
			$this->load->model('checkout/coupon');
			$ret = $this->model_checkout_coupon->commitCoupon($this->customer->getId(), $coupon['coupon_id'],
				$order_info['order_id'], $order_info['total']);

			$this->log->write("订单金额<=0：".$order_info["order_id"]);
			$this->session->data['error_msg'] = '优惠劵支付完成';
			$this->session->data['url_continue'] = $this->url->link('mobile_store/order');
			$this->session->data['text_continue'] = '马上查看订单';
			$this->redirect($this->url->link('weixin/error'));
			return;
		}
		
		if ($this->weixin_init() != true) {
			$this->log->write("微信初始化失败：".$order_info["order_id"]);
			$this->error();
			return; //首次验证或初始化失败
		}
		
		$body = (string)$order_info['comment'];
		if (strlen($body) > 127) {
			$bs = explode(' ', $body);
			$body = "";
			foreach($bs as $p) {
				$body .= "$p ";
				if (strlen($body) > 80)
					break;
			}
		}
		
		if ($body == "") {
			$body = "菜鸽子多件商品";
		}
		
		$wxPayHelper = new PayHelper();
		$wxPayHelper->add_param("appid", (string)$this->appid);
		$wxPayHelper->add_param("mch_id", (string)$this->partnerid);
		$wxPayHelper->add_param("nonce_str", (string)time());
		$wxPayHelper->add_param("body", $body);
		$wxPayHelper->add_param("out_trade_no", (string)$order_info['order_id']);
		$wxPayHelper->add_param("total_fee", (int)($pay_total*100));
		$notify_url = str_replace("weixin.php", "paynotify.php", $this->url->get_index());
		$wxPayHelper->add_param("notify_url", $notify_url);
		$wxPayHelper->add_param("spbill_create_ip", (string)$this->request->server['REMOTE_ADDR']);
		$wxPayHelper->add_param("trade_type", "JSAPI");
		$wxPayHelper->add_param("openid", $this->customer->getEmail());
		
		$wxtools = new WeixinTools();
		$request = $wxPayHelper->make_request($this->partnerkey);
		$url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
		$response = $wxtools->postToWx($url, $request);
		if ($response == false) {
			$this->log->write("微信prepay出错：".$order_info["order_id"]."\n".print_r($request,true));
			$this->error();
			return;
		}
		
		$resHelper = new PayHelper();
		$res = $resHelper->parse_response($response);
		if (isset($res->return_code) == false || isset($res->return_msg) == false ||
			isset($res->result_code) == false || (string)$res->return_code != 'SUCCESS' ||
			(string)$res->result_code != 'SUCCESS') {
			$this->log->write("微信prepay返回失败: ".$order_info["order_id"]."\n".$response."\n".print_r($request,true));
			$this->error();
			return;
		}
		
		if ($resHelper->sign_verify($this->partnerkey) != true) {
			$this->log->write("微信prepay签名验证出错: ". $order_info["order_id"]."\n".$response."\n".print_r($request,true));
			$this->error();
			return;
		}
		
		$jsHelper = new PayHelper();
		$jsHelper->add_param('appId', $resHelper->get('appid'));
		$jsHelper->add_param('signType', 'MD5');
		$jsHelper->add_param('package', "prepay_id=".$resHelper->get('prepay_id'));
		$jsHelper->add_param('timeStamp', time());
		$jsHelper->add_param('nonceStr', $jsHelper->get('timeStamp'));
		$this->data['appId'] = $jsHelper->get('appId');
		$this->data['signType'] = $jsHelper->get('signType');
		$this->data['package'] = $jsHelper->get('package');
		$this->data['timeStamp'] = $jsHelper->get('timeStamp');
		$this->data['nonceStr'] = $jsHelper->get('nonceStr');
		$this->data['paySign'] = $jsHelper->sign_make($this->partnerkey);
		
		$this->data['pay_result'] = $this->url->link('weixin/pay_result', "showwxpaytitle=1");
		
		// view template
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/weixin/pay.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/weixin/pay.tpl';
		} else {
			$this->template = 'default/template/weixin/pay.tpl';
		}
		
		$this->children = array(
			'mobile_store/titlebar',
			'mobile_store/header'
		);
		
		$this->response->setOutput($this->render());
	}
	
	public function prepay() {
		if (!isset($this->session->data['order_info'])) {
			$this->redirect($this->url->link('mobile_store/home'));
		}
		$order_info = $this->session->data['order_info'];
		
		if ($this->weixin_init() != true) {
			$this->log->write("微信初始化出错：".$order_info["order_id"]);
			$this->error();
			return; //首次验证或初始化失败
		}
		
		switch ($order_info['order_type']) {
			case 0:
			case 1:
				$this->session->data['error_msg'] = '下单成功，请等待门店称重后发送微信支付消息。';
				break;
			case 2:	
				$this->session->data['error_msg'] = '预定成功，商品发货前，您会收到微信支付消息。';
				break;
		}
		$this->session->data['url_continue'] = $this->url->link('mobile_store/order/info', 'order_id='.$order_info['order_id']);
		$this->session->data['text_continue'] = '马上查看订单';
		$this->sendOrderNotify($order_info);
		$this->redirect($this->url->link('weixin/error'));
	}
	
/*	
	public function sendToKf() {
		if (isset($this->session->data['openid'])) {
			$this->load->model("weixin/auto_reply");
			$xml = $this->model_weixin_auto_reply->makeXmlMuService(
				$this->session->data['openid'], 'gh_98fc727a4c89', 'onesliu@caigezi2');

			$wxtools = new WeixinTools();
			$url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
			$response = $wxtools->postToWx($url, $request);
			if ($response == false) {
				$this->log->write("weixin prepay response error.");
				$this->error();
				return;
			}
		}
	}
*/
}

?>