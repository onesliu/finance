<?php
class ControllerMobileStoreCheckoutOrder extends Controller { 
	public function index() {
		if (!$this->customer->isLogged() && (!$this->cart->hasProducts())) {
			$this->log->write('checkout cart product error: not logged.');
	  		$this->redirect($this->url->link('mobile_store/cart'));
    	}	

   		if (isset($this->request->post['options'])) {
			$option = $this->request->post['options'];
		} else {
			$option = array();	
		}
		
    	$products = $this->cart->getProducts();

		foreach ($products as $product) {
			$product_total = 0;

			foreach ($products as $product_2) {
				if ($product_2['product_id'] == $product['product_id']) {
					$product_total += $product_2['quantity'];
				}
			}		
			
			if ($product['minimum'] > $product_total) {
				$this->redirect($this->url->link('mobile_store/cart'));
			}				

			if (!isset($product['option'])) {
				$product_options = $this->model_catalog_product->getProductOptions($product['product_id']);
				$product['option'] = $this->make_option($option, $product_options);
			}
		}
		
		$total_data = $this->cart_total();
		// cart product values
		$order_info = $this->confirm($products, $total_data);
		
		if ($order_info == false) {
			$this->order_error();
			return;
		}

		$this->cart->clear();
		
		if ($order_info['order_type']==0 || $order_info['order_type']==2) {
			//'微信支付'
			$weixin_payment = $this->url->link('weixin/pay', '', 'wxpay');
		}
		else {
			//'下单待称重'
			$weixin_payment = $this->url->link('weixin/pay/prepay');
		}
		
		$this->redirect($weixin_payment);
  	}
  	
  	public function special() {
  		if (!$this->customer->isLogged()) {
  			$this->log->write('checkout special product error: not logged.');
  			$this->redirect($this->url->link('mobile_store/home'));
  		}
  		
  		if (!isset($this->request->get['product_id'])) {
  			$this->log->write('checkout special product error: no product_id.');
  			$this->redirect($this->url->link('mobile_store/home'));
  		}
  		$product_id = $this->request->get['product_id'];
  		
  		$this->load->model('catalog/product');
  		$product = $this->model_catalog_product->getProduct($product_id);
  		if ($product == false) {
			$this->order_error();
			return;
  		}
  		if (isset($this->request->get['quantity'])) {
  			$product['quantity'] = $this->request->get['quantity'];
  		}
  		else {
  			$product['quantity'] = 1;
  		}
  		
  		if (isset($this->request->post['options'])) {
			$option = $this->request->post['options'];
		} else {
			$option = array();	
		}
  		
		$product_options = $this->model_catalog_product->getProductOptions($product_id);
		$product['option'] = $this->make_option($option, $product_options);
  
		$option_price = 0.0;
		foreach($product['option'] as $product_option_value) {
			$option_price += (double)($product_option_value['price_prefix'].$product_option_value['price']);
		}
		
  		$product['total'] = ($product['sellprice'] + $option_price) * $product['quantity'];
  		$product['weight'] = $product['weight'] * $product['quantity'];
  		
  		$products = array();
  		$products[$product_id] = $product;
  		
  		$total_data = $this->special_total($product);
  		$order_info = $this->confirm($products, $total_data, false);
  		
		//'下单待预定'
		$weixin_payment = $this->url->link('weixin/pay/prepay');
		
		$this->redirect($weixin_payment);
  	}
  	
  	private function make_option($option, $product_options) {
  		
  		$option_data = array();
  		foreach ($product_options as $product_option) {
			if (empty($option[$product_option['product_option_id']])) continue;
			
			foreach($product_option['option_value'] as $product_option_value) {
				if ($option[$product_option['product_option_id']] == $product_option_value['product_option_value_id']) {
					$option_data[] = array(
							'product_option_id'       => $product_option['product_option_id'],
							'product_option_value_id' => $product_option_value['product_option_value_id'],
							'option_id'               => $product_option['option_id'],
							'option_value_id'         => $product_option_value['option_value_id'],
							'name'                    => $product_option['name'],
							'option_value'            => $product_option_value['name'],
							'type'                    => $product_option['type'],
							'quantity'                => $product_option_value['quantity'],
							'subtract'                => $product_option_value['subtract'],
							'price'                   => $product_option_value['price'],
							'price_prefix'            => $product_option_value['price_prefix'],
							'points'                  => $product_option_value['points'],
							'points_prefix'           => $product_option_value['points_prefix'],									
							'weight'                  => $product_option_value['weight'],
							'weight_prefix'           => $product_option_value['weight_prefix']
						);
				}
			}
		}
		
		return $option_data;
  	}
  	
