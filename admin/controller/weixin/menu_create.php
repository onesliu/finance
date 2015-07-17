<?php
include_once(DIR_APPLICATION."controller/weixin/weixin.php");

class ControllerWeixinMenuCreate extends ControllerWeixinWeixin {
	
	public function index() {
		
		if ($this->weixin_init() != true) {
			return; //首次验证或初始化失败
		}
				
		$wxtools = new WeixinTools();
		$menu_def = $wxtools->prepareMenu($this->config->get('weixin_menu'), $this->config->get('weixin_appid'));
		
		//创建菜单
		$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$this->access_token;
		$res = $wxtools->postToWx($url, $menu_def);
		if ($res == false) {
			$this->log->write("weixin prepay response error.");
			$this->response->setOutput("weixin prepay response error.");
		}
		else {
			$this->response->setOutput($res);
		}
	}
	
}
?>