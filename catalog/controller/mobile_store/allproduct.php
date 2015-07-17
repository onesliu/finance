<?php 
class ControllerMobileStoreAllproduct extends Controller {  
	public function index() { 
		$this->language->load('mobile_store/category');
		
		$this->load->model('catalog/category');
		
		$this->load->model('catalog/product');
		$this->load->model('mobile_store/product');
		
		$this->load->model('tool/image');
		
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$dir_img = $this->config->get('config_ssl') . 'image/';
		} else {
			$dir_img = $this->config->get('config_url') . 'image/';
		}
		$this->data['dir_img'] = $dir_img;
		
		$this->request->get['back'] = true;
		$this->request->get['product_page'] = true;
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		$limit = 20;
		
		if (isset($this->request->get['category_name'])) {
			$category_name = $this->request->get['category_name'];
		}
		
		$this->data['heading_title'] = '所有商品';
		
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
		
		$this->data['products'] = array();
		
		$data = array(
			'start'              => ($page - 1) * $limit,
			'limit'              => $limit,
			'sort'				 => 'p2c.category_id',
		);
		
		if (isset($this->request->get['filter'])) {
			$data['filter_name'] = $this->request->get['filter'];
		}
		
		if (isset($category_name) && $category_name != '') {
			$product_total = $this->model_mobile_store_product->getTotalProductsByCategoryName($category_name, $data);
		}
		else {
			$product_total = $this->model_mobile_store_product->getTotalProducts($data);
		}
		
		if ($product_total > 0) {
		
			if (isset($category_name) && $category_name != '') {
				$results = $this->model_mobile_store_product->getProductsByCategoryName($category_name, $data);
			}
			else {
				$results = $this->model_mobile_store_product->getProducts($data);
			}
			
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
					$scalable = '付款后称重'; //0: 固定价格产品, 2: 单项特品
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
					'sellprice'	  => $result['mpn'], //销售单位价格
					'product_type' => $result['product_type'],
					'special'     => $special,
					'tax'         => $tax,
					'rating'      => $result['rating'],
					'weight_show' => ((int)$result['weight']) . $result['weight_class'],
					'scalable'	  => $scalable,
					'reviews'     => sprintf($this->language->get('text_reviews'), (int)$result['reviews']),
					'href'        => $this->url->link('mobile_store/product', '&product_id=' . $result['product_id'])
				);
			}
			
			$pagination = new Pagination();
			$pagination->total = $product_total;
			$pagination->page = $page;
			$pagination->limit = $limit;
			$pagination->num_pages = ceil($pagination->total / $pagination->limit);
			$pagination->text = $this->language->get('text_pagination');
			if (isset($category_name) && $category_name != '') {
				$pagination->url = $this->url->link2('mobile_store/allproduct', "category_name=$category_name", 'SSL');
			}
			else {
				$pagination->url = $this->url->link2('mobile_store/allproduct', '', 'SSL');
			}
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
			
    	} else {
			$this->document->setTitle($this->language->get('text_error'));

      		$this->data['heading_title'] = $this->language->get('text_error');

      		$this->data['text_error'] = '对不起，未找到相关的商品';

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/mobile_store/not_found.tpl')) {
				$this->template = $this->config->get('config_template') . '/template/mobile_store/not_found.tpl';
			} else {
				$this->template = 'default/template/mobile_store/not_found.tpl';
			}
			
    	}
    				
		$this->children = array(
			'mobile_store/navi',
			'mobile_store/titlebar',
			'mobile_store/header'
		);
    	$this->response->setOutput($this->render());										
	}
}
?>