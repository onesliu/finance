<?php 
class ControllerQingyouMenu extends Controller { 
	private $error = array();
 
	public function index() {
		$this->language->load('qingyou/food');

		$this->document->setTitle($this->language->get('m_heading_title'));
		
		$this->load->model('qingyou/menu');
		$this->load->model('qingyou/menu_group');
		$this->load->model('qingyou/food');
		 
		$this->getList();
	}

	public function insert() {
		$this->language->load('qingyou/food');

		$this->document->setTitle($this->language->get('m_heading_title'));
		
		$this->load->model('qingyou/menu');
		$this->load->model('qingyou/menu_group');
		$this->load->model('qingyou/food');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_qingyou_menu->addMenu($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
						
			$this->redirect($this->url->link('qingyou/menu', 'token=' . $this->session->data['token'] . $url, 'SSL')); 
		}

		$this->getForm();
	}

	public function update() {
		$this->language->load('qingyou/food');

		$this->document->setTitle($this->language->get('m_heading_title'));
		
		$this->load->model('qingyou/menu');
		$this->load->model('qingyou/menu_group');
		$this->load->model('qingyou/food');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_qingyou_menu->editMenu($this->request->get['id'], $this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');
			
			$url = '';

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
						
			$this->redirect($this->url->link('qingyou/menu', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->language->load('qingyou/food');

		$this->document->setTitle($this->language->get('m_heading_title'));
		
		$this->load->model('qingyou/menu');
		$this->load->model('qingyou/menu_group');
		$this->load->model('qingyou/food');
				
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $group_id) {
				$this->model_qingyou_menu->deleteMenu($group_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');
			
			$url = '';

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			
			$this->redirect($this->url->link('qingyou/menu', 'token=' . $this->session->data['token'] . $url, 'SSL'));
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
       		'text'      => $this->language->get('m_heading_title'),
			'href'      => $this->url->link('qingyou/menu', 'token=' . $this->session->data['token'] . $url, 'SSL'),
      		'separator' => ' :: '
   		);
									
		$this->data['insert'] = $this->url->link('qingyou/menu/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$this->data['delete'] = $this->url->link('qingyou/menu/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');
		
		$this->data['menus'] = array();
		
		$data = array(
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);
				
		$menu_total = $this->model_qingyou_menu->getTotalMenus();
		
		$results = $this->model_qingyou_menu->getMenus($data);
		
		foreach ($results as $result) {
			$action = array();
						
			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('qingyou/menu/update', 'token=' . $this->session->data['token'] . '&id=' . $result['id'] . $url, 'SSL')
			);
			
			$gn = array();
			$groups = $this->model_qingyou_menu->getMenuGroups($result['id']);
			foreach($groups as $group) {
				$gn[] = $group['name'];
			}
			$group_names = implode("<br/>", $gn);
			
			$mn = array();
			$menu_food = $this->model_qingyou_food->getMenuFood($result['id']);
			foreach($menu_food as $food) {
				$mn[] = $food['name'];
			}
			$food_names = implode("<br/>", $mn);

			$this->data['menus'][] = array(
				'id'		 => $result['id'],
				'name'        => $result['name'],
				'sort' 		 => $result['sort'],
				'disable'	 => $result['disable'],
				'group_names' => $group_names,
				'food_names' => $food_names,
				'selected'    => isset($this->request->post['selected']) && in_array($result['id'], $this->request->post['selected']),
				'action'      => $action
			);
		}
		
		$this->data['heading_title'] = $this->language->get('m_heading_title');

		$this->data['text_no_results'] = $this->language->get('text_no_results');
		$this->data['text_enabled'] = $this->language->get('text_enabled');
    	$this->data['text_disabled'] = $this->language->get('text_disabled');
		
		$this->data['column_name'] = $this->language->get('m_column_name');
		$this->data['column_groups'] = $this->language->get('m_column_group');
		$this->data['column_food'] = $this->language->get('m_column_food');
		$this->data['column_sort_order'] = $this->language->get('m_column_sort_order');
		$this->data['column_action'] = $this->language->get('m_column_action');
		$this->data['column_disable'] = $this->language->get('m_column_disable');

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
		$pagination->url = $this->url->link('qingyou/menu', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');
			
		$this->data['pagination'] = $pagination->render();
		
		$this->template = 'qingyou/menu_list.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	protected function getForm() {
		$this->data['heading_title'] = $this->language->get('m_heading_title');

		$this->data['text_none'] = $this->language->get('text_none');
		$this->data['text_default'] = $this->language->get('text_default');
		$this->data['text_image_manager'] = $this->language->get('text_image_manager');
		$this->data['text_browse'] = $this->language->get('text_browse');
		$this->data['text_clear'] = $this->language->get('text_clear');		
		$this->data['text_enabled'] = $this->language->get('text_enabled');
    	$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_percent'] = $this->language->get('text_percent');
		$this->data['text_amount'] = $this->language->get('text_amount');
				
		$this->data['entry_name'] = $this->language->get('m_entry_name');
		$this->data['entry_image'] = $this->language->get('m_entry_image');
		$this->data['entry_desp'] = $this->language->get('m_entry_desp');
		$this->data['entry_sort_order'] = $this->language->get('m_entry_sort_order');
		$this->data['entry_disable'] = $this->language->get('m_entry_disable');
		$this->data['entry_group'] = $this->language->get('m_entry_group');
		$this->data['entry_food'] = $this->language->get('m_entry_food');
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

    	$this->data['tab_general'] = $this->language->get('tab_general');
		
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
       		'text'      => $this->language->get('m_heading_title'),
			'href'      => $this->url->link('qingyou/menu', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		if (!isset($this->request->get['id'])) {
			$this->data['action'] = $this->url->link('qingyou/menu/insert', 'token=' . $this->session->data['token'], 'SSL');
		} else {
			$this->data['action'] = $this->url->link('qingyou/menu/update', 'token=' . $this->session->data['token'] . '&id=' . $this->request->get['id'], 'SSL');
		}
		
		$this->data['cancel'] = $this->url->link('qingyou/menu', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['groups'] = array();
    	$this->data['allgroups'] = $this->model_qingyou_menu_group->getGroups(null);
		$this->data['menu_food'] = array();
    	$this->data['allfood'] = $this->model_qingyou_food->getAllFood(null);
    	
    	$menu_info = null;
    	if (isset($this->request->get['id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
      		$menu_info = $this->model_qingyou_menu->getMenu($this->request->get['id']);
			$menu_groups = $this->model_qingyou_menu->getMenuGroups($this->request->get['id']);
			foreach($menu_groups as $group) {
				$this->data['groups'][] = $group['id'];
			}
			
			$menu_food = $this->model_qingyou_food->getMenuFood($this->request->get['id']);
			foreach($menu_food as $food) {
				$this->data['menu_food'][] = $food['id'];
			}
    	}
    	
		$this->data['token'] = $this->session->data['token'];
		
		$this->setMenuField($menu_info, 'name');
		$this->setMenuField($menu_info, 'desp');
		$this->setMenuField($menu_info, 'sort', 0);
		$this->setMenuField($menu_info, 'disable', 1);
		$this->setMenuField($menu_info, 'image1');
		$this->setMenuField($menu_info, 'image2');
		$this->setMenuField($menu_info, 'image3');
		
		$this->load->model('tool/image');
		$this->setThumb($menu_info, 1);
		$this->setThumb($menu_info, 2);
		$this->setThumb($menu_info, 3);
		
		$this->data['no_image'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);
		
		$this->template = 'qingyou/menu_form.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	private function setMenuField($menu_info, $field, $default = '') {
		if (isset($this->request->post[$field])) {
			$this->data[$field] = $this->request->post[$field];
		} elseif (!empty($menu_info)) {
			$this->data[$field] = $menu_info[$field];
		} else {
			$this->data[$field] = $default;
		}
	}
	
	private function setThumb($menu_info, $i) {
		if (isset($this->request->post["image$i"]) && file_exists(DIR_IMAGE . $this->request->post["image$i"])) {
			$this->data["thumb$i"] = $this->model_tool_image->resize($this->request->post["image$i"], 100, 100);
		} elseif (!empty($menu_info) && $menu_info["image$i"] && file_exists(DIR_IMAGE . $menu_info["image$i"])) {
			$this->data["thumb$i"] = $this->model_tool_image->resize($menu_info["image$i"], 100, 100);
		} else {
			$this->data["thumb$i"] = $this->model_tool_image->resize('no_image.jpg', 100, 100);
		}
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'qingyou/menu')) {
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
		if (!$this->user->hasPermission('modify', 'qingyou/menu')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
 
		if (!$this->error) {
			return true; 
		} else {
			return false;
		}
	}
	
	protected function validateRepair() {
		if (!$this->user->hasPermission('modify', 'qingyou/menu')) {
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