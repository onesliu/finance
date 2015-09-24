<?php 
class ControllerRestUser extends Controller {
	private $error = array();
	
	public function index() {
		
		$ret = new stdClass();
		$ret->result = -1;

		$ret->user = "";
		if ($this->customer->isLogged()) {
			$ret->user = $this->customer->getTelephone();
			$ret->customerid = $this->customer->getId();
			$ret->email = $this->customer->getEmail();
			$ret->name = $this->customer->getFirstName();
			$ret->result = 0;
		}
		
		if (isset($this->session->data["logintype"])) {
			$ret->usertype = $this->session->data["logintype"];
			$ret->typename = $this->typename($ret->usertype);
			if ($ret->usertype > 0 && $this->user->isLogged()) {
				$ret->userid = $this->user->getId();
			}
		}
		
		$this->response->setOutput(json_encode($ret));
  	}
	
	private function typename($typeid) {
		$typename = "普通客户";
		switch($typeid) {
			case 0: $typename = "普通客户"; break;
			case 1: $typename = "业务员"; break;
			case 2: $typename = "代理商会员"; break;
		}
		return $typename;
	}
	
	public function login() {
		
		$ret = new stdClass();
		$ret->result = 0;
		
		if (isset($this->request->post['user']) && isset($this->request->post['password'])) {
			$user = $this->request->post['user'];
			$password = $this->request->post['password'];
		}
		else {
			$ret->result = -1;
			$this->response->setOutput(json_encode($ret));
			return;
		}
		
		$ret->user = $user;
		
		if ($this->customer->login($user, $password)) {
			unset($this->session->data['guest']);
			$this->session->data["logintype"] = 0;
			$this->session->data["loginuser"] = $user;
			$ret->usertype = 0;
			$ret->customerid = $this->customer->getId();
			$ret->email = $this->customer->getEmail();
			$ret->name = $this->customer->getFirstName();

			if ($this->user->login($this->customer->getTelephone(), $password)) {
				$userinfo = $this->user->getUserInfo();
				$ret->user = $this->customer->getTelephone();
				$ret->usertype = $userinfo["usertype"];
				$this->session->data["logintype"] = $userinfo["usertype"];
				$this->session->data["loginuser"] = $ret->user;
				$ret->userid = $this->user->getId();
			}
			
			$ret->typename = $this->typename($ret->usertype);
		}
		else {
			$ret->result = -1;
		}
		$this->response->setOutput(json_encode($ret));
	}
	
	public function logout() {
		
		$ret = new stdClass();
		$ret->result = 0;
		
		unset($this->session->data["logintype"]);
		unset($this->session->data["loginuser"]);
		
		if ($this->customer->isLogged())
			$this->customer->logout();
		if ($this->user->isLogged())
			$this->user->logout();
		
		$this->response->setOutput(json_encode($ret));
	}
	
