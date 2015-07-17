<?php

class ControllerWeixinClosed extends Controller {
	public function index() {
    	
		$this->data['error_msg'] = '菜鸽子商城维护中，暂时停止服务，预计开放时间今晚22:00，对此带来的不便敬请谅解。';
		
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$dir_img = $this->config->get('config_ssl') . 'image/';
		} else {
			$dir_img = $this->config->get('config_url') . 'image/';
		}
		$this->data['logo'] = $dir_img . 'logo.png';
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/weixin/error.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/weixin/error.tpl';
		} else {
            $this->template = 'default/template/weixin/error.tpl';
        }
		
		$this->children = array(
			'mobile_store/header',
		);

		$this->response->setOutput($this->render());
	}
}
?>