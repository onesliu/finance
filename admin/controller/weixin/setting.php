<?php
class ControllerWeixinSetting extends Controller {
	private $error = array();
 
	public function index() {
		$this->language->load('weixin/setting'); 

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			
			if (isset($this->request->post['weixin_menu'])) {
				$menustr = htmlspecialchars_decode($this->request->post['weixin_menu']);
				$this->request->post['weixin_menu'] = $menustr;
			}
			
			$this->model_setting_setting->editSetting('weixin', $this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('weixin/setting', 'token=' . $this->session->data['token'], 'SSL'));
		}
		
		$data = $this->model_setting_setting->getSetting('weixin');
		
		$this->data['heading_title'] = $this->language->get('heading_title');
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		$this->data['config_token'] = $this->language->get('weixin_token');
		$this->data['config_appid'] = $this->language->get('weixin_appid');
		$this->data['config_appsecret'] = $this->language->get('weixin_appsecret');
		$this->data['config_menu'] = $this->language->get('weixin_menu');
		
		$this->data['weixin_token'] = (isset($data['weixin_token']))?$data['weixin_token']:'';
		$this->data['weixin_appid'] = (isset($data['weixin_appid']))?$data['weixin_appid']:'';
		$this->data['weixin_appsecret'] = (isset($data['weixin_appsecret']))?$data['weixin_appsecret']:'';
		$this->data['weixin_menu'] = (isset($data['weixin_menu']))?$data['weixin_menu']:'';
		
		$this->data['weixin_paysignkey'] = (isset($data['weixin_paysignkey']))?$data['weixin_paysignkey']:'';
		$this->data['weixin_partnerid'] = (isset($data['weixin_partnerid']))?$data['weixin_partnerid']:'';
		$this->data['weixin_partnerkey'] = (isset($data['weixin_partnerkey']))?$data['weixin_partnerkey']:'';
		
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		if (isset($this->error['token'])) {
			$this->data['error_token'] = $this->error['token'];
		} else {
			$this->data['error_token'] = '';
		}
		
 		if (isset($this->error['appid'])) {
			$this->data['error_appid'] = $this->error['appid'];
		} else {
			$this->data['error_appid'] = '';
		}

 		if (isset($this->error['appsecret'])) {
			$this->data['error_appsecret'] = $this->error['appsecret'];
		} else {
			$this->data['error_appsecret'] = '';
		}
		
		$this->data['error_paysignkey'] = '';
		$this->data['error_partnerid'] = '';
		$this->data['error_partnerkey'] = '';

		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('weixin/setting', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
		
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		$this->data['action'] = $this->url->link('weixin/setting', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('weixin/setting', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['token'] = $this->session->data['token'];
		
		$this->template = 'weixin/setting.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
		
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'weixin/setting')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['weixin_token']) {
			$this->error['token'] = $this->language->get('error_token');
		}

		if (!$this->request->post['weixin_appid']) {
			$this->error['appid'] = $this->language->get('error_appid');
		}

		if (!$this->request->post['weixin_appsecret']) {
			$this->error['appsecret'] = $this->language->get('error_appsecret');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
	
}
?>