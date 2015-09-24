<?php
class ControllerRestRequires extends Controller {
	public function index() {
		
		$reqs = new stdClass();
		$reqs->result = -1;
		
		$rs = $this->db->query("select r.require_id,r.require_group,name,cr.rvalue_id,rv.rvalue 
			from f_require r left outer join f_customer_require cr on r.require_id=cr.require_id
			left join f_require_value rv on cr.rvalue_id=rv.rvalue_id");
		if ($rs == false) {
			$this->response->setOutput(json_encode($product));
			return;
		}
		
		$reqs->info = array();
		foreach($rs->rows as $row) {
			$req = new stdClass();
			$req->require_id = $row["require_id"];
			$req->require_group = $row["require_group"];
			$req->name = $row["name"];
			$req->rvalue_id = $row["rvalue_id"];
			$req->rvalue = $row["rvalue"];
			$req->rvs = array();
			
			$reqs->info[] = $req;
		}
		
		$rvs = $this->db->query("select rvalue_id,require_group,rvalue from f_require_value");
		if ($rvs == false) {
			$this->response->setOutput(json_encode($product));
			return;
		}
		
		foreach($reqs->info as &$req) {
			foreach($rvs->rows as $rv) {
				if ($req->require_group == $rv["require_group"]) {
					$req->rvs[$rv["rvalue_id"]] = $rv["rvalue"];
				}
			}
		}
		
		$reqs->result = 0;
		$this->response->setOutput(json_encode($reqs));
	}
	
	private function get_require($product_id, $customer_id) {

		$prs = $this->db->query("select pr.require_id,name,require_group from f_product_require pr join f_require r on 
			pr.require_id=r.require_id where product_id=$product_id");
		if ($prs == false || $prs->num_rows <= 0) return null;
		$reqs = array();
		foreach($prs->rows as $pr) {
			$req = new stdClass();
			$req->require_id = $pr["require_id"];
			
			$rs = $this->db->query("select cr.rvalue_id,rvalue from f_customer_require cr 
				left join f_require_value rv on cr.rvalue_id=rv.rvalue_id 
				where customer_id=$customer_id and require_id=".$pr["require_id"]);
			if ($rs != false && $rs->num_rows > 0) {
				$req->rvalue_id = $rs->row["rvalue_id"];
				$req->rvalue = $rs->row["rvalue"];
			}
			else {
				$req->rvalue_id = 0;
				$req->rvalue = "";
			}
			
			$reqs[] = $req;
		}
		
		return $reqs;
	}
	
	public function set_require() {
		
		$ret = new stdClass();
		$ret->result = -1;
		
		if (!$this->customer->isLogged()) {
			$this->redirect($this->url->link("rest/user"));
			return;
		}
		$customer_id = $this->customer->getId();

		if (!isset($this->request->post["require"]) || !is_array($this->request->post["require"])) {
			$this->response->setOutput(json_encode($ret));
			return;
		}
		$require = $this->request->post["require"];
		
		foreach($require as $rid => $rval) {
			if ($this->db->query("insert into f_customer_require set 
				customer_id=$customer_id, 
				require_id=$rid, 
				rvalue_id=$rval
				on duplicate key update 
				rvalue_id=$rval") == false) {
					$this->response->setOutput(json_encode($ret));
					return;
				}
		}
		
		$ret->result = 0;
		$this->response->setOutput(json_encode($ret));
	}
	
}
?>