<?php
class ControllerRestHeader extends Controller {
	protected function index() {
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		$this->data['base'] = $server;
		$this->data['title'] = "寻钱宝";
		
		$this->template = 'default/template/rest/header.tpl';
    	$this->render();
	}
}
?>