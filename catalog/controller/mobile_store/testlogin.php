<?php 
class ControllerMobileStoreTestlogin extends Controller {
	
	public function index() {
		$email = "oQE_QtyZ3JARRHr33B6vJJBDZBr4";
		$password = WEIXIN_USERPWD;
		$this->customer->login($email, $password);
		$this->redirect($this->url->link('mobile_store/home', '', 'SSL')); 
  	}
}
?>