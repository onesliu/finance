<?php 
class ControllerQingyouFood extends Controller { 
	private $error = array();
 
	public function index() {
		$this->language->load('qingyou/food');

		$this->document->setTitle($this->language->get('f_heading_title'));
		
		$this->load->model('qingyou/food');
		$this->load->model('qingyou/food_attr');
		
		$this->getList();
	}

	public function insert() {
		$this->language->load('qingyou/food');

		$this->document->setTitle($this->language->get('f_heading_title'));
		
		$this->load->model('qingyou/food');
		$this->load->model('qingyou/food_attr');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_qingyou_food->addFood($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
						
			$this->redirect($this->url->link('qingyou/food', 'token=' . $this->session->data['token'] . $url, 'SSL')); 
		}

		$this->getForm();
	}

	public function update() {
		$this->language->load('qingyou/food');

		$this->document->setTitle($this->language->get('f_heading_title'));
		
		$this->load->model('qingyou/food');
		$this->load->model('qingyou/food_attr');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_qingyou_food->editFood($this->request->get['id'], $this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');
			
			$url = '';

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
						
			$this->redirect($this->url->link('qingyou/food', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->language->load('qingyou/food');

		$this->document->setTitle($this->language->get('f_heading_title'));
		
		$this->load->model('qingyou/food');
		$this->load->model('qingyou/food_attr');
				
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $food_id) {
				$this->model_qingyou_food->deleteFood($food_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');
			
			$url = '';

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			
			$this->redirect($this->url->link('qingyou/food', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}
		
		$this->getList();
	}
	
	protected function getList() {
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		
		$url = '';
		
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
						
   		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('f_heading_title'),
			'href'      => $this->url->link('qingyou/food', 'token=' . $this->session->data['token'] . $url, 'SSL'),
      		'separator' => ' :: '
   		);
									
		$this->data['insert'] = $this->url->link('qingyou/food/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$this->data['delete'] = $this->url->link('qingyou/food/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');
		
		$this->data['allfood'] = array();
		
		$data = array(
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);
				
		$menu_total = $this->model_qingyou_food->getTotalFood();
		
		$results = $this->model_qingyou_food->getAllFood($data);
		
		foreach ($results as $result) {
			$action = array();
						
			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('qingyou/food/update', 'token=' . $this->session->data['token'] . '&id=' . $result['id'] . $url, 'SSL')
			);
			
			$mn = array();
			$menu_food = $this->model_qingyou_food->getFoodMenu($result['id']);
			foreach($menu_food as $food) {
				$mn[] = $food['name'];
			}
			$menu_names = implode("<br/>", $mn);

			$fs = array();
			$food_src = $this->model_qingyou_food->getFoodSources($result['id']);
			foreach($food_src as $s) {
				$src = (($s['source_type']==0)?'主料':'辅料')." ".$s['name'];
				if ($s['groupid'] > 0)
					$fs[] = sprintf('<strong>%s</strong>', $src);
				else
					$fs[] = $src;
			}
			$source_names = implode("<br/>", $fs);
			
			$this->data['allfood'][] = array(
				'id'		 => $result['id'],
				'name'        => $result['name'],
				'sort' 		 => $result['sort'],
				'disable'	 => $result['disable'],
				'menu_names' => $menu_names,
				'source_names'  => $source_names,
				'selected'    => isset($this->request->post['selected']) && in_array($result['id'], $this->request->post['selected']),
				'action'      => $action
			);
		}
		
		$this->data['heading_title'] = $this->language->get('f_heading_title');

		$this->data['text_no_results'] = $this->language->get('text_no_results');
		$this->data['text_enabled'] = $this->language->get('text_enabled');
    	$this->data['text_disabled'] = $this->language->get('text_disabled');
		
		$this->data['column_name'] = $this->language->get('f_column_name');
		$this->data['column_menus'] = $this->language->get('f_column_menus');
		$this->data['column_source'] = $this->language->get('f_entry_source_name');
		$this->data['column_sort_order'] = $this->language->get('f_column_sort_order');
		$this->data['column_action'] = $this->language->get('f_column_action');
		$this->data['column_disable'] = $this->language->get('f_column_disable');

		$this->data['button_insert'] = $this->language->get('button_insert');
		$this->data['button_delete'] = $this->language->get('button_delete');
 
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
		
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}
		
		$pagination = new Pagination();
		$pagination->total = $menu_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('qingyou/food', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');
			
		$this->data['pagination'] = $pagination->render();
		
		$this->template = 'qingyou/food_list.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	protected function getForm() {
		$this->data['heading_title'] = $this->language->get('f_heading_title');

		$this->data['text_none'] = $this->language->get('text_none');
		$this->data['text_default'] = $this->language->get('text_default');
		$this->data['text_image_manager'] = $this->language->get('text_image_manager');
		$this->data['text_browse'] = $this->language->get('text_browse');
		$this->data['text_clear'] = $this->language->get('text_clear');		
		$this->data['text_enabled'] = $this->language->get('text_enabled');
    	$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_percent'] = $this->language->get('text_percent');
		$this->data['text_amount'] = $this->language->get('text_amount');

		$this->data['entry_name'] = $this->language->get('f_entry_name');
		$this->data['entry_image'] = $this->language->get('f_entry_image');
		$this->data['entry_desp'] = $this->language->get('f_entry_desp');
		$this->data['entry_sort_order'] = $this->language->get('f_entry_sort_order');
		$this->data['entry_disable'] = $this->language->get('f_entry_disable');
		$this->data['entry_attr'] = $this->language->get('f_entry_attr');
		$this->data['entry_source_name'] = $this->language->get('f_entry_source_name');
		$this->data['entry_source_type'] = $this->language->get('f_entry_source_type');
		$this->data['text_main_source'] = $this->language->get('text_main_source');
		$this->data['text_other_source'] = $this->language->get('text_other_source');
		$this->data['entry_make_video'] = $this->language->get('f_entry_make_video');
		$this->data['entry_make_url'] = $this->language->get('f_entry_make_url');
		$this->data['entry_step_image'] = $this->language->get('f_entry_step_image');
		$this->data['entry_step_desp'] = $this->language->get('f_entry_step_desp');
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		$this->data['button_insert'] = $this->language->get('button_insert');
		$this->data['button_remove'] = $this->language->get('button_delete');
		
    	$this->data['tab_general'] = $this->language->get('tab_general');
		$this->data['tab_source'] = $this->language->get('tab_source');
		$this->data['tab_make'] = $this->language->get('tab_make');
    	
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
	
 		if (isset($this->error['name'])) {
			$this->data['error_name'] = $this->error['name'];
		} else {
			$this->data['error_name'] = array();
		}

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('f_heading_title'),
			'href'      => $this->url->link('qingyou/food', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		if (!isset($this->request->get['id'])) {
			$this->data['action'] = $this->url->link('qingyou/food/insert', 'token=' . $this->session->data['token'], 'SSL');
		} else {
			$this->data['action'] = $this->url->link('qingyou/food/update', 'token=' . $this->session->data['token'] . '&id=' . $this->request->get['id'], 'SSL');
		}
		
		$this->data['cancel'] = $this->url->link('qingyou/food', 'token=' . $this->session->data['token'], 'SSL');

		$this->load->model('tool/image');
		
		$this->data['allsources'] = $this->model_qingyou_food->getAllSources();
		$this->data['attrs'] = $this->model_qingyou_food_attr->getFoodAttrs(null);
    	$this->data['food_attrs'] = array();
    	$this->data['food_sources'] = array();
    	$this->data['food_source_ids'] = array();
    	$this->data['make_steps'] = array();
    	$this->data['food_source_ids'] = array();
    	
    	$food_info = null;
    	if (isset($this->request->get['id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
      		$food_info = $this->model_qingyou_food->getFood($this->request->get['id']);
			$food_attrs = $this->model_qingyou_food->getFoodAttrs($this->request->get['id']);
			foreach($food_attrs as $attr_class) {
				foreach($attr_class as $attr) {
					$this->data['food_attrs'][] = $attr['id'];
				}
			}
			
			$this->data['food_sources'] = $this->model_qingyou_food->getFoodSources($this->request->get['id']);
    		foreach($this->data['food_sources'] as $s) {
				$this->data['food_source_ids'][] = $s['product_id'];
			}
			
			$this->data['make_steps'] = $this->model_qingyou_food->getMakeSteps($this->request->get['id']);
			foreach($this->data['make_steps'] as &$step) {
				$step['thumb'] = $this->makeThumb($step['image']);
			}
    	}
    	
		$this->data['token'] = $this->session->data['token'];
		
		$this->setMenuField($food_info, 'name');
		$this->setMenuField($food_info, 'desp');
		$this->setMenuField($food_info, 'sort', 0);
		$this->setMenuField($food_info, 'disable', 1);
		$this->setMenuField($food_info, 'image1');
		$this->setMenuField($food_info, 'image2');
		$this->setMenuField($food_info, 'image3');
		$this->setMenuField($food_info, 'make_video');
		$this->setMenuField($food_info, 'make_url');
		
		$this->setThumb($food_info, 1);
		$this->setThumb($food_info, 2);
		$this->setThumb($food_info, 3);
		
		$this->data['no_image'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);
		
		$this->template = 'qingyou/food_form.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	private function setMenuField($food_info, $field, $default = '') {
		if (isset($this->request->post[$field])) {
			$this->data[$field] = $this->request->post[$field];
		} elseif (!empty($food_info)) {
			$this->data[$field] = $food_info[$field];
		} else {
			$this->data[$field] = $default;
		}
	}
	
	private function setThumb($food_info, $i) {
		if (isset($this->request->post["image$i"]) && file_exists(DIR_IMAGE . $this->request->post["image$i"])) {
			$this->data["thumb$i"] = $this->model_tool_image->resize($this->request->post["image$i"], 100, 100);
		} elseif (!empty($food_info) && $food_info["image$i"] && file_exists(DIR_IMAGE . $food_info["image$i"])) {
			$this->data["thumb$i"] = $this->model_tool_image->resize($food_info["image$i"], 100, 100);
		} else {
			$this->data["thumb$i"] = $this->model_tool_image->resize('no_image.jpg', 100, 100);
		}
	}
	
	private function makeThumb($image) {
		if ($image && file_exists(DIR_IMAGE . $image))
			return $this->model_tool_image->resize($image, 100, 100);
		else
			return $this->model_tool_image->resize('no_image.jpg', 100, 100);
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'qingyou/food')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['name']) < 2) || (utf8_strlen($this->request->post['name']) > 255)) {
			$this->error['name'] = $this->language->get('error_name');
		}
		
		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}
					
		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
	
	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'qingyou/food')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
 
		if (!$this->error) {
			return true; 
		} else {
			return false;
		}
	}
	
	protected function validateRepair() {
		if (!$this->user->hasPermission('modify', 'qingyou/food')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
 
		if (!$this->error) {
			return true; 
		} else {
			return false;
		}
	}

}
?>