<?php 
class ControllerWeixinLogin extends Controller {
	private $error = array();
	
	public function index() {
		// Login override for admin users
		if (isset($this->request->get['jump'])) {
			$redirect = $this->request->get['jump'];
			
			foreach($this->request->get as $key => $val) {
				if ($key != 'route' && $key != 'jump' && $key != 'code' && $key != 'state') {
					$redirect .= "&$key=$val";
				}
			}
		}
		else
			$redirect = 'mobile_store/allproduct';
			
		if (isset($this->request->post['redirect'])) {
			$redirect = $this->request->post['redirect'];
		}
		else if (isset($this->request->get['redirect'])) {
			$redirect = $this->request->get['redirect'];
		}
		
		if (isset($redirect)) {
			$ua = parse_url($redirect);
			if (!isset($ua['host']))
				$redirect = $this->url->link($redirect, '', 'SSL');
		}
		
		if (!empty($this->request->get['token'])) {
			$this->customer->logout();

			unset($this->session->data['wishlist']);
			unset($this->session->data['shipping_address_id']);
			unset($this->session->data['shipping_country_id']);
			unset($this->session->data['shipping_zone_id']);
			unset($this->session->data['shipping_postcode']);
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_address_id']);
			unset($this->session->data['payment_country_id']);
			unset($this->session->data['payment_zone_id']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['comment']);
			unset($this->session->data['order_id']);
			unset($this->session->data['coupon']);
			unset($this->session->data['reward']);
			unset($this->session->data['voucher']);
			unset($this->session->data['vouchers']);
			
			$this->load->model('account/customer');
			
			$customer_info = $this->model_account_customer->getCustomerByToken($this->request->get['token']);
			
		 	if ($customer_info && $this->customer->login($customer_info['email'], '', true)) {
				$this->redirect($redirect); 
			}
		}
		
		if (isset($this->request->get['code'])) {
			$this->load->model('weixin/access_token');
			$this->load->model('setting/setting');
			
			$this->session->data['oauth_code'] = $this->request->get['code'];
			$this->session->data['oauth_state'] = $this->request->get['state'];
			
			$wx = $this->model_setting_setting->getSetting('weixin');
			$ac = $this->model_weixin_access_token->getTempAccessToken($wx['weixin_appid'],
					$wx['weixin_appsecret'], $this->request->get['code']);
			if ($ac != false) {
				$openid = $ac->openid;
				$this->session->data['openid'] = $openid;
				$this->session->data['oauth_access_token'] = $ac->access_token;
			}
			else {
				$this->response->setOutput("微信认证错误，请重试");
				return;
			}
		}
		
		if ($this->customer->isLogged()) {  
      		$this->redirect($redirect);
    	}
    	
    	if (isset($this->request->post['email'])) {
  			$email = $this->request->post['email'];
  		}
  		else if (isset($this->request->get['email'])) {
  			$email = $this->request->get['email'];
  		}
  		else if (isset($openid)) {
  			$email = $openid;
  		}
  		else {
  			$this->response->setOutput("没有登录用户名和密码，请重试");
  			return;
  		}
    	
  		if ($this->customer->login($email, WEIXIN_USERPWD)) {
			unset($this->session->data['guest']);
			
			if (isset($redirect)) {
				$this->redirect($redirect); 
			}
    	}
    	else {
    		$this->response->setOutput("自动登录失败");
    	}
  	}
  
}
?>