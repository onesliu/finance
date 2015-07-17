<?php

class ModelWeixinAccessToken extends Model {
	protected $access_token;
	protected $expires_in;
	protected $errcode;
	protected $errmsg;
	
	protected $auth2_access_token;
	
	public function get($appid, $secret) {
		$this->save('', 0, 0);
		
		if ($appid == null || $secret == null)
			return false;

		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$secret";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPGET, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.1 Safari/537.11');
		$token = curl_exec($ch);
		$rescode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		$result = json_decode($token);
		
		if (isset($result->access_token)) {
			$this->access_token = $result->access_token;
			$this->expires_in = $result->expires_in;
			$this->save($result->access_token, $result->expires_in, time());
			return $this->access_token;
		}
		else {
			$this->errcode = $result->errcode;
			$this->errmsg = $result->errmsg;
		}
		return false;
	}
	
	public function getTempAccessToken($appid, $secret, $code) {
		if ($appid == null || $secret == null || $code == null)
			return false;

		$url = "https://api.weixin.qq.com/sns/oauth2/access_token?grant_type=authorization_code&appid=$appid&secret=$secret&code=$code";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPGET, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.1 Safari/537.11');
		$token = curl_exec($ch);
		$rescode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		$result = json_decode($token);
		
		if (isset($result->access_token)) {
			$this->auth2_access_token = $result;
			$this->save_oauth_access_token($result->openid, $result->access_token);
			return $result;
		}
		else {
			$this->errcode = $result->errcode;
			$this->errmsg = $result->errmsg;
			return false;
		}
	}
	
	private function save_oauth_access_token($openid, $access_token) {
		$this->db->query("update ". DB_PREFIX ."customer set access_token='$access_token' WHERE	email='$openid'");
	}
	
	function get_oauth_access_token($openid) {
		$query = $this->db->query("SELECT access_token FROM ".DB_PREFIX."customer WHERE email='$openid'");
		return (isset($query->row['access_token']))?$query->row['access_token'] : null;
	}
	
	private function save($access_token, $expire_time, $startime) {
		$this->config->set('weixin_access_token', $access_token);
		$this->config->set('weixin_token_expire', $expire_time);
		$this->config->set('weixin_token_starttime', $startime);
		$data = array('weixin_access_token' => $access_token,
						'weixin_token_expire' => $expire_time,
						'weixin_token_starttime' => $startime);
		$this->model_setting_setting->editSetting('weixin', $data);
		
		$this->log->write("Get weixin access_token: $access_token, $expire_time");
	}
}
?>