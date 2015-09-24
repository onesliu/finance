<?php  
class ControllerRestInvitecode extends Controller {  
  	public function index() {
		
    	$return = new stdClass();
		$return->result = -1;
		$return->msg = "获取邀请码出错";
		
		if (!$this->user->isLogged()) {
			$return->msg = "用户未登录";
			$this->response->setOutput(json_encode($return));
			return;
		}
		
		if (!isset($this->request->post['usertype'])) {
			$usertype = 0;
		}
		else {
			$usertype = $this->request->post['usertype'];
		}
	
		$user = $this->user->getUserInfo();
		$userid = $user['user_id'];
		$code = $this->invitecode->getCode($userid, $usertype);
		if ($code == false) {
			$return->msg = "获取邀请码出错";
		} else if ($code == 'FULL') {
			$return->msg = "邀请码已用完";
		} else {
			$return->result = 0;
			$return->code = $code;
		}

    	$this->response->setOutput(json_encode($return));
  	}
	
}
?>