<?php
include_once(DIR_APPLICATION."controller/weixin/weixin.php");

class ControllerWeixinShipping extends ControllerWeixinWeixin { 
	
	private $tfile = '/template/weixin/shipping.tpl';
	
	public function index() {
		
		if ($this->weixin_init() != true) {
			return; //首次验证或初始化失败
		}
		
		$this->load->model('account/address');
		$this->load->model('account/district');

		if (isset($this->session->data['payment_address_id'])) {
			$this->data['address_id'] = $this->session->data['payment_address_id'];
		} else {
			$this->data['address_id'] = $this->customer->getAddressId();
		}

		$address = $this->model_account_address->getAddress($this->data['address_id']);
		if ($address != false) {
			$this->data['address'] = $address;
		}
		
		$this->data['saveaddr_url'] = $this->url->link('mobile_store/shipping/saveaddr');

		// shipping values
		$this->data['text_shipping_time'] = $this->language->get('text_shipping_time');
		$this->data['text_shipping_district'] = $this->language->get('text_shipping_district');
		$this->data['shipping_districts'] = $this->model_account_district->getAddresses();
		if (!isset($this->data['shipping_districts'])) {
			if (count($this->data['shipping_districts']) == 1 &&
				$this->data['shipping_district_id'] == 0) {
					$this->data['shipping_district_id'] = $this->data['shipping_districts']['id'];
				}
		}
		
		// shipping time
		$first_shipping_time = $this->config->get('first_shipping_time');
		if ($first_shipping_time == null)
			$first_shipping_time = '10:30';
		$this->data['first_shipping_time'] = $first_shipping_time;
			
		$last_shipping_time = $this->config->get('last_shipping_time');
		if ($last_shipping_time == null)
			$last_shipping_time = '16:30';
		$this->data['last_shipping_time'] = $last_shipping_time;
		
		$now = time();
		
		$today = date("Y-m-d", $now);
		$tomorow = date("Y-m-d", $now+24*60*60);
		
		$cuttime1 = strtotime("$today $first_shipping_time:00");
		$cuttime2 = strtotime("$today $last_shipping_time:00");
		
		if ($now < $cuttime1)
			$this->data['shipping_time']["$today 11:30:00"] = "今天中午12点前";

		if ($now < $cuttime2)
			$this->data['shipping_time']["$today 17:30:00"] = "今天下午6点前";
			
		if ($now - $cuttime2 >= 0 && $now - $cuttime2 < 3600) { //下午截止下单后半小时内不能下单
			$this->data['gap_time'] = date("H:i", $cuttime2+3600);
		}

		$this->data['shipping_time']["$tomorow 11:30:00"] = "明天中午12点前";
		$this->data['shipping_time']["$tomorow 17:30:00"] = "明天下午6点前";
		
		$this->data['user_telephone'] = $this->customer->getTelephone();
		$this->data['checkout'] = $this->url->link('mobile_store/checkout_order', '', 'wxpay');
		$this->request->get['back'] = true;
		if (isset($this->session->data['order_type'])) {
			$order_type = $this->session->data['order_type'];
		}
		else {
			$order_type = 1;
		}
		
		if ($order_type == 1) {
			$this->data['checkout_btn'] = "下 单 称 重";
		}
		else {
			$this->data['checkout_btn'] = "微 信 支 付";
		}
		
		$this->wx_address();
		
		$this->children = array(
			'mobile_store/titlebar',
			'mobile_store/header'
		);
		
		// view template
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . $this->tfile)) {
			$this->template = $this->config->get('config_template') . $this->tfile;
		} else {
			$this->template = 'default' . $this->tfile;
		}
		
		$this->response->setOutput($this->render());
	}
	
	private function wx_address() {
		
		$addrHelper = new PayHelper();
		$addrHelper->add_param("appid", $this->appid);
		$url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$addrParam['url'] = str_replace("&amp;", "&", $url);
		$addrHelper->add_param("url", $addrParam['url']);
		$addrParam['timeStamp'] = time();
		$addrHelper->add_param("timestamp", $addrParam['timeStamp']);
		$addrParam['nonceStr'] = $addrParam['timeStamp'];
		$addrHelper->add_param("noncestr", $addrParam['nonceStr']);
		if (isset($this->session->data['oauth_access_token'])) {
			$addrParam['token'] = $this->session->data['oauth_access_token'];
			$addrHelper->add_param("accesstoken", $addrParam['token']);
		}
		
		$sign = $addrHelper->make_addr_sign();
		$addrParam['addrSign'] = $sign;
		$addrParam['appId'] = $this->appid;
		
		$this->data['addrParam'] = $addrParam;
	}
	
	public function special() {
		
		if (!isset($this->request->get['product_id'])) {
			$this->log->write('shipping special: 没有产品ID');
			$this->response->setOutput('没有产品ID');
			return;
		}
		
		$product_id = $this->request->get['product_id'];
		$this->tfile = '/template/weixin/shipping_special.tpl';
		
		$this->load->model('catalog/product');
		$product_info = $this->model_catalog_product->getProduct($product_id);
		if (!$product_info) {
			$this->log->write('shipping special: 不能查询到产品信息');
			return;
		}
		
		$this->load->model('tool/image');
		
		$this->data['name'] = $product_info['name'];
		$this->data['price'] = $product_info['sellprice']; //销售单位价格
		$this->data['showprice'] = $this->currency->format($product_info['sellprice']); //销售单位价格
		$this->data['unit'] = $product_info['sellunit']; //销售单位
		if ($product_info['image']) {
			$this->data['image'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height'));
		} else {
			$this->data['image'] = $this->model_tool_image->resize('no_image.jpg', $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height'));;
		}
		
		$this->data['images'] = array();
			
		$results = $this->model_catalog_product->getProductImages($this->request->get['product_id']);
		
		foreach ($results as $result) {
			$this->data['images'][] = array(
				'popup' => $this->model_tool_image->resize($result['image'], $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height')),
				'thumb' => $this->model_tool_image->resize($result['image'], $this->config->get('mobile_store_image_width'), $this->config->get('mobile_store_image_height'))
			);
		}
		
  		if (isset($this->request->post['options'])) {
			$option = $this->request->post['options'];
		} else {
			$option = array();	
		}
		$this->data['option_selected'] = $option;

		$this->data['options'] = array();
		
		foreach ($this->model_catalog_product->getProductOptions($this->request->get['product_id']) as $option) { 
			if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox' || $option['type'] == 'image') { 
				$option_value_data = array();
				
				foreach ($option['option_value'] as $option_value) {
					if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
						$option_value_data[] = array(
							'product_option_value_id' => $option_value['product_option_value_id'],
							'option_value_id'         => $option_value['option_value_id'],
							'name'                    => $option_value['name'],
							'image'                   => $this->model_tool_image->resize($option_value['image'], 50, 50),
							'price'                   => (float)$option_value['price'] ? $option_value['price'] : 0,
							'price_prefix'            => $option_value['price_prefix']
						);
					}
				}
				
				$this->data['options'][] = array(
					'product_option_id' => $option['product_option_id'],
					'option_id'         => $option['option_id'],
					'name'              => $option['name'],
					'type'              => $option['type'],
					'option_value'      => $option_value_data,
					'required'          => $option['required']
				);					
			} elseif ($option['type'] == 'text' || $option['type'] == 'textarea' || $option['type'] == 'file' || $option['type'] == 'date' || $option['type'] == 'datetime' || $option['type'] == 'time') {
				$this->data['options'][] = array(
					'product_option_id' => $option['product_option_id'],
					'option_id'         => $option['option_id'],
					'name'              => $option['name'],
					'type'              => $option['type'],
					'option_value'      => $option['option_value'],
					'required'          => $option['required']
				);						
			}
		}
	
			
		$this->data['checkout_url'] = $this->url->link2('mobile_store/checkout_order/special', 'product_id='.$product_id);
		$this->request->get['back'] = true;
		
		$this->index();
		
		$this->response->setOutput($this->output);
	}
}

?>