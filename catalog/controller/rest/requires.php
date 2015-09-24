<?php
class ControllerRestRequires extends Controller {
	
	public function index() {
		
		$reqs = new stdClass();
		$reqs->result = -1;
		
		if (!$this->customer->isLogged()) {
			$reqs->result = "nologin";
			$this->response->setOutput(json_encode($reqs));
			return;
		}
		$customer_id = $this->customer->getId();
		
		$result = $this->db->query("select require_id,require_group,name from f_require order by class_id,require_id");
		$result2 = $this->db->query("select cr.require_id,cr.rvalue_id,rv.rvalue,cr.rvalue as crvalue 
		from f_customer_require cr left join f_require_value rv on cr.rvalue_id=rv.rvalue_id 
		where cr.customer_id=$customer_id");
		
		foreach($result->rows as &$row) {
			$row["rvalue_id"] = null;
			$row["rvalue"] = null;
			$row["crvalue"] = null;
			foreach($result2->rows as &$row2) {
				if ($row["require_id"] == $row2["require_id"]) {
					$row["rvalue_id"] = $row2["rvalue_id"];
					$row["rvalue"] = $row2["rvalue"];
					$row["crvalue"] = $row2["crvalue"];
				}
			}
		}
		$this->makereq($reqs, $result);
		
		$reqs->result = 0;
		$this->response->setOutput(json_encode($reqs));
	}
	
	public function productreq() {

		$reqs = new stdClass();
		$reqs->result = -1;
		
		if (!$this->customer->isLogged()) {
			$reqs->result = "nologin";
			$this->response->setOutput(json_encode($reqs));
			return;
		}
		$customer_id = $this->customer->getId();
		
		if (!isset($this->request->post["product_id"])) {
			$this->response->setOutput(json_encode($reqs));
			return;
		}
		$product_id = $this->request->post["product_id"];

		$result = $this->make_productreq($customer_id, $product_id);
		$this->makereq($reqs, $result);
			
		$reqs->result = 0;
		$this->response->setOutput(json_encode($reqs));
	}
	
	public function productreq2($args) {
		$customer_id = $args[0];
		$product_id = $args[1];
		$result = $this->make_productreq($customer_id, $product_id);
		$reqs = new stdClass();
		$this->makereq($reqs, $result);
		return $reqs;
	}
	
	public function make_productreq($customer_id, $product_id) {
		$result = $this->db->query("select r.require_id,r.require_group,name 
		from f_product_require pr join f_require r on pr.require_id=r.require_id 
		where pr.product_id=$product_id order by class_id,r.require_id");
		$result2 = $this->db->query("select cr.require_id,cr.rvalue_id,rv.rvalue,cr.rvalue as crvalue 
		from f_customer_require cr left join f_require_value rv on cr.rvalue_id=rv.rvalue_id 
		where cr.customer_id=$customer_id");
		
		foreach($result->rows as &$row) {
			$row["rvalue_id"] = null;
			$row["rvalue"] = null;
			$row["crvalue"] = null;
			foreach($result2->rows as &$row2) {
				if ($row["require_id"] == $row2["require_id"]) {
					$row["rvalue_id"] = $row2["rvalue_id"];
					$row["rvalue"] = $row2["rvalue"];
					$row["crvalue"] = $row2["crvalue"];
				}
			}
		}
		return $result;
	}
	
	public function classreq() {
		$reqs = new stdClass();
		$reqs->result = -1;
		
		if (!$this->customer->isLogged()) {
			$reqs->result = "nologin";
			$this->response->setOutput(json_encode($reqs));
			return;
		}
		$customer_id = $this->customer->getId();
		
		$class_id = 0;
		if (isset($this->request->post["class_id"])) {
			$class_id = $this->request->post["class_id"];
		}
		
		$result = $this->db->query("select require_id,require_group,name from f_require where class_id=$class_id order by require_id");
		$result2 = $this->db->query("select cr.require_id,cr.rvalue_id,rv.rvalue,cr.rvalue as crvalue 
		from f_customer_require cr left join f_require_value rv on cr.rvalue_id=rv.rvalue_id 
		where cr.customer_id=$customer_id");
		
		foreach($result->rows as &$row) {
			$row["rvalue_id"] = null;
			$row["rvalue"] = null;
			$row["crvalue"] = null;
			foreach($result2->rows as &$row2) {
				if ($row["require_id"] == $row2["require_id"]) {
					$row["rvalue_id"] = $row2["rvalue_id"];
					$row["rvalue"] = $row2["rvalue"];
					$row["crvalue"] = $row2["crvalue"];
				}
			}
		}
		$this->makereq($reqs, $result);
		
		$reqs->result = 0;
		$this->response->setOutput(json_encode($reqs));
	}
	
	private function makereq(&$reqs, $rs) {
		$reqs->info = array();
		foreach($rs->rows as $row) {
			$req = new stdClass();
			$req->require_id = $row["require_id"];
			$req->require_group = $row["require_group"];
			$req->name = $row["name"];
			$req->rvalue_id = $row["rvalue_id"];
			$req->rvalue = $row["rvalue"];
			$req->crvalue = $row["crvalue"];
			$req->rvs = array();
			
			$reqs->info[] = $req;
		}
		
		$rvs = $this->db->query("select rvalue_id,value_type,require_group,rvalue from f_require_value");
		if ($rvs == false) {
			$this->response->setOutput(json_encode($product));
			return;
		}
		
		foreach($reqs->info as &$req) {
			foreach($rvs->rows as $rv) {
				if ($req->require_group == $rv["require_group"]) {
					$rvo = new stdClass();
					$rvo->rvalue_id = $rv["rvalue_id"];
					$rvo->rvalue = $rv["rvalue"];
					$req->rvs[] = $rvo;
					$req->value_type = $rv["value_type"];
				}
			}
		}
	}
	
	public function set() {
		
		$ret = new stdClass();
		$ret->result = -1;
		
		if (!$this->customer->isLogged()) {
			$reqs->result = "nologin";
			$this->log->write("未登录");
			$this->response->setOutput(json_encode($reqs));
			return;
		}
		$customer_id = $this->customer->getId();

		if (!isset($this->request->post["require_id"]) || !$this->request->post["rvalue_id"]) {
			$this->log->write("参数不够: require_id, rvalue_id");
			$this->response->setOutput(json_encode($ret));
			return;
		}
		$require_id = $this->request->post["require_id"];
		$rvalue_id = $this->request->post["rvalue_id"];
		if (!is_numeric($require_id) || !is_numeric($rvalue_id)) {
			$this->log->write("参数不是整数: require_id, rvalue_id");
			$this->response->setOutput(json_encode($ret));
			return;
		}
		
		$crvalue = "";
		if (isset($this->request->post["crvalue"]))
			$crvalue = $this->request->post["crvalue"];
		
		if ($this->db->query($sql = "insert into f_customer_require set 
			customer_id=$customer_id, 
			require_id=$require_id, 
			rvalue_id=$rvalue_id,
			rvalue='$crvalue' 
			on duplicate key update 
			rvalue_id=$rvalue_id,
			rvalue='$crvalue'") == false) {
				$this->log->write("sql执行出错：" . $sql);
				$this->response->setOutput(json_encode($ret));
				return;
			}
		
		$ret->result = 0;
		$this->response->setOutput(json_encode($ret));
	}
	
}
?>