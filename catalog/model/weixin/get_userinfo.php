<?php

class ModelWeixinGetUserinfo extends Model {
	protected $errcode;
	protected $errmsg;
	
	public function getUserInfo($access_token, $openid) {
		
		if ($access_token == null || $openid == null)
			return false;

		$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$openid&lang=zh_CN";
		$wxtools = new WeixinTools();
		$res = $wxtools->getFromWx($url);
		if ($res == false) {
			$this->log->write("weixin get user info error.");
			return false;
		}

		$result = json_decode($res);
		
		if (isset($result->subscribe)) {
			if ($result->subscribe == 1) {
				$this->save($openid, $result);
				return $result;
			}
			else {
				$this->log->write("tried to get unsubscribe user infomation, openid = $openid");
			}
		}
		else {
			$this->errcode = $result->errcode;
			$this->errmsg = $result->errmsg;
		}
		return false;
	}
	
	public function unSubscribeUser($openid) {
		$this->db->query(sprintf("update %scustomer set subscribe=0, status=0, approved=0 where openid='%s'", DB_PREFIX, $openid));
	}
	
	private function save($openid, $info) {
		$q = $this->db->query(sprintf("select count(*) as cnt from %scustomer where openid='%s'", DB_PREFIX, $openid));
		if ($q->row["cnt"] <= 0) {
			$this->db->query(sprintf("insert into %scustomer
			(subscribe,openid,nickname,sex,city,country,province,language,headimgurl,subscribe_time,
			email,firstname,password,customer_group_id,date_added,status,approved) values (%d, '%s', '%s', %d, '%s',
			'%s', '%s', '%s', '%s', %d, '%s', '%s', SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1('%s'))))),
			1, now(), 1, 1)", DB_PREFIX,
			$info->subscribe, $info->openid, $this->db->escape($info->nickname), $info->sex, $this->db->escape($info->city),
			$this->db->escape($info->country), $this->db->escape($info->province), $this->db->escape($info->language), $info->headimgurl,
			$info->subscribe_time, $info->openid, $this->db->escape($info->nickname), WEIXIN_USERPWD));
		}
		else {
			$this->db->query(sprintf("update %scustomer set nickname='%s', sex=%d, city='%s', country='%s',
			province='%s', language='%s', headimgurl='%s', subscribe_time=%d, firstname='%s', email='%s',
			subscribe=%d, approved=1,
			status=1, date_added=now(), password=SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1('%s')))))
			where openid='%s'",
			DB_PREFIX, $this->db->escape($info->nickname), $info->sex, $this->db->escape($info->city), $this->db->escape($info->country),
			$this->db->escape($info->province), $this->db->escape($info->language), $info->headimgurl, $info->subscribe_time,
			$this->db->escape($info->nickname), $info->openid,
			$info->subscribe, WEIXIN_USERPWD, $openid));
		}
	}
}
?>