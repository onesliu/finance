<?php
class ControllerModuleMSFeatured extends Controller {
	protected function index($setting) {
		static $module = 0;
		
      	$this->data['heading_title'] = $this->config->get('ms_featured_heading_title_' . $this->config->get('config_language_id') );
		
		$this->data['button_cart'] = $this->language->get('button_cart');
		
		$this->data['show_class'] = ($setting['initial'] == "up") ? "active" : "";
		
		$this->load->model('mobile_store/product'); 
		
		$this->load->model('tool/image');

		$this->data['products'] = array();

		$products = explode(',', $this->config->get('ms_featured_product'));		
		
		$fs_selected_products = $this->config->get('mobile_store_selected_products');	
		
		if ($fs_selected_products != 0 && $fs_selected_products != "") {
			$arr_fs_selected_products = explode("," , $fs_selected_products);
			$fs_filter_products = true;
		} else {
			$fs_filter_products = false;
		}
		
		foreach ($products as $product_id) {
			if ($fs_filter_products){
				if ( in_array($product_id, $arr_fs_selected_products) ) {
					$product_info = $this->model_catalog_product->getProduct($product_id);
				} else {
					$product_info = null;
				}
			} else {
				$product_info = $this->model_catalog_product->getProduct($product_id);
			}	
			
			if ($product_info) {
				if ($product_info['image']) {
					$image = $this->model_tool_image->resize($product_info['image'], $setting['image_width'], $setting['image_height']);
				} else {
					$image = false;
				}

				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')));
				} else {
					$price = false;
				}
						
				if ((float)$product_info['special']) {
					$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')));
				} else {
					$special = false;
				}
				
				if ($this->config->get('config_review_status')) {
					$rating = $product_info['rating'];
				} else {
					$rating = false;
				}
					
				$this->data['products'][] = array(
					'product_id' => $product_info['product_id'],
					'thumb'   	 => $image,
					'name'    	 => $product_info['name'],
					'description'=> mb_substr(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), 0, 100) . '..',
					'price'   	 => $price,
					'special' 	 => $special,
					'rating'     => $rating,
					'original_href'    	 => $this->url->link('product/product', 'product_id=' . $product_info['product_id']),
				    'href'    	 => $this->url->link('mobile_store/product', 'product_id=' . $product_info['product_id'])
				);
			}
		}
		
		
		$this->data['module'] = $module++;

		$template_file = "ms_featured.tpl";
		

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/' . $template_file )) {
			$this->template = $this->config->get('config_template') . '/template/module/' . $template_file;
		} else {
			$this->template = 'default/template/module/' . $template_file;
		}

		$this->render();
	}
}
?>