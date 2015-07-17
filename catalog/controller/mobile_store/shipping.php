<?php 
class ControllerMobileStoreShipping extends Controller {
	public function index() {
		
  	}
	
	public function saveaddr() {
		if ($this->request->server['REQUEST_METHOD'] != 'POST') {
			return;
		}
		
		$this->load->model('account/district');
		$this->load->model('account/address');
		$this->load->model('account/customer');
		
		$addr['firstname'] = $this->request->post['userName'];
		$addr['telephone'] = $this->request->post['telNumber'];
		$addr['address_1'] = $this->request->post['proviceFirstStageName'].
							 $this->request->post['addressCitySecondStageName'].
							 $this->request->post['addressCountiesThirdStageName'].
							 $this->request->post['addressDetailInfo'];
		$addr['district_id'] = $this->request->post['userName'];
		$addr['lastname'] = '';
		$addr['company'] = '';
		$addr['company_id'] = '';
		$addr['tax_id'] = '';
		$addr['address_2'] = '';
		$addr['postcode'] = $this->request->post['addressPostalCode'];;
		$addr['city'] = $this->request->post['addressCitySecondStageName'];;
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