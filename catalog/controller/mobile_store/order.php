<?php 
class ControllerMobileStoreOrder extends Controller {
	private $error = array();
		
	public function index() {
    	if (!$this->customer->isLogged()) {
      		$this->session->data['redirect'] = $this->url->link('mobile_store/order', '', 'SSL');

	  		$this->redirect($this->url->link('mobile_store/login', '', 'SSL'));
    	}
		
		$this->language->load('account/order');
		$this->load->model('account/order');
 		
    	$this->document->setTitle($this->language->get('heading_title'));

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_order_id'] = $this->language->get('text_order_id');
		$this->data['text_status'] = $this->language->get('text_status');
		$this->data['text_date_added'] = $this->language->get('text_date_added');
		$this->data['text_customer'] = $this->language->get('text_customer');
		$this->data['text_products'] = $this->language->get('text_products');
		$this->data['text_total'] = $this->language->get('text_total');
		$this->data['text_empty'] = $this->language->get('text_empty');
		$this->data['text_return'] = $this->language->get('text_return');

		$this->data['button_view'] = $this->language->get('button_view');
		$this->data['button_reorder'] = $this->language->get('button_reorder');
		$this->data['button_continue'] = $this->language->get('button_continue');
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		$limit = 30;
		
		$this->load->model('tool/image');
		
		$this->data['orders'] = array();
		
		$order_total = $this->model_account_order->getTotalOrders();
		
		$results = $this->model_account_order->getOrders(($page - 1) * $limit, $limit, 6); //不查询已取消订单
		
		foreach ($results as $result) {
			$product_total = $this->model_account_order->getOrderProducts($result['order_id']);
			
			$pname = '';
			foreach($product_total as &$product) {
				$pname .= $product['name'].' ';
				if ($product['image'])
					$product['image'] = $this->model_tool_image->resize($product['image'], $this->config->get('config_image_category_width'), $this->config->get('config_image_category_height'));
				else
					$product['image'] = $this->model_tool_image->img_url('no_image.jpg');
			}

			$this->data['orders'][] = array(
				'order_id'   => $result['order_id'],
				'name'       => $result['firstname'] . ' ' . $result['lastname'],
				'status'     => $result['status'],
				'order_status_id' => $result['order_status_id'],
				'date_added' => $result['date_added'],
				'products'   => $product_total,
				'productnames' => $pname,
				'total'      => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
				'href'       => $this->url->link('mobile_store/order/info', 'order_id=' . $result['order_id'], 'SSL'),
				'reorder'    => $this->url->link('mobile_store/order', 'order_id=' . $result['order_id'], 'SSL')
			);
		}

		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->num_pages = ceil($pagination->total / $pagination->limit);
		$this->log->write('total:'.$pagination->total." limit:".$pagination->limit);
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link2('mobile_store/order', "page=".($page+1), 'SSL');
		//$pagination->render();
		
		$this->data['pagination'] = $pagination;
		
		$this->request->get['back'] = true;
		
		if ($page == 1)
			$tfile = "order.tpl";
		else
			$tfile = "order_more.tpl";

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . "/template/mobile_store/$tfile")) {
			$this->template = $this->config->get('config_template') . "/template/mobile_store/$tfile";
		} else {
			$this->template = "default/template/mobile_store/$tfile";
		}
		
		if ($page <= 1) {
			$this->children = array(
				'mobile_store/titlebar',
				'mobile_store/navi',
				'mobile_store/header'
			);
		}
						
		$this->response->setOutput($this->render());				
	}
	
	public function info() { 
		$this->language->load('account/order');
		
		if (isset($this->request->get['order_id'])) {
			$order_id = $this->request->get['order_id'];
		} else {
			$order_id = 0;
		}	

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('mobile_store/order/info', 'order_id=' . $order_id, 'SSL');
			
			$this->redirect($this->url->link('mobile_store/login', '', 'SSL'));
    	}
						
		$this->load->model('account/order');
		$this->request->get['back'] = true;
		
		$order_info = $this->model_account_order->getOrder($order_id);
		
		if ($order_info) {
			$this->document->setTitle($this->language->get('text_order'));
			
     		$this->data['heading_title'] = $this->language->get('text_order');
			
			$this->data['text_order_detail'] = $this->language->get('text_order_detail');
			$this->data['text_invoice_no'] = $this->language->get('text_invoice_no');
    		$this->data['text_order_id'] = $this->language->get('text_order_id');
			$this->data['text_date_added'] = $this->language->get('text_date_added');
      		$this->data['text_shipping_method'] = $this->language->get('text_shipping_method');
			$this->data['text_shipping_address'] = $this->language->get('text_shipping_address');
      		$this->data['text_payment_method'] = $this->language->get('text_payment_method');
      		$this->data['text_payment_address'] = $this->language->get('text_payment_address');
      		$this->data['text_history'] = $this->language->get('text_history');
			$this->data['text_comment'] = $this->language->get('text_comment');
			$this->data['text_return'] = $this->language->get('text_return');

      		$this->data['column_name'] = $this->language->get('column_name');
      		$this->data['column_model'] = $this->language->get('column_model');
      		$this->data['column_quantity'] = $this->language->get('column_quantity');
      		$this->data['column_price'] = $this->language->get('column_price');
      		$this->data['column_total'] = $this->language->get('column_total');
			$this->data['column_action'] = $this->language->get('column_action');
			$this->data['column_date_added'] = $this->language->get('column_date_added');
      		$this->data['column_status'] = $this->language->get('column_status');
      		$this->data['column_comment'] = $this->language->get('column_comment');
      		$this->data['column_weight'] = '重量';
			
			$this->data['button_return'] = $this->language->get('button_return');
      		$this->data['button_continue'] = $this->language->get('button_continue');
		
			if ($order_info['invoice_no']) {
				$this->data['invoice_no'] = $order_info['invoice_prefix'] . $order_info['invoice_no'];
			} else {
				$this->data['invoice_no'] = '';
			}
			
			$this->data['order_id'] = $this->request->get['order_id'];
			$this->data['date_added'] = $order_info['date_added'];
			
			if ($order_info['payment_address_format']) {
      			$format = $order_info['payment_address_format'];
    		} else {
				$format = '{firstname}{lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
			}
		
    		$find = array(
	  			'{firstname}',
	  			'{lastname}',
	  			'{company}',
      			'{address_1}',
      			'{address_2}',
     			'{city}',
      			'{postcode}',
      			'{zone}',
				'{zone_code}',
      			'{country}'
			);
	
			$replace = array(
	  			'firstname' => $order_info['payment_firstname'],
	  			'lastname'  => $order_info['payment_lastname'],
	  			'company'   => $order_info['payment_company'],
      			'address_1' => $order_info['payment_address_1'],
      			'address_2' => $order_info['payment_address_2'],
      			'city'      => $order_info['payment_city'],
      			'postcode'  => $order_info['payment_postcode'],
      			'zone'      => $order_info['payment_zone'],
				'zone_code' => $order_info['payment_zone_code'],
      			'country'   => $order_info['payment_country']  
			);
			
			$this->data['payment_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

      		$this->data['payment_method'] = $order_info['payment_method'];
			
			if ($order_info['shipping_address_format']) {
      			$format = $order_info['shipping_address_format'];
    		} else {
				//$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
				$format = '{firstname}{lastname}&nbsp;{telephone}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}';
			}
		
    		$find = array(
	  			'{firstname}',
	  			'{lastname}',
	  			'{company}',
      			'{address_1}',
      			'{address_2}',
     			'{city}',
      			'{postcode}',
      			'{zone}',
				'{zone_code}',
      			'{country}',
    			'{telephone}'
			);
	
			$replace = array(
	  			'firstname' => $order_info['shipping_firstname'],
	  			'lastname'  => $order_info['shipping_lastname'],
	  			'company'   => $order_info['shipping_company'],
      			'address_1' => $order_info['shipping_address_1'],
      			'address_2' => $order_info['shipping_address_2'],
      			'city'      => $order_info['shipping_city'],
      			'postcode'  => $order_info['shipping_postcode'],
      			'zone'      => $order_info['shipping_zone'],
				'zone_code' => $order_info['shipping_zone_code'],
				'country'   => $order_info['shipping_country'],
				'telephone' => $order_info['shipping_telephone']
			);

			$this->data['shipping_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));
			$this->data['shipping_method'] = $order_info['shipping_method'];
			$this->data['shipping_time'] = $order_info['shipping_time'];
			$this->data['shipping_district'] = $order_info['shipping_district'];
			$this->data['shipping_district_addr'] = $order_info['shipping_district_addr'];
			
			$this->data['products'] = array();
			
			$products = $this->model_account_order->getOrderProducts($this->request->get['order_id']);

      		foreach ($products as $product) {
				$option_data = array();
				
				$options = $this->model_account_order->getOrderOptions($this->request->get['order_id'], $product['order_product_id']);

         		foreach ($options as $option) {
          			if ($option['type'] != 'file') {
						$value = $option['value'];
					} else {
						$value = utf8_substr($option['value'], 0, utf8_strrpos($option['value'], '.'));
					}
					
					$option_data[] = array(
						'name'  => $option['name'],
						'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
					);					
        		}

        		$this->data['products'][] = array(
          			'name'     => $product['name'],
          			'model'    => $product['model'],
          			'option'   => $option_data,
          			'quantity' => $product['quantity'],
        			'weight' => $product['weight'],
        			'sellunit' => $product['upc'], //销售单位
          			'price'    => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
					'total'    => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']),
					'return'   => $this->url->link('account/return/insert', 'order_id=' . $order_info['order_id'] . '&product_id=' . $product['product_id'], 'SSL')
        		);
      		}

			// Voucher
			$this->data['vouchers'] = array();
			
			$vouchers = $this->model_account_order->getOrderVouchers($this->request->get['order_id']);
			
			foreach ($vouchers as $voucher) {
				$this->data['vouchers'][] = array(
					'description' => $voucher['description'],
					'amount'      => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value'])
				);
			}
			
      		$totals = $this->model_account_order->getOrderTotals($this->request->get['order_id']);
      		foreach($totals as $total) {
      			if ($total['code'] == 'total') {
      				$this->data['totals'][] = $total;
      			}
      		}
			
			$this->data['comment'] = nl2br($order_info['comment']);
			
			$this->data['histories'] = array();

			$results = $this->model_account_order->getOrderHistories($this->request->get['order_id']);

      		foreach ($results as $result) {
        		$this->data['histories'][] = array(
          			'date_added' => $result['date_added'],
          			'status'     => $result['status'],
          			'comment'    => nl2br($result['comment'])
        		);
      		}
      		
	      	$status = $this->model_account_order->getOrderStatus();
	      	foreach($status as $s) {
	      		if ($order_info['order_status_id'] == $s['order_status_id']) {
	      			$this->data['order_status'] = $s['name'];
	      			break;
	      		}
	      	}

	      	if ($order_info['order_status_id'] == 2) {
	      		$this->data['text_pay_btn'] = '微信支付';
	      		$this->data['weixin_payment'] = $this->url->link('weixin/pay', '', 'wxpay');
				$this->data['text_cashpay'] = "货到付款";
				$this->data['url_cashpay'] = $this->url->link('mobile_store/order/cashpay', '', 'wxpay');
	      		$this->session->data['order_info'] = $order_info;
	      	}
      		if ($order_info['order_status_id'] <= 2) {
	      		$this->data['text_cancel_btn'] = '关闭';
	      		$this->data['cancel_order'] = $this->url->link('mobile_store/order/cancel', '', 'wxpay');
	      		$this->session->data['order_info'] = $order_info;
       		}
       		
       		$this->data['order_type'] = $order_info['order_type'];

      		$this->data['continue'] = $this->url->link('mobile_store/order', '', 'SSL');
		
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/mobile_store/order_info.tpl')) {
				$this->template = $this->config->get('config_template') . '/template/mobile_store/order_info.tpl';
			} else {
				$this->template = 'default/template/mobile_store/order_info.tpl';
			}
			
			$this->children = array(
			'mobile_store/titlebar',
			'mobile_store/header',
			'mobile_store/coupon'
			);
								
			$this->response->setOutput($this->render());		
    	} else {
			$this->document->setTitle($this->language->get('text_order'));
			
      		$this->data['heading_title'] = $this->language->get('text_order');

      		$this->data['text_error'] = $this->language->get('text_error');

      		$this->data['button_continue'] = $this->language->get('button_continue');
			
      		$this->data['continue'] = $this->url->link('mobile_store/order', '', 'SSL');
			 			
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/mobile_store/not_found.tpl')) {
				$this->template = $this->config->get('config_template') . '/template/mobile_store/not_found.tpl';
			} else {
				$this->template = 'default//template/mobile_store/not_found.tpl';
			}
			
			$this->children = array(
			'mobile_store/titlebar',
			'mobile_store/header'
			);
								
			$this->response->setOutput($this->render());				
    	}
  	}
  	
  	public function cancel() {
		if (!isset($this->session->data['order_info'])) {
			$this->redirect($this->url->link('mobile_store/home'));
		}
		$order_info = &$this->session->data['order_info'];
  		
		$this->load->model('checkout/order');
		if ($order_info['order_status_id'] < 5) {
			$this->model_checkout_order->orderCancelStatus($order_info);
			$ret = $this->model_checkout_order->fastupdate($order_info['order_id'], $order_info);
		}
		else {
			$ret = true;
		}

		if ($ret != false) {
			$this->session->data['error_msg'] = '订单已取消';
			$this->session->data['url_continue'] = $this->url->link('mobile_store/allproduct', '', 'SSL');
			$this->session->data['text_continue'] = '转到买菜页';
		}
		else {
			$this->session->data['error_msg'] = '订单取消失败';
			$this->session->data['url_continue'] = $this->url->link('mobile_store/order/info', 'order_id='.$order_info['order_id'], 'SSL');
			$this->session->data['text_continue'] = '查看订单';
		}
		$this->redirect($this->url->link('weixin/error'));
  	}
  	
  	public function cashpay() {
		if (!isset($this->session->data['order_info'])) {
			$this->redirect($this->url->link('mobile_store/home'));
		}
		$order_info = &$this->session->data['order_info'];
  		
		$this->load->model('checkout/order');
		if ($order_info['order_status_id'] == 2) {
			$this->model_checkout_order->orderChangeStatus($order_info);
			$order_info['iscash'] = 1;
			$ret = $this->model_checkout_order->fastupdate($order_info['order_id'], $order_info);
		}
		else {
			$ret = false;
		}
		
		$this->sendStatusChgMsg($order_info);

		if ($ret == false) {
			$this->session->data['error_msg'] = '订单无法改为到付';
			$this->session->data['url_continue'] = $this->url->link('mobile_store/allproduct', '', 'SSL');
			$this->session->data['text_continue'] = '转到买菜页';
		}
		else {
			$this->session->data['error_msg'] = '订单已改为到付';
			$this->session->data['url_continue'] = $this->url->link('mobile_store/order/info', 'order_id='.$order_info['order_id'], 'SSL');
			$this->session->data['text_continue'] = '查看订单';
		}
		$this->redirect($this->url->link('weixin/error'));
  	}
  	
  	private function sendStatusChgMsg($order_info) {
		if (isset($this->session->data['openid']))
			$openid = $this->session->data['openid'];
		else {
			$this->log->write("不能发送付款成功消息，因为无openid");
			return;
		}

  		$status = $this->model_checkout_order->getOrderStatus();
  		$content = $status[$order_info['order_status_id']];
  		$msg = sprintf($content->wxmsg, $order_info['order_id'], $this->currency->format($order_info['total']), $order_info['date_added'], $order_info['comment']);
  		
  		$messages = array();
		$messages[0]["title"] = "交易提醒 " . $content->wxtitle;
		$messages[0]["description"] = $msg;
		$messages[0]["url"] = $this->url->link2('mobile_store/order/info', 'order_id='.$order_info['order_id']);
		$messages[0]["picurl"] = "";
		
		$wxtools = new WeixinTools();
		$msg = $wxtools->makeKfMsg($openid, "news", $messages);
		$res = $wxtools->sendKfMsg($msg, $this->access_token);
		if ($res == false) {
			$this->log->write("发送客服提醒消息出错");
		}
		
  	}
}
?>