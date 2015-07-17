<?php
class ControllerModuleMSLatest extends Controller {
	protected function index($setting) {
		static $module = 0; 
		
      	$this->data['heading_title'] = $this->config->get('ms_latest_heading_title_' . $this->config->get('config_language_id') );
		
		$this->data['button_cart'] = $this->language->get('button_cart');
		
		$this->data['show_class'] = ($setting['initial'] == "up") ? "active" : "";
		
		$this->load->model('tool/image');
		
		$this->data['image_width'] = $setting['image_width'];
		$this->data['image_height'] = $setting['image_height'];

		$this->load->model('mobile_store/product');
		
		$this->data['products'] = array();
		
		$data = array(
			'sort'  => 'p.date_added',
			'order' => 'DESC',
			'start' => 0,
			'limit' => $setting['limit']
		);

		$results = $this->model_mobile_store_product->getProducts($data);

		foreach ($results as $result) {
			if ($result['image']) {
				$image = $this->model_tool_image->img_url($result['image']);
			} else {
				$image = $this->model_tool_image->img_url('no_image.jpg');
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
			
			if ($this->config->get('config_review_status')) {
				$rating = $result['rating'];
			} else {
				$rating = false;
			}
			
			$this->data['products'][] = array(
				'product_id' => $result['product_id'],
				'thumb'   	 => $image,
				'name'    	 => $result['name'],
				'description'=> mb_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, 100) . '..',
				'price'   	 => $price,
				'special' 	 => $special,
				'rating'     => $rating,
				'weight'	 => ((int)$result['weight']) . $result['weight_class'],
				'model'	 	 => $result['model'],
				'type'		 => $result['type'],
				'product_type' => $result['product_type'],
				'subscribe'	 => $result['subscribe'],
				'original_href'    	 => $this->url->link('product/product', 'product_id=' . $result['product_id']),
				'href'    	 => $this->url->link('mobile_store/product', 'product_id=' . $result['product_id'])
			);
		}
		
		$this->data['module'] = $module++;

		
		$template_file = "ms_latest.tpl";
	

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/' . $template_file )) {
			$this->template = $this->config->get('config_template') . '/template/module/' . $template_file;
		} else {
			$this->template = 'default/template/module/' . $template_file;
		}

		$this->render();
	}
	
}
?>