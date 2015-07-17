<?php
include_once(DIR_APPLICATION."controller/weixin/weixin.php");

class ControllerWeixinCustomerlist extends ControllerWeixinWeixin {
	
	public function index() {
		
		if ($this->weixin_init() != true) {
			return; //首次验证或初始化失败
		}
		
		$wxtools = new WeixinTools();
		$ret = new stdClass();
		
		//创建菜单
		$url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token=".$this->access_token;
		$res = $wxtools->getFromWx($url);
		if ($res == false) {
			$this->log->write("weixin user get error.");
			$ret->status = -1;
			$this->response->setOutput(json_encode($ret));
			return;
		}

		$customers = array();
		while(true) {
			$json = json_decode($res);
			if (isset($json->errcode)) {
				$ret->status = -1;
				$ret->return = $json;
				$this->response->setOutput(json_encode($ret));
				return;
			}
			
			$customers = array_merge($customers, $json->data->openid);
			
			if (!isset($json->next_openid) || $json->next_openid == null || $json->next_openid == "")
				break;
			
			$url2 = $url."&next_openid=".$json->next_openid;
			$res = $wxtools->getFromWx($url);
			if ($res == false) {
				$this->log->write("weixin user next get error.");
				$ret->status = -1;
				$this->response->setOutput(json_encode($ret));
				return;
			}
		}
		
		if (count($customers) > 0) {
			$this->load->model('weixin/customer');
			$this->model_weixin_customer->updateOpenid($customers);
		}
		
		$ret->status = 0;
		$this->response->setOutput(json_encode($ret));
	}
	
}
?>