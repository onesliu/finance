<?php
class ControllerModuleMobileStore extends Controller {
	private $error = array(); 
	
	public function install() {
		$this->load->model('mobile_store/mobile_store');
		$this->model_mobile_store_mobile_store->createMobileStoreLayout();	
	}
	
	public function index() {   
		$this->load->language('module/mobile_store');
		$this->load->model('mobile_store/mobile_store');

		$this->document->setTitle($this->language->get('heading_title'));
		$this->document->addStyle('view/stylesheet/mobile_store.css');
		
		$this->load->model('setting/setting');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('mobile_store', $this->request->post);		
			
			$this->session->data['success'] = $this->language->get('text_success');
			$this->cache->delete('product');
						
			$this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}
				
		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_yes'] = $this->language->get('text_yes');
		$this->data['text_no'] = $this->language->get('text_no');
		$this->data['text_browse'] = $this->language->get('text_browse');
		$this->data['text_clear'] = $this->language->get('text_clear');
		$this->data['text_image_manager'] = $this->language->get('text_image_manager');
		$this->data['text_list'] = $this->language->get('text_list');
		$this->data['text_grid'] = $this->language->get('text_grid');
		
		$this->data['entry_logo'] = $this->language->get('entry_logo');
		$this->data['entry_charset'] = $this->language->get('entry_charset');
		$this->data['entry_choose_products'] = $this->language->get('entry_choose_products');
		$this->data['entry_selected_products'] = $this->language->get('entry_selected_products');
		$this->data['entry_available_products'] = $this->language->get('entry_available_products');
		$this->data['entry_image'] = $this->language->get('entry_image');
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		if (isset($this->error['image'])) {
			$this->data['error_image'] = $this->error['image'];
		} else {
			$this->data['error_image'] = array();
		}
		
  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/mobile_store', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$this->data['action'] = $this->url->link('module/mobile_store', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['mobile_store_logo'])) {
			$this->data['mobile_store_logo'] = $this->request->post['mobile_store_logo'];
		} elseif ($this->config->get('mobile_store_logo')) { 
			$this->data['mobile_store_logo'] = (string)$this->config->get('mobile_store_logo');
		} else {
			$this->data['mobile_store_logo'] = '';
		}
		
		$this->load->model('tool/image');
		
		if ( $this->data['mobile_store_logo'] ){
			$this->data['thumb'] = $this->model_tool_image->resize($this->data['mobile_store_logo'], 30, 30);
		} else {
		    $this->data['thumb'] = $this->model_tool_image->resize('no_image.jpg', 30, 30);
		}	
		
		$this->data['no_image'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);
		
		if (isset($this->request->post['mobile_store_selected_products'])) {
			$selected_products_ids = $this->request->post['mobile_store_selected_products'];
		} elseif ($this->config->get('mobile_store_selected_products')) { 
			$selected_products_ids = $this->config->get('mobile_store_selected_products');
		} else {
			$selected_products_ids = 0;
		}
		
		$this->data['mobile_store_available_products'] = array();
		
		$data = array( 
			'type'                  => 'available',
			'selected_products_ids' => $selected_products_ids,
			'sort'                  => 'pd.name'
		);
		
		$results = $this->model_mobile_store_mobile_store->getProducts($data);
		
		foreach($results as $result){
			if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], 30, 30);
				} else {
					$image = false;
				}
			
				$this->data['mobile_store_available_products'][] = array(
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'model'       => $result['model'],
					'description' => substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, 250) . '..',
					'price'       => $result['price'],
					'href'        => $this->url->link('product/product', '&product_id=' . $result['product_id'])
				);
		}
		
		
		$this->data['mobile_store_selected_products'] = array();
		
		$data = array( 
			'type'                  => 'selected',
			'selected_products_ids' => $selected_products_ids,
			'sort'                  => 'pd.name'
		);
		
		if ($selected_products_ids != 0 ){
		
			$results = $this->model_mobile_store_mobile_store->getProducts($data);
			
			foreach($results as $result){
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], 30, 30);
				} else {
					$image = false;
				}
			
				$this->data['mobile_store_selected_products'][] = array(
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'model'       => $result['model'],
					'description' => substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, 250) . '..',
					'price'       => $result['price'],
					'href'        => $this->url->link('product/product', '&product_id=' . $result['product_id'])
				);
			}
		}
		
		$this->data['mobile_store_selected_products_ids'] = $selected_products_ids;
		
		
		if (isset($this->request->post['mobile_store_image_width'])) {
			$this->data['mobile_store_image_width'] = $this->request->post['mobile_store_image_width'];
		} elseif ($this->config->get('mobile_store_image_width')) { 
			$this->data['mobile_store_image_width'] = $this->config->get('mobile_store_image_width');
		} else {
			$this->data['mobile_store_image_width'] = '';
		}
		
		if (isset($this->request->post['mobile_store_image_height'])) {
			$this->data['mobile_store_image_height'] = $this->request->post['mobile_store_image_height'];
		} elseif ($this->config->get('mobile_store_image_height')) { 
			$this->data['mobile_store_image_height'] = $this->config->get('mobile_store_image_height');
		} else {
			$this->data['mobile_store_image_height'] = '';
		}
		
		if (isset($this->request->post['mobile_store_charset'])) {
			$this->data['mobile_store_charset'] = $this->request->post['mobile_store_charset'];
		} elseif ($this->config->get('mobile_store_charset')) { 
			$this->data['mobile_store_charset'] = $this->config->get('mobile_store_charset');
		} else {
			$this->data['mobile_store_charset'] = 0;
		}	
		
		$this->data['token'] = $this->session->data['token'];
		
		$this->template = 'module/mobile_store.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/mobile_store')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if ( utf8_strlen($this->request->post['mobile_store_image_width']) < 1 || utf8_strlen($this->request->post['mobile_store_image_height']) < 1  ){
			$this->error['image'] = $this->language->get('error_image');
		}
				
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}
?>