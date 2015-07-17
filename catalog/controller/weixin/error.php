<?php
include_once(DIR_APPLICATION."controller/weixin/pay_result.php");

class ControllerWeixinError extends Controller {
	public function index() {
    	
		$this->data['error_msg'] = $this->session->data['error_msg'];
		$this->data['continue'] = $this->session->data['url_continue'];
		$this->data['text_continue'] = $this->session->data['text_continue'];
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/weixin/error.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/weixin/error.tpl';
		} else {
            $this->template = 'default/template/weixin/error.tpl';
        }
		
		$this->children = array(
			'mobile_store/titlebar',
			'mobile_store/navi',
			'mobile_store/header',
		);

		$this->response->setOutput($this->render());
	}
}
?>