<?php
class ControllerModuleMSDnews extends Controller {
	protected function index($setting) {
		static $module = 0; 
		
		$this->document->addScript('catalog/view/javascript/jquery/jquery.dnews.js');
		
		$this->data['limit']        = $setting['limit'];
		$this->data['feed_url']     = $setting['feed_url'];
	
		$this->data['module'] = $module++;
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/ms_dnews.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/module/ms_dnews.tpl';
		} else {
			$this->template = 'default/template/module/ms_dnews.tpl';
		}

		$this->render();
	}
}
?>