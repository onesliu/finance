<?php
class ControllerModuleMSSpecialCountDown extends Controller {
	protected function index($setting) {
		static $module = 0;
		
		$this->data['heading_title'] = $this->config->get('ms_special_countdown_heading_title_' . $this->config->get('config_language_id') );
		$this->data['heading_days'] = $this->config->get('ms_special_countdown_heading_days_' . $this->config->get('config_language_id') );
		$this->data['heading_hours'] = $this->config->get('ms_special_countdown_heading_hours_' . $this->config->get('config_language_id') );
		
		$this->load->model('mobile_store/product');
		$this->load->model('module/ms_special_countdown');
		
		$this->data['button_cart'] = $this->language->get('button_cart');
		
		$this->load->model('catalog/product');
		
		$this->load->model('tool/image');

		$this->data['products'] = array();
		
		$data = array(
			'sort'  => 'pd.name',
			'order' => 'ASC',
			'start' => 0,
			'limit' => $setting['limit']
		);

		$results = $this->model_mobile_store_product->getProductSpecials($data);

		foreach ($results as $result) {
			if ($result['image']) {
				$image = $this->model_tool_image->resize($result['image'], $setting['image_width'], $setting['image_height']);
			} else {
				$image = false;
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
			
			$description = mb_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, 100) . '..';			
			
			$show_special_countdown = 0;
			$show_days              = 1;
			$secs					= 0;
			$mins                   = 0;
			$hours	                = 0;
			$days                   = 0;
			$weeks                  = 0;
 			
			$date_stop = $this->model_module_ms_special_countdown->getSpecialPriceEndData($result['product_id']);
			
			if ($date_stop){
				$target_split = preg_split('/\-/', $date_stop);		
				
				$now = time();
				$target = mktime(0,0,0, $target_split[1], $target_split[2], $target_split[0]);
				$diffSecs = $target - $now;
				
				if ( $diffSecs > 0 ){
					$show_special_countdown = 1;
					
					if ($diffSecs < 24 * 60 * 60 ){
						$show_days = 0;
					}
					
					$secs  = $diffSecs % 60;
					$mins  = floor($diffSecs/60)%60;
					$hours = floor($diffSecs/60/60)%24;
					$days  = floor($diffSecs/60/60/24);//%7;
					$weeks = floor($diffSecs/60/60/24/7);
				}
			}		
			
			$this->data['products'][] = array(
				'product_id' => $result['product_id'],
				'thumb'   	 => $image,
				'name'    	 => $result['name'],
				'description' => $description,
				'price'   	 => $price,
				'special' 	 => $special,
				'rating'     => $rating,
				'show_special_countdown' => $show_special_countdown,
				'days'                   =>  $days,
				'hours'                  =>  $hours,
				'mins'                   =>  $mins,
				'secs'                   =>  $secs,
				'reviews'    => sprintf($this->language->get('text_reviews'), (int)$result['reviews']),
				'original_href'    	 => $this->url->link('product/product', 'product_id=' . $result['product_id']),
				'href'    	 => $this->url->link('mobile_store/product', 'product_id=' . $result['product_id'])
			);
		
		}
		
		$this->data['show_class'] = ($setting['initial'] == "up") ? "active" : "";
			
		$this->data['theme_name'] = $this->config->get('config_template');

		$this->data['module'] = $module++;
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/ms_special_countdown.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/module/ms_special_countdown.tpl';
		} else {
			$this->template = 'default/template/module/ms_special_countdown.tpl';
		}

		$this->render();
	}
}
?>