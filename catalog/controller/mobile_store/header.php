<?php   
class ControllerMobileStoreHeader extends Controller {
	protected function index() {
		$this->data['title'] = $this->document->getTitle();
		
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}
		
		$showhelp = false;
		$interval_day = 5;
		if ($this->config->get('help_interval_day') != null)
			$interval_day = $this->config->get('help_interval_day');
			
		if ($this->customer->isLogged()) {
			$lastlogin = strtotime($this->customer->lastlogin);
			$showhelp = ((time() - $lastlogin) >= (86400*$interval_day)); //默认连续5天没用本系统显示帮助
		}
		$this->data['showhelp'] = (int)$showhelp;
		
		$this->data['base'] = $server;
		$this->data['description'] = $this->document->getDescription();
		$this->data['keywords'] = $this->document->getKeywords();
		$links = $this->document->getLinks();
		
		$this->data['links'] = array();
		
		foreach($links as $link){
			if (($link['rel'] == "canonical") && ( strpos($link['href'], 'product/product') !== false) ){
				$link['href'] = str_replace("product/product", "mobile_store/product", $link['href']);
			}
			$this->data['links'][] = $link;
		}
		
		$this->data['styles'] = $this->document->getStyles();
		$this->data['scripts'] = $this->document->getScripts();
		$this->data['lang'] = $this->language->get('code');
		$this->data['direction'] = $this->language->get('direction');
		$this->data['google_analytics'] = html_entity_decode($this->config->get('config_google_analytics'), ENT_QUOTES, 'UTF-8');
		$this->data['mobile_store_charset'] = $this->config->get('mobile_store_charset');
		
		$this->language->load('mobile_store/header');		
				
		if ($this->config->get('config_icon') && file_exists(DIR_IMAGE . $this->config->get('config_icon'))) {
			$this->data['icon'] = $server . 'image/' . $this->config->get('config_icon');
		} else {
			$this->data['icon'] = '';
		}
		
		$this->data['name'] = $this->config->get('config_name');
				
		if ($this->config->get('mobile_store_logo') && file_exists(DIR_IMAGE . $this->config->get('mobile_store_logo'))) {
			$this->data['logo'] = $server . 'image/' . $this->config->get('mobile_store_logo');
		} else {
			$this->data['logo'] = '';
		}
		
		
		// Calculate Totals
		$total_data = array();					
		$total = 0;
		$taxes = $this->cart->getTaxes();
		
		if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {						 
			$this->load->model('setting/extension');
			
			$sort_order = array(); 
			
			$results = $this->model_setting_extension->getExtensions('total');
			
			foreach ($results as $key => $value) {
				$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
			}
			
			array_multisort($sort_order, SORT_ASC, $results);
			
			foreach ($results as $result) {
				if ($this->config->get($result['code'] . '_status')) {
					$this->load->model('total/' . $result['code']);
		
					$this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
				}
			}
		}
		
		$this->data['text_home'] = $this->language->get('text_home');
		$this->data['text_login'] = $this->language->get('text_login');
		$this->data['text_account'] = $this->language->get('text_account');
		$this->data['text_categories'] = $this->language->get('text_categories');
		$this->data['text_items'] = $this->currency->format($total);
    	$this->data['text_search'] = $this->language->get('text_search');
    	$this->data['text_order'] = $this->language->get('text_order');
		
    	$this->data['text_checkout'] = $this->language->get('text_checkout');
		$this->data['text_all_products'] = $this->language->get('text_all_products');

				
		$this->data['home'] = $this->url->link('mobile_store/home');
		$this->data['login'] = $this->url->link('mobile_store/login');
		$this->data['logged'] = $this->customer->isLogged();
		$this->data['account'] = $this->url->link('mobile_store/account', '', 'SSL');
		$this->data['category_list'] = $this->url->link('mobile_store/category_list', '', 'SSL');
		$this->data['cart'] = $this->url->link('mobile_store/cart', '', 'wxpay');
		$this->data['checkout'] = $this->url->link('mobile_store/checkout', '', 'SSL');
		$this->data['order'] = $this->url->link('mobile_store/order', '', 'SSL');
		
