<?php  
class ControllerRestApply extends Controller {
	public function index() {

		$this->template = 'default/template/rest/apply.tpl';
		$this->children = array(
			'rest/header'
		);

		$this->response->setOutput($this->render());
	}
	
	public function newapplist() {
		$ret = new stdClass();
		$ret->result = -1;

		if (!$this->user->isLogged()) {
			$ret->result = "nologin";
			$this->response->setOutput(json_encode($ret));
			return;
		}
		
		$result = $this->db->query($sql = "select a.*, p.*, 
			c.firstname as cname,c.telephone as cphone,
			(select step_name from f_product_step where product_id=a.product_id and step_id=a.cur_step_id) as step_name
			from f_application a 
			join f_product p on a.product_id=p.product_id 
			join oc_customer c on a.customer_id=c.customer_id 
			where a.user_id = 0 
			order by date_added desc");
			
		$ret->info = $this->make_app_item($result, true);
		
		$ret->result = 0;
		$this->response->setOutput(json_encode($ret));
	}
	
	private $no_image;
	
	private function make_app_item($result, $is_user = false) {
			
		$this->load->model('tool/image');
		$no_image = $this->model_tool_image->resize('no_image.jpg', $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
		$this->no_image = $no_image;

		$arr = array();
		foreach($result->rows as $row) {
			if ($row['product_img']) {
				$image = $this->model_tool_image->resize($row['product_img'], $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
			} else {
				$image = $no_image;
			}

			$app = new stdClass();
			$app->app_id = $row["app_id"];
			$app->product_id = $row["product_id"];
			$app->product_name = $row["name"];
			$app->category1 = $row["category1"];
			$app->category2 = $row["category2"];
			$app->customer_id = $row["customer_id"];
			$app->customer_name = ($row["cname"])?$row["cname"]:"";
			$app->customer_phone = ($row["cphone"])?$row["cphone"]:"";
			$app->app_status = $row["app_status"];
			$app->appstatus = $this->make_status($app->app_status);
			$app->cur_step_id = $row["cur_step_id"];
			$app->cur_step_name = ($row["step_name"])?$row["step_name"]:"";
			$app->step_status = $row["step_status"];
			$app->stepstatus = $this->make_status($app->step_status);
			$app->date_added = ($row["date_added"])?$row["date_added"]:"";
			$app->date_over = ($row["date_over"])?$row["date_over"]:"";
			$app->rst_limit = ($row["rst_amount"])?$row["rst_amount"]:"";
			$app->rst_period = ($row["rst_period"])?$row["rst_period"]:"";
			$app->rst_rate = ($row["rst_rate"])?$row["rst_rate"]:"";
			$app->image = $image;
			$app->reqs = $this->make_requires($app->customer_id, $app->product_id);
			if (isset($row["uname"])) {
				$app->user_id = $row["user_id"];
				$app->user_name = ($row["uname"])?$row["uname"]:"";
				$app->user_phone = ($row["uphone"])?$row["uphone"]:"";
			}
			if ($is_user) {
				$app->belong = ($row["belong"])?$row["belong"]:"";
				$app->material = ($row["material"])?$row["material"]:"";
				$app->remark = ($row["remark"])?$row["remark"]:"";
				$app->steps = $this->get_appstep($app->product_id, $app->cur_step_id, $app->step_status);
			}
			
			$arr[] = $app;
		}
		
		return $arr;
	}
	
	private function make_requires($customer_id, $product_id) {
		$requires = $this->call('rest/requires/productreq2', array($customer_id, $product_id));
		$reqs = array();
		foreach($requires->info as $req) {
			$r = new stdClass();
			$r->name = $req->name;
			switch($req->value_type) {
				case "number": $r->value = ($req->crvalue)?$req->crvalue:"未填"; break;
				default: $r->value = ($req->rvalue)?$req->rvalue:"未填"; break;
			}
			$reqs[] = $r;
		}
		return $reqs;
	}
	
	private function make_status($status) {
		$ret = "";
		switch ($status) {
			case 0: $ret = "待处理"; break;
			case 1: $ret = "处理中"; break;
			case 2: $ret = "已完成"; break;
			case 3: $ret = "已中止"; break;
		}
		return $ret;
	}
	
	public function customerlist() {
		$ret = new stdClass();
		$ret->result = -1;
		
		if (!$this->customer->isLogged()) {
			$ret->result = "nologin";
			$this->response->setOutput(json_encode($ret));
			return;
		}
		$customer_id = $this->customer->getId();
		
		$result = $this->db->query($sql = "select a.*, p.*, 
			u.firstname as uname, u.telephone as uphone, 
			c.firstname as cname,c.telephone as cphone,
			(select step_name from f_product_step where product_id=a.product_id and step_id=a.cur_step_id) as step_name
			from f_application a 
			join f_product p on a.product_id=p.product_id 
			left outer join oc_user u on a.user_id=u.user_id 
			join oc_customer c on a.customer_id=c.customer_id 
			where a.customer_id=$customer_id 
			order by date_added desc");
		$ret->info = $this->make_app_item($result, false);
		$ret->no_image = $this->no_image;

		$ret->result = 0;
		$this->response->setOutput(json_encode($ret));
	}
	
	public function userlist() {
		$ret = new stdClass();
		$ret->result = -1;
		
		if ($this->user->isLogged()) {
			$userinfo = $this->user->getUserInfo();
			if ($userinfo["usertype"] != 0) {
				$ret->result = "nologin";
				$this->response->setOutput(json_encode($ret));
				return;
			}
		}
		else {
			$ret->result = "nologin";
			$this->response->setOutput(json_encode($ret));
			return;
		}
		$user_id = $this->user->getId();
		
		$result = $this->db->query("select a.*, p.*, 
			u.firstname as uname, u.telephone as uphone, 
			c.firstname as cname,c.telephone as cphone,
			(select step_name from f_product_step where product_id=a.product_id and step_id=a.cur_step_id) as step_name
			from f_application a 
			join f_product p on a.product_id=p.product_id 
			left outer join oc_user u on a.user_id=u.user_id 
			join oc_customer c on a.customer_id=c.customer_id 
			where a.user_id=$user_id 
			order by date_added desc");
			
		$ret->info = $this->make_app_item($result, true);

		$ret->result = 0;
		$this->response->setOutput(json_encode($ret));
	}
	
	private function escstr($s) {
		return ($s)?$s:"";
	}
	
	public function appdetail() {
		$ret = new stdClass();
		$ret->result = -1;

		if (!$this->customer->isLogged() && !$this->user->isLogged()) {
			$ret->result = "nologin";
			$this->response->setOutput(json_encode($ret));
			return;
		}
		
		$is_user = false;
		if ($this->user->isLogged()) {
			$userinfo = $this->user->getUserInfo();
			if ($userinfo["usertype"] == 0)
				$is_user = true;
		}
		
		if (!isset($this->request->post["app_id"])) {
			$ret->result = "参数不正确";
			$this->response->setOutput(json_encode($ret));
			return;
		}
		$app_id = $this->request->post["app_id"];
		
		$result = $this->db->query("select a.*, p.*, u.telephone as uphone, u.firstname as uname, c.firstname as cname,c.telephone as cphone,
		(select step_name from f_product_step where product_id=a.product_id and step_id=a.cur_step_id) as step_name
			from f_application a 
			join f_product p on a.product_id=p.product_id 
			left outer join oc_user u on a.user_id=u.user_id 
			join oc_customer c on a.customer_id=c.customer_id 
			where a.app_id=$app_id");
		if ($result->num_rows == 1) {
			$row = $result->row;
			$ret->app_id = $row["app_id"];
			$ret->product_id = $row["product_id"];
			$ret->product_name = $row["name"];
			$ret->category1 = $row["category1"];
			$ret->category2 = $row["category2"];
			$ret->material = $this->escstr($row["material"]);
			$ret->remark = $this->escstr($row["remark"]);
			$ret->user_id = $row["user_id"];
			$ret->user_name = $this->escstr($row["uname"]);
			$ret->user_phone = $this->escstr($row["uphone"]);
			$ret->customer_id = $row["customer_id"];
			$ret->customer_name = $this->escstr($row["cname"]);
			$ret->customer_phone = $this->escstr($row["cphone"]);
			$ret->app_status = $row["app_status"];
			$ret->appstatus = $this->make_status($ret->app_status);
			$ret->cur_step_id = $row["cur_step_id"];
			$ret->cur_step_name = $this->escstr($row["step_name"]);
			$ret->step_status = $row["step_status"];
			$ret->stepstatus = $this->make_status($ret->step_status);
			$ret->date_added = $this->escstr($row["date_added"]);
			$ret->date_over = $this->escstr($row["date_over"]);
			$ret->rst_limit = $this->escstr($row["rst_amount"]);
			$ret->rst_period = $this->escstr($row["rst_period"]);
			$ret->rst_rate = $this->escstr($row["rst_rate"]);
			if ($is_user) {
				$ret->belong = $this->escstr($row["belong"]);
			}
			
			$ret->steps = $this->get_appstep($ret->product_id, $ret->cur_step_id, $ret->step_status);
			$ret->reqs = $this->make_requires($ret->customer_id, $ret->product_id);
		}
		else {
			$ret->result = "查不到指定的申请：$app_id";
			$this->response->setOutput(json_encode($ret));
			return;
		}
		
		$ret->result = 0;
		$this->response->setOutput(json_encode($ret));
	}
	
	public function appstep() {
		$ret = new stdClass();
		$ret->result = -1;
		
		if (!$this->customer->isLogged()) {
			$ret->result = "nologin";
			$this->response->setOutput(json_encode($ret));
			return;
		}
		
		if (!isset($this->request->post["app_id"])) {
			$ret->result = "参数不正确";
			$this->response->setOutput(json_encode($ret));
			return;
		}
		$app_id = $this->request->post["app_id"];
		
		$result = $this->db->query("select * from f_application where app_id=$app_id");
		if ($result->num_rows <= 0) {
			$ret->result = "参数不正确";
			$this->response->setOutput(json_encode($ret));
			return;
		}
		$product_id = $result->row["product_id"];
		$cur_step_id = $result->row["cur_step_id"];
		$step_status = $result->row["step_status"];
		
		$ret->info = $this->get_appstep($product_id, $cur_step_id, $step_status);
		$ret->app_id = $app_id;
		
		$ret->result = 0;
		$this->response->setOutput(json_encode($ret));
	}
	
	private function get_appstep($product_id, $cur_step_id, $step_status) {
		
		$info = array();
		$result = $this->db->query("select * from f_product_step where product_id=$product_id order by step_id asc");
		foreach($result->rows as $row) {
			$step = new stdClass();
			$step->step_id = $row["step_id"];
			$step->step_name = $row["step_name"];
			$step->step_text = $row["step_text"];
			if ($step->step_id < $cur_step_id) {
				$step->step_status = 2;
			} else if ($step->step_id == $cur_step_id) {
				if ($step_status > 1)
					$step->step_status = $step_status;
				else
					$step->step_status = 1;
			} else {
				$step->step_status = 0;
			}
			$step->stepstatus = $this->make_status($step->step_status);
			
			$info[] = $step;
		}
		
		return $info;
	}

	public function apply() {
		$ret = new stdClass();
		$ret->result = -1;

		if (!$this->customer->isLogged()) {
			$ret->result = "未登录";
			$this->response->setOutput(json_encode($ret));
			return;
		}
		$customer_id = $this->customer->getId();
		
		if (!isset($this->request->post["product_id"]) ||
			!isset($this->request->post["limit"]) ||
			!isset($this->request->post["period"])) {
			$ret->result = "参数不正确";
			$this->response->setOutput(json_encode($ret));
			return;
		}
		$product_id = $this->request->post["product_id"];
		$limit = $this->request->post["limit"];
		$period = $this->request->post["period"];
		
		if (!is_numeric($product_id) || !is_numeric($limit) || !is_numeric($period)) {
			$ret->result = "参数不正确";
			$this->response->setOutput(json_encode($ret));
			return;
		}
		
		$result = $this->db->query("select app_id from f_application where product_id=$product_id 
			and customer_id=$customer_id");
		if ($result->num_rows > 0) {
			$ret->result = "您已申请该产品";
			$ret->app_id = $result->row["app_id"];
			$this->response->setOutput(json_encode($ret));
			return;
		}
		
		$this->db->query("insert into f_application set 
			product_id=$product_id,
			customer_id = $customer_id,
			user_id = 0,
			app_status = 0,
			step_status = 0,
			date_added = now(),
			rst_amount = $limit,
			rst_period = $period
			");
			
		$ret->app_id = $this->db->getLastId();
		
		$ret->result = 0;
		$this->response->setOutput(json_encode($ret));
	}
	
	public function accept() {
		$ret = new stdClass();
		$ret->result = -1;

		if (!$this->user->isLogged()) {
			$ret->result = "nologin";
			$this->response->setOutput(json_encode($ret));
			return;
		}
		$user_id = $this->user->getId();
		
		if (!isset($this->request->post["app_id"])) {
			$ret->result = "参数不正确";
			$this->response->setOutput(json_encode($ret));
			return;
		}
		$app_id = $this->request->post["app_id"];
		
		$init_status = ",app_status=1,cur_step_id=1,step_status=1";
		if (isset($this->request->post["changeuser"])) {
			$init_status = "";
			$user_id = $this->request->post["changeuser"];
		}
		
		$this->db->begin();
		$r = $this->db->query("select * from f_application where app_id=$app_id for update");
		if ($r == false || $r->num_rows != 1 || $r->row["user_id"] > 0) {
			$this->db->rollback();
			$ret->result = "执行错误";
			if (isset($r->row))
				$ret->user_id = $r->row["user_id"];
			$this->log->write($ret->result);
			$this->response->setOutput(json_encode($ret));
			return;
		}
		$this->db->query("update f_application set user_id=$user_id $init_status where app_id=$app_id");
		$this->db->commit();

		$ret->result = 0;
		$this->response->setOutput(json_encode($ret));
	}
	
	public function chgstep() {
		$ret = new stdClass();
		$ret->result = -1;

		if (!$this->user->isLogged()) {
			$ret->result = "nologin";
			$this->response->setOutput(json_encode($ret));
			return;
		}
		$user_id = $this->user->getId();
		
		if (!isset($this->request->post["app_id"]) || !isset($this->request->post["step_id"]) ||
			!isset($this->request->post["action"])) {
			$ret->result = "参数不正确";
			$this->response->setOutput(json_encode($ret));
			return;
		}
		$app_id = $this->request->post["app_id"];
		$step_id = $this->request->post["step_id"];
		$action = $this->request->post["action"];
		
		$this->db->begin();
		$app = $this->db->query("select * from f_application where app_id=$app_id for update");
		if ($app->num_rows != 1) {
			$this->db->rollback();
			$ret->result = "申请没找到";
			$this->response->setOutput(json_encode($ret));
			return;
		}
		if ($app->row["app_status"] == 0) {
			$this->db->rollback();
			$ret->result = "尚未被接单";
			$this->response->setOutput(json_encode($ret));
			return;
		}
		$product_id = $app->row["product_id"];
		
		$step = $this->db->query("select max(step_id) as maxstepid from f_product_step where product_id=$product_id");
		if ($step->row["maxstepid"] > 0)
			$maxstepid = $step->row["maxstepid"];
		else {
			$this->db->rollback();
			$ret->result = "步骤错误";
			$this->response->setOutput(json_encode($ret));
			return;
		}
		
		if ($action == "fin") {
			if ($step_id >= $maxstepid) {
				$cur_step_id = $step_id + 1;
				$app_status = 2;
				$step_status = 2;
			}
			else {
				$cur_step_id = $step_id + 1;
				$app_status = 1;
				$step_status = 1;
			}
		} else if ($action == "stop") {
			$cur_step_id = $step_id;
			$app_status = 3;
			$step_status = 3;
		} else {
			$this->db->rollback();
			$ret->result = "action错误";
			$this->response->setOutput(json_encode($ret));
			return;
		}
		
		$this->db->query("update f_application set cur_step_id=$cur_step_id,
			app_status=$app_status, step_status=$step_status
			where app_id=$app_id");
		$this->db->commit();
		
		$ret->result = 0;
		$ret->cur_step_id = $cur_step_id;
		$ret->app_status = $app_status;
		$ret->step_status = $step_status;
		$this->response->setOutput(json_encode($ret));
	}
}
?>