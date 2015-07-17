<?php 
class ControllerMobileStoreCategory extends Controller {  
	public function index() { 
		$this->language->load('mobile_store/category');
		
		$this->load->model('catalog/category');
		
		$this->load->model('catalog/product');
		$this->load->model('mobile_store/product');
		
		$this->load->model('tool/image');
		
		$this->request->get['back'] = true;
		$this->request->get['product_page'] = true;
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		$limit = 20;

		if (isset($this->request->get['fspath'])) {
			$path = '';
		
			$parts = explode('_', (string)$this->request->get['fspath']);
		
			foreach ($parts as $path_id) {
				if (!$path) {
					$path = $path_id;
				} else {
					$path .= '_' . $path_id;
				}

				$category_info = $this->model_catalog_category->getCategory($path_id);
				
				if ($category_info) {
	       			$this->data['breadcrumbs'][] = array(
   	    				'text'      => $category_info['name'],
						'href'      => $this->url->link('mobile_store/category', 'fspath=' . $path),
        				'separator' => $this->language->get('text_separator')
        			);
				}
			}
		
			$category_id = array_pop($parts);
		} else {
			$category_id = 0;
		}
		
		$category_info = $this->model_catalog_category->getCategory($category_id);
	
		if ($category_info) {
	  		$this->document->setTitle($category_info['name']);
			$this->document->setDescription($category_info['meta_description']);
			$this->document->setKeywords($category_info['meta_keyword']);
			
			$this->data['heading_title'] = $category_info['name'];
			
			$this->data['text_refine'] = $this->language->get('text_refine');
			$this->data['text_select'] = $this->language->get('text_select');
			$this->data['text_empty'] = $this->language->get('text_empty');			
			$this->data['text_quantity'] = $this->language->get('text_quantity');
			$this->data['text_manufacturer'] = $this->language->get('text_manufacturer');
			$this->data['text_model'] = $this->language->get('text_model');
			$this->data['text_price'] = $this->language->get('text_price');
			$this->data['text_tax'] = $this->language->get('text_tax');
			$this->data['text_points'] = $this->language->get('text_points');
			$this->data['text_compare'] = sprintf($this->language->get('text_compare'), (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));
			$this->data['text_display'] = $this->language->get('text_display');
			$this->data['text_list'] = $this->language->get('text_list');
			$this->data['text_grid'] = $this->language->get('text_grid');
			$this->data['text_sort'] = $this->language->get('text_sort');
			$this->data['text_limit'] = $this->language->get('text_limit');
					
			$this->data['button_cart'] = $this->language->get('button_cart');
			$this->data['button_wishlist'] = $this->language->get('button_wishlist');
			$this->data['button_compare'] = $this->language->get('button_compare');
			$this->data['button_continue'] = $this->language->get('button_continue');
			
			if ($category_info['image']) {
				$this->data['thumb'] = $this->model_tool_image->resize($category_info['image'], $this->config->get('config_image_category_width'), $this->config->get('config_image_category_height'));
			} else {
				$this->data['thumb'] = '';
			}
			
			$this->data['description'] = html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8');
			$this->data['compare'] = $this->url->link('mobile_store/compare');
			
			$this->data['categories'] = array();
			
			$results = $this->model_catalog_category->getCategories($category_id);
			
			foreach ($results as $result) {
				$data = array(
					'filter_category_id'  => $result['category_id'],
					'filter_sub_category' => true	
				);
				
				$product_total = $this->model_catalog_product->getTotalProducts($data);
				
				$this->data['categories'][] = array(
					'name'  => $result['name'] . ' (' . $product_total . ')',
					'href'  => $this->url->link('mobile_store/category', 'fspath=' . $this->request->get['fspath'] . '_' . $result['category_id'])
				);
			}
			
			$this->data['products'] = array();
			
			$data = array(
				'filter_category_id' => $category_id, 
				'filter_sub_category'=> true, 
				'start'              => ($page - 1) * $limit,
				'limit'              => $limit,
			);
			
			$product_total = $this->model_mobile_store_product->getTotalProducts($data); 
			
			$results = $this->model_mobile_store_product->getProducts($data);
			
			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'));
				} else {
					$image = $this->model_tool_image->resize('no_image.jpg', $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'));
				}
				
				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')));
				} else {
					$price = false;
				}
				
				if ((float)$result['special']) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')));
				} else {
					$special = false;
				}	
				
				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price']);
				} else {
					$tax = false;
				}
				
				if ($this->config->get('config_review_status')) {
					$rating = (int)$result['rating'];
				} else {
					$rating = false;
				}
				
				if ($result['product_type'] == 0 || $result['product_type'] == 2) {
					$scalable = '付款后称重';
				}
				else {
					$scalable = '称重后付款';
				}
								
				$this->data['products'][] = array(
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'model'		  => $result['model'],
					'name'        => $result['name'],
					'description' => mb_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, 100) . '..',
					'price'       => $price,
					'unit'		  => $result['sku'], //库存单位
					'sellunit'	  => $result['upc'], //销售单位
					'sellprice'	  => $this->currency->format($result['mpn']), //销售单位价格
					'product_type' => $result['product_type'],
					'special'     => $special,
					'tax'         => $tax,
					'rating'      => $result['rating'],
					'weight_show' => ((int)$result['weight']) . $result['weight_class'],
					'scalable'	  => $scalable,
					'reviews'     => sprintf($this->language->get('text_reviews'), (int)$result['reviews']),
					'href'        => $this->url->link('mobile_store/product', 'fspath=' . $this->request->get['fspath'] . '&product_id=' . $result['product_id'])
				);
			}
			
			$pagination = new Pagination();
			$pagination->total = $product_total;
			$pagination->page = $page;
			$pagination->limit = $limit;
			$pagination->num_pages = ceil($pagination->total / $pagination->limit);
			$pagination->text = $this->language->get('text_pagination');
			$pagination->url = $this->url->link2('mobile_store/category', 'fspath=' . $this->request->get['fspath'], 'SSL');
			$this->data['pagination'] = $pagination;
			
			$this->data['searchurl'] = $this->url->link('mobile_store/allproduct');
			
			if ($page <= 1) {
				$cfile = 'category.tpl';
			}
			else {
				$cfile = 'category_more.tpl';
			}
				
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . "/template/mobile_store/$cfile")) {
				$this->template = $this->config->get('config_template') . "/template/mobile_store/$cfile";
			} else {
				$this->template = "default/template/mobile_store/$cfile";
			}
			
			if ($page <= 1) {
				$this->children = array(
					'mobile_store/navi',
					'mobile_store/titlebar',
					'mobile_store/header'
				);
			}
				
			$this->response->setOutput($this->render());										
    	} else {
			$this->document->setTitle($this->language->get('text_error'));

      		$this->data['heading_title'] = $this->language->get('text_error');

      		$this->data['text_error'] = $this->language->get('text_error');

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/mobile_store/not_found.tpl')) {
				$this->template = $this->config->get('config_template') . '/template/mobile_store/not_found.tpl';
			} else {
				$this->template = 'default/template/mobile_store/not_found.tpl';
			}
			
			$this->children = array(
				'mobile_store/navi',
				'mobile_store/titlebar',
				'mobile_store/header'
			);
					
			$this->response->setOutput($this->render());
		}
	}
}
?>