<?php

class ControllerWeixinPrepay extends Controller { 
	public function index() {
		
		// view template
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/weixin/prepay.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/weixin/prepay.tpl';
		} else {
			$this->template = 'default/template/weixin/prepay.tpl';
		}
		
		$this->render();
	}
	
}

?>