  	private function order_error() {
  		$e = "下单失败";
		$this->document->setTitle($e);
      	$this->data['heading_title'] = $e;
      	$this->data['text_error'] = $e;

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
  	
  	private function cart_total() {
  		$total_data = array();
  		$total = 0;
  		$taxes = $this->cart->getTaxes();
		
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
		
		$sort_order = array(); 
	  
		foreach ($total_data as $key => $value) {
			$sort_order[$key] = $value['sort_order'];
		}

		array_multisort($sort_order, SORT_ASC, $total_data);
		
		return $total_data;
  	}
  	
  	private function special_total($product) {
  		$total_data = array();
  		$this->language->load('total/total');
  		$this->language->load('total/sub_total');
  		
		$total_data[] = array( 
			'code'       => 'sub_total',
			'title'      => $this->language->get('text_sub_total'),
			'text'       => $this->currency->format($product['total']),
			'value'      => $product['total'],
			'sort_order' => $this->config->get('sub_total_sort_order')
		);
  		$total_data[] = array(
			'code'       => 'total',
			'title'      => $this->language->get('text_total'),
			'text'       => $this->currency->format(max(0, $product['total'])),
			'value'      => max(0, $product['total']),
			'sort_order' => $this->config->get('total_sort_order')
		);

		return $total_data;
  	}
  	
  	private function confirm($products, $total_data, $bucha = true) {
		$data = array();
		
		$data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
		$data['store_id'] = $this->config->get('config_store_id');
		$data['store_name'] = $this->config->get('config_name');
		
		if ($data['store_id']) {
			$data['store_url'] = $this->config->get('config_url');		
		} else {
			$data['store_url'] = HTTP_SERVER;	
		}
		
		if (isset($this->session->data['payment_address_id'])) {
			$address_id = $this->session->data['payment_address_id'];
		} else {
			$address_id = $this->customer->getAddressId();
		}
		
		if ($this->customer->isLogged()) {
			$data['customer_id'] = $this->customer->getId();
			$data['customer_group_id'] = $this->customer->getCustomerGroupId();
			$data['firstname'] = $this->customer->getFirstName();
			$data['lastname'] = $this->customer->getLastName();
			$data['email'] = $this->customer->getEmail();
			$data['telephone'] = $this->customer->getTelephone();
			$data['fax'] = $this->customer->getFax();

			$this->load->model('account/address');
			$payment_address = $this->model_account_address->getAddress($address_id);
		} elseif (isset($this->session->data['guest'])) {
			$data['customer_id'] = 0;
			$data['customer_group_id'] = $this->session->data['guest']['customer_group_id'];
			$data['firstname'] = $this->session->data['guest']['firstname'];
			$data['lastname'] = $this->session->data['guest']['lastname'];
			$data['email'] = $this->session->data['guest']['email'];
			$data['telephone'] = $this->session->data['guest']['telephone'];
			$data['fax'] = $this->session->data['guest']['fax'];

			$payment_address = $this->session->data['guest']['payment'];
		}
		
		$data['payment_firstname'] = $this->customer->getFirstName();
		$data['payment_lastname'] = $this->customer->getLastName();	
		$data['payment_telephone'] = $this->customer->getTelephone();
		$data['payment_company'] = $payment_address['company'];	
		$data['payment_company_id'] = $payment_address['company_id'];	
		$data['payment_tax_id'] = $payment_address['tax_id'];	
		$data['payment_address_1'] = $payment_address['address_1'];
		$data['payment_address_2'] = $payment_address['address_2'];
		$data['payment_city'] = $payment_address['city'];
		$data['payment_postcode'] = $payment_address['postcode'];
		$data['payment_zone'] = $payment_address['zone'];
		$data['payment_zone_id'] = $payment_address['zone_id'];
		$data['payment_country'] = $payment_address['country'];
		$data['payment_country_id'] = $payment_address['country_id'];
		$data['payment_address_format'] = $payment_address['address_format'];
	
		if (isset($this->session->data['payment_method']['title'])) {
			$data['payment_method'] = $this->session->data['payment_method']['title'];
		} else {
			$data['payment_method'] = '';
		}
		
		if (isset($this->session->data['payment_method']['code'])) {
			$data['payment_code'] = $this->session->data['payment_method']['code'];
		} else {
			$data['payment_code'] = '';
		}
	
		$data['shipping_firstname'] = $payment_address['firstname'];
		$data['shipping_lastname'] = $payment_address['lastname'];	
		$data['shipping_company'] = $payment_address['company'];	
		$data['shipping_address_1'] = $payment_address['address_1'];
		$data['shipping_address_2'] = $payment_address['address_2'];
		$data['shipping_city'] = $payment_address['city'];
		$data['shipping_postcode'] = $payment_address['postcode'];
		$data['shipping_zone'] = $payment_address['zone'];
		$data['shipping_zone_id'] = $payment_address['zone_id'];
		$data['shipping_country'] = $payment_address['country'];
		$data['shipping_country_id'] = $payment_address['country_id'];
		$data['shipping_address_format'] = $payment_address['address_format'];
		$data['shipping_district_id'] = $payment_address['district_id'];
		$data['shipping_telephone'] = $payment_address['telephone'];
	
		if (isset($this->session->data['shipping_method']['title'])) {
			$data['shipping_method'] = $this->session->data['shipping_method']['title'];
		} else {
			$data['shipping_method'] = '';
		}
		
		if (isset($this->session->data['shipping_method']['code'])) {
			$data['shipping_code'] = $this->session->data['shipping_method']['code'];
		} else {
			$data['shipping_code'] = '';
		}				
		
		$product_data = array();
		$order_type = 0; //0:固定客单价订单, 1:变客单价订单, 2:特产单品订单
		$comment = "";
		
		foreach ($products as $product) {
			$option_data = array();

			if (isset($product['option'])) {
				foreach ($product['option'] as $option) {
					if ($option['type'] != 'file') {
						$value = $option['option_value'];	
					} else {
						$value = $this->encryption->decrypt($option['option_value']);
					}	
					
					$option_data[] = array(
						'product_option_id'       => $option['product_option_id'],
						'product_option_value_id' => $option['product_option_value_id'],
						'option_id'               => $option['option_id'],
						'option_value_id'         => $option['option_value_id'],								   
						'name'                    => $option['name'],
						'value'                   => $value,
						'type'                    => $option['type']
					);					
				}
			}
			
			$download = array();
			if (isset($product['download']))
				$download = $product['download'];
			
			if ($product['product_type'] == 1) {
				$order_type = 1;
			}
			
			if ($product['product_type'] == 2 && count($products) == 1) {
				$order_type = 2;
			}
 
			$product_data[] = array(
				'product_id' => $product['product_id'],
				'name'       => $product['name'],
				'model'      => $product['model'],
				'option'     => $option_data,
				'download'   => $download,
				'quantity'   => $product['quantity'],
				'subtract'   => $product['subtract'],
				'price'      => $product['price'],
				'weight'	 => $product['weight'],
				'total'      => $product['total'],
				'tax'        => $this->tax->getTax($product['price'], $product['tax_class_id']),
				'reward'     => $product['reward']
			);
			
			$comment .= $product['name']." ";
		}
  	
		//补差商品加入
		if ($bucha == true) {
			$this->load->model('mobile_store/product');
			$bucha = $this->model_mobile_store_product->getBuchaProduct();
			if ($bucha != false) {
				$hasbucha = false;
				foreach($product_data as $p) {
					if ($p['product_id'] == $bucha['product_id']) {
						$hasbucha = true;
						break;
					}
				}
				
				if ($hasbucha == false) {
					$product_data[] = array(
						'product_id' => $bucha['product_id'],
						'name'       => $bucha['name'],
						'model'      => $bucha['model'],
						'option'     => array(),
						'download'   => array(),
						'quantity'   => 1,
						'subtract'   => 0,
						'price'      => $bucha['price'],
						'weight'	 => $bucha['weight'],
						'total'      => 0.0,
						'tax'        => $this->tax->getTax($bucha['price'], $bucha['tax_class_id']),
						'reward'     => 0
					);
				}
			}
		}
		
		$data['order_type'] = $order_type;
		$data['comment'] = trim($comment);
		if ($order_type == 0 || $order_type == 2) //固定价订单状态转换至：待付款
			$data['order_status_id'] = 2;
		else //变价订单状态转换至：待称重
			$data['order_status_id'] = 1;
		//$data['comment'] = (isset($this->session->data['comment']))?$this->session->data['comment']:"";
		
		// Gift Voucher
		$voucher_data = array();
		
		if (!empty($this->session->data['vouchers'])) {
			foreach ($this->session->data['vouchers'] as $voucher) {
				$voucher_data[] = array(
					'description'      => $voucher['description'],
					'code'             => substr(md5(mt_rand()), 0, 10),
					'to_name'          => $voucher['to_name'],
					'to_email'         => $voucher['to_email'],
					'from_name'        => $voucher['from_name'],
					'from_email'       => $voucher['from_email'],
					'voucher_theme_id' => $voucher['voucher_theme_id'],
					'message'          => $voucher['message'],						
					'amount'           => $voucher['amount']
				);
			}
		}

		foreach($total_data as $tt) {
      		if ($tt['code'] == 'total') {
      			$this->data['totals'][] = $tt;
      		}
      	}
      	
      	$total = 0;
      	foreach($total_data as $tt) {
      		if ($tt['code'] == 'total')
      			$total = $tt['value'];
      	}
      	
      	$data['coupon_total'] = 0.0;
      	if (isset($this->session->data['coupon'])) {
      		$coupon = $this->session->data['coupon'];
      		if ($coupon["order_total"] != $total) {
      			$this->log->write($coupon["order_total"].' '.$total);
      			$this->log->write(print_r($coupon,true));
      			return false;
      		}
      			
      		$data['coupon_total'] = $coupon['discount'];
      	}
		
      	$data['products'] = $product_data;
      	$data['vouchers'] = $voucher_data;
		$data['totals'] = $total_data;
		$data['total'] = $total;
		
		$data['affiliate_id'] = 0;
		$data['commission'] = 0;
		$data['language_id'] = $this->config->get('config_language_id');
		$data['currency_id'] = $this->currency->getId();
		$data['currency_code'] = $this->currency->getCode();
		$data['currency_value'] = $this->currency->getValue($this->currency->getCode());
		$data['ip'] = $this->request->server['REMOTE_ADDR'];
		
		if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
			$data['forwarded_ip'] = $this->request->server['HTTP_X_FORWARDED_FOR'];	
		} elseif(!empty($this->request->server['HTTP_CLIENT_IP'])) {
			$data['forwarded_ip'] = $this->request->server['HTTP_CLIENT_IP'];	
		} else {
			$data['forwarded_ip'] = '';
		}
		
		if (isset($this->request->server['HTTP_USER_AGENT'])) {
			$data['user_agent'] = $this->request->server['HTTP_USER_AGENT'];	
		} else {
			$data['user_agent'] = '';
		}
		
		if (isset($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
			$data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];	
		} else {
			$data['accept_language'] = '';
		}
					
		//$this->log->write(print_r($this->request->post, true));
		if (isset($this->request->post['district-select']))
			$data['shipping_district_id'] = $this->request->post['district-select'];
		else
			$data['shipping_district_id'] = 0;
			
		if (isset($this->request->post['time-select']))
			$data['shipping_time'] = $this->request->post['time-select'];
		else
			$data['shipping_time'] = 0;
			
		$data['shipping_firstname'] = $this->request->post['user_name'];
		$data['shipping_telephone'] = $this->request->post['user_telephone'];
		$data['shipping_address_1'] = $this->request->post['user_addr'];
		
		//保存订单
		$this->load->model('checkout/order');
		
		$data['order_id'] = new_wx_orderid();
		$ret = $this->model_checkout_order->addOrder($data);
		while ($ret == false) {
			$orderid = inc_order_serial($data['order_id']);
			if ($orderid == false) {
				return false;
			}
			else {
				$data['order_id'] = $orderid;
				$ret = $this->model_checkout_order->addOrder($data);
			}
		}
		$this->model_checkout_order->confirm($data['order_id'], $data['order_status_id']);

		$this->session->data['order_id'] = $data['order_id'];
		$this->session->data['order_info'] = $data;
		
		$this->save_addr($data);
		
		return $data;
  	}
  	
  	public function save_addr($order_info) {
		$this->load->model('account/district');
		$this->load->model('account/address');
		$this->load->model('account/customer');
		
		if (!isset($order_info['shipping_firstname']) && $order_info['shipping_firstname'] == '')
			return;
		
		$addr['firstname'] = $order_info['shipping_firstname'];
		$addr['telephone'] = $order_info['shipping_telephone'];
		$addr['address_1'] = $order_info['shipping_address_1'];
		$addr['district_id'] = $order_info['shipping_district_id'];
		$addr['lastname'] = '';
		$addr['company'] = '';
		$addr['company_id'] = '';
		$addr['tax_id'] = '';
		$addr['address_2'] = '';
		$addr['postcode'] = $this->request->post['user_postcode'];;
		$addr['city'] = $this->request->post['user_city'];;
		$addr['zone_id'] = 0;
		$addr['country_id'] = 44;
		
		$addrid = $this->model_account_address->findAddress($addr);
		if ($addrid == null) {
			 $addrid = $this->model_account_address->addAddress($addr);
		}
		$this->model_account_customer->setLastAddress($this->customer->getId(), $addrid);
	}
  	
}
?>