		if (isset($this->request->get['filter_name'])) {
			$this->data['filter_name'] = $this->request->get['filter_name'];
		} else {
			$this->data['filter_name'] = '';
		}
		
		$this->data['action'] = $this->url->link('mobile_store/home');

		if (!isset($this->request->get['route'])) {
			$this->data['redirect'] = $this->url->link('mobile_store/home');
		} else {
			$data = $this->request->get;
			
			unset($data['_route_']);
			
			$route = $data['route'];
			
			unset($data['route']);
			
			$url = '';
			
			if ($data) {
				$url = '&' . urldecode(http_build_query($data, '', '&'));
			}			
			
			$this->data['redirect'] = $this->url->link($route, $url);
		}

    	if (($this->request->server['REQUEST_METHOD'] == 'POST') && isset($this->request->post['language_code'])) {
			$this->session->data['language'] = $this->request->post['language_code'];
		
			if (isset($this->request->post['redirect'])) {
				$this->redirect($this->request->post['redirect']);
			} else {
				$this->redirect($this->url->link('mobile_store/home'));
			}
    	}		
						
		$this->data['language_code'] = $this->session->data['language'];
		
		$this->load->model('localisation/language');
		
		$this->data['languages'] = array();
		
		$results = $this->model_localisation_language->getLanguages();
		
		foreach ($results as $result) {
			if ($result['status']) {
				$this->data['languages'][] = array(
					'name'  => $result['name'],
					'code'  => $result['code'],
					'image' => $result['image']
				);	
			}
		}

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && isset($this->request->post['currency_code'])) {
      		$this->currency->set($this->request->post['currency_code']);
			
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['shipping_method']);
				
			if (isset($this->request->post['redirect'])) {
				$this->redirect($this->request->post['redirect']);
			} else {
				$this->redirect($this->url->link('common/home'));
			}
   		}
						
		$this->data['currency_code'] = $this->currency->getCode(); 
		
		$this->load->model('localisation/currency');
		 
		 $this->data['currencies'] = array();
		 
		$results = $this->model_localisation_currency->getCurrencies();	
		
		foreach ($results as $result) {
			if ($result['status']) {
   				$this->data['currencies'][] = array(
					'title'        => $result['title'],
					'code'         => $result['code'],
					'symbol_left'  => $result['symbol_left'],
					'symbol_right' => $result['symbol_right']				
				);
			}
		}
		
		if (isset($this->request->get['filter_category_id'])) {
			$filter_category_id = $this->request->get['filter_category_id'];
		} else {
			$filter_category_id = 0;
		} 
		
		$this->data['filter_category_id'] = $filter_category_id; 
		
		// Menu
		$this->load->model('catalog/category');
		$this->load->model('catalog/product');
		
		// 3 Level Category Search
		$this->data['categories'] = array();
					
		$categories_1 = $this->model_catalog_category->getCategories(0);
		
		foreach ($categories_1 as $category_1) {
			$level_2_data = array();
			
			$categories_2 = $this->model_catalog_category->getCategories($category_1['category_id']);
			
			foreach ($categories_2 as $category_2) {
				$level_3_data = array();
				
				$categories_3 = $this->model_catalog_category->getCategories($category_2['category_id']);
				
				foreach ($categories_3 as $category_3) {
					$level_3_data[] = array(
						'category_id' => $category_3['category_id'],
						'name'        => $category_3['name'],
					);
				}
				
				$level_2_data[] = array(
					'category_id' => $category_2['category_id'],	
					'name'        => $category_2['name'],
					'children'    => $level_3_data
				);					
			}
			
			$this->data['categories'][] = array(
				'category_id' => $category_1['category_id'],
				'name'        => $category_1['name'],
				'children'    => $level_2_data
			);
		}
		
		$this->data['theme_name'] = $this->config->get('config_template');
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/mobile_store/header.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/mobile_store/header.tpl';
		} else {
			$this->template = 'default/template/mobile_store/header.tpl';
		}
		
		$this->data['theme_img_dir'] = $server . 'catalog/view/theme/' . $this->config->get('config_template') . '/image/';
		
    	$this->render();
	} 	
}
?>