	public function saveinfo() {
		$ret = new stdClass();
		$ret->result = -1;

		if (!$this->customer->isLogged()) {
			$ret->error = "没有登录";
			$this->log->write($ret->error);
  			$this->response->setOutput(json_encode($ret));
			return;
		}
		$customer_id = $this->customer->getId();
		
		$username = "";
		if (isset($this->request->post['username']))
			$username = $this->request->post['username'];
		
		if (!isset($this->request->post['email'])) {
			$ret->error = "没有电子邮件";
			$this->log->write($ret->error);
  			$this->response->setOutput(json_encode($ret));
			return;
		}
		$email = $this->request->post['email'];
		if (preg_match("/([a-z0-9]*[-_\.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[\.][a-z]{2,3}([\.][a-z]{2})?/i", $email) == false) {
			$ret->error = "电子邮件格式不正确";
			$this->log->write($ret->error);
  			$this->response->setOutput(json_encode($ret));
			return;
		}
		
		$username = $this->db->escape($username);
		$email = $this->db->escape($email);
		$this->db->query("update oc_customer set firstname='$username', email='$email'
			where customer_id=$customer_id");
			
		if ($this->user->isLogged()) {
			$user_id = $this->user->getId();
			$this->db->query("update oc_user set firstname='$username', email='$email'
			where user_id=$user_id");
		}
		
		$ret->result = 0;
		$this->response->setOutput(json_encode($ret));
	}
	
	public function changepwd() {
		$ret = new stdClass();
		$ret->result = -1;
		
		if (!isset($this->request->post['newpwd']) || $this->request->post['newpwd'] == "") {
			$ret->error = "没有新密码";
			$this->log->write($ret->error);
  			$this->response->setOutput(json_encode($ret));
			return;
		}
		$newpwd = $this->request->post['newpwd'];

		if ($this->customer->isLogged()) {
			$this->load->model('account/customer');
			$this->model_account_customer->editPassword($this->customer->getId(), $newpwd);
			$ret->result = 0;
		}
		
		if ($this->user->isLogged()) {
			$this->load->model('account/user');
			$this->model_account_user->editPassword($this->user->getId(), $newpwd);
			$ret->result = 0;
		}

		if ($ret->result != 0) {
			$ret->error = "修改密码失败";
			$this->log->write($ret->error);
		}
		
		$this->response->setOutput(json_encode($ret));
	}
	
	public function register() {
		$ret = new stdClass();
		$ret->result = -1;
		
		if (!isset($this->request->post['telephone']) ||
			!isset($this->request->post['password']) ||
			!isset($this->request->post['email']) ||
			!isset($this->request->post['username']) ||
			!isset($this->request->post['invitecode'])) {
			$ret->error = "注册信息不全";
			$this->log->write($ret->error);
  			$this->response->setOutput(json_encode($ret));
			return;
  		}
		
		$data = array();
		$data['telephone'] = $this->request->post['telephone'];
		$data['username'] = $this->request->post['username'];
		$data['password'] = $this->request->post['password'];
		$data['email'] = $this->request->post['email'];
		$data['invitecode'] = $this->request->post['invitecode'];
		
		if (preg_match("/1[3458]{1}\d{9}$/", $data['telephone']) == false) {
			$ret->error = "手机号码不正确";
			$this->log->write($ret->error);
  			$this->response->setOutput(json_encode($ret));
			return;
		}
		
		if (preg_match("/([a-z0-9]*[-_\.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[\.][a-z]{2,3}([\.][a-z]{2})?/i", $data['email']) == false) {
			$ret->error = "电子邮件格式不正确";
			$this->log->write($ret->error);
  			$this->response->setOutput(json_encode($ret));
			return;
		}
		
		$invitecode = $data['invitecode'];
		if (preg_match("/1[3458]{1}\d{9}$/", $invitecode)) { //电话邀请码，客户注册
			$result = $this->db->query("select user_id from oc_user where username='$invitecode'");
			$result2 = $this->db->query("select user_id from oc_customer where telephone='$invitecode'");
			if ($result->num_rows < 1) {
				if ($result2->num_rows != 1) {
					$ret->error = "电话邀请码不存在：$invitecode";
					$this->log->write($ret->error);
					$this->response->setOutput(json_encode($ret));
					return;
				} else {
					$data["userid"] = $result2->row['user_id'];
				}
			} else if ($result->num_rows == 1) {
				$data["userid"] = $result->row['user_id'];
			} else {
				$ret->error = "电话邀请码内部重复：$invitecode";
				$this->log->write($ret->error);
				$this->response->setOutput(json_encode($ret));
				return;
			}
			$data["usertype"] = 0;

			$this->load->model('account/customer');
			
			//客户注册
			$customerid = $this->model_account_customer->add_customer($data);
			if (!is_int($customerid)) {
				$ret->error = "客户注册失败：$customerid";
				$this->log->write($ret->error);
				$this->response->setOutput(json_encode($ret));
				return;
			}
		}
		else {
			$codeinfo = $this->invitecode->codeInfo($data['invitecode']);
			if ($codeinfo == false) {
				$ret->error = "邀请码不正确";
				$this->log->write($ret->error);
				$this->response->setOutput(json_encode($ret));
				return;
			}
			if ($codeinfo["hasused"] > 0) {
				$ret->error = "邀请码已被使用";
				$this->log->write($ret->error);
				$this->response->setOutput(json_encode($ret));
				return;
			}
			
			$usertype = $codeinfo["usertype"];
			if ($usertype === false) {
				$ret->error = "邀请码不正确";
				$this->log->write($ret->error);
				$this->response->setOutput(json_encode($ret));
				return;
			} else if ($usertype < 0 || $usertype > 2) {
				$ret->error = "不是正确的邀请码";
				$this->log->write($ret->error);
				$this->response->setOutput(json_encode($ret));
				return;
			}
			
			$data["usertype"] = $usertype;
			$data["userid"] = $codeinfo["userpid"];

			$this->load->model('account/customer');
			$this->load->model('account/user');
			
			if ($usertype > 0) { //业务员、代理商后台注册
				$userid = $this->model_account_user->add_user($data);
				if (!is_int($userid)) {
					$ret->error = "用户注册失败：".$userid;
					$this->log->write($ret->error);
					$this->response->setOutput(json_encode($ret));
					return;
				}
				$data["userid"] = $userid;
			}
			//客户注册
			$customerid = $this->model_account_customer->add_customer($data);
			if (!is_int($customerid)) {
				$ret->error = "客户注册失败：".$customerid;
				$this->log->write($ret->error);
				$this->response->setOutput(json_encode($ret));
				return;
			}
			
			$this->invitecode->useCode($data['invitecode'], $customerid);
		}
		
		$ret->result = 0;
		$this->response->setOutput(json_encode($ret));
	}
	
	public function userlevel() {
		$ret = new stdClass();
		$ret->result = -1;

		if (!$this->customer->isLogged()) {
			$reqs->result = "nologin";
			$this->response->setOutput(json_encode($ret));
			return;
		}
		$userid = $this->customer->getTelephone();

		if (isset($this->request->post["userid"]) && $this->request->post["userid"] > 0)
			$userid = $this->request->post["userid"];
		
		$this->load->model('tool/image');
		$no_image = $this->model_tool_image->resize('no_image.jpg', $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
		
		$users = array();
		$result = $this->db->query("select user_id, username, email, firstname, user_pid, usertype, user_group_id, date_added, (select count(*) from oc_user where user_pid = u.user_id and usertype > 0) as user_cnt, (select count(*) from oc_customer where user_id=u.user_id and telephone not in (select username from oc_user)) as customer_cnt from oc_user u where user_pid = (select user_id from oc_user where username='$userid') and usertype > 0 order by usertype desc;");
		foreach ($result->rows as $row) {
			$u = new stdClass();
			$u->userid = $row["username"];
			$u->name = $row["firstname"];
			$u->email = $row["email"];
			$u->usertype = $row["usertype"];
			$u->typename = $this->typename($u->usertype);
			$u->date_added = $row["date_added"];
			$u->subcnt = $row["user_cnt"] + $row["customer_cnt"];
			if ($u->usertype == 2)
				$u->image = $this->model_tool_image->resize('data/business_users.jpg', $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
			else
				$u->image = $this->model_tool_image->resize('data/business_user.jpg', $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
			$u->no_image = $no_image;
			$users[] = $u;
		}
		
		$result = $this->db->query("select customer_id,firstname,email,telephone,user_id,date_added from oc_customer where user_id=(select user_id from oc_user where username='$userid') and telephone not in (select username from oc_user)");
		foreach ($result->rows as $row) {
			$u = new stdClass();
			$u->userid = $row["telephone"];
			$u->name = $row["firstname"];
			$u->email = $row["email"];
			$u->usertype = 0;
			$u->typename = $this->typename($u->usertype);
			$u->date_added = $row["date_added"];
			$u->subcnt = 0;
			$u->image = $this->model_tool_image->resize('data/customer.jpg', $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
			$u->no_image = $no_image;
			$users[] = $u;
		}
		
		$ret->info = $users;
		$ret->cnt = count($users);
		$ret->result = 0;
		$this->response->setOutput(json_encode($ret));
	}
}
?>