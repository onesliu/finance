<?php

class ModelWeixinAutoReply extends Model {
	
	public function getReplyFromId($ToUserName, $FromUserName, $msg_id) {
		$q = $this->db->query("select * from ".DB_PREFIX."auto_message where id=".$msg_id);
		if ($q->num_rows > 0) {
			if ($q->row['MsgType'] == 'news') {
				$reply = json_decode($q->row['Items']);
				if ($FromUserName)
					return $this->makeXmlNewsReply($ToUserName, $FromUserName, $reply);
			}
			else if ($q->row['MsgType'] == 'text') {
				$reply = $q->row['Items'];
				if ($FromUserName)
					return $this->makeXmlTextReply($ToUserName, $FromUserName, $reply);
			}
		}
		
		return false;
	}
	
	public function getReply($ToUserName, $FromUserName, $msg_content) {
		if ($msg_content == null)
			return false;

		$q = $this->db->query(sprintf("select * from %sauto_message where (select '%s' regexp pattern)=1;", DB_PREFIX, $msg_content));
		if ($q->num_rows > 0) {
			if ($q->row['MsgType'] == 'news') {
				$reply = json_decode($q->row['Items']);
				if ($FromUserName)
					return $this->makeXmlNewsReply($ToUserName, $FromUserName, $reply);
			}
			else if ($q->row['MsgType'] == 'text') {
				$reply = $q->row['Items'];
				if ($FromUserName)
					return $this->makeXmlTextReply($ToUserName, $FromUserName, $reply);
			}
		}
		
		return false;
	}
	
	public function makeXmlNewsReply($ToUserName, $FromUserName, $items) {
		$xml = sprintf("<xml>
			<ToUserName><![CDATA[%s]]></ToUserName>
			<FromUserName><![CDATA[%s]]></FromUserName>
			<CreateTime>%d</CreateTime>
			<MsgType><![CDATA[news]]></MsgType>
			<ArticleCount>%d</ArticleCount><Articles>", $ToUserName, $FromUserName, time(), count($items));
		foreach($items as $item) {
			$xml .= sprintf("<item>
			<Title><![CDATA[%s]]></Title> 
			<Description><![CDATA[%s]]></Description>
			<PicUrl><![CDATA[%s]]></PicUrl>
			<Url><![CDATA[%s]]></Url>
			</item>", $item->title, $item->description, $item->picurl, $item->url);
		}
		$xml .= "</Articles></xml>";
		return $xml;
	}
	
	public function makeXmlTextReply($ToUserName, $FromUserName, $item) {
		$xml = sprintf("<xml>
			<ToUserName><![CDATA[%s]]></ToUserName>
			<FromUserName><![CDATA[%s]]></FromUserName>
			<CreateTime>%s</CreateTime>
			<MsgType><![CDATA[text]]></MsgType>
			<Content><![CDATA[%s]]></Content>
			</xml>", $ToUserName, $FromUserName, time(), $item);
		return $xml;
	}
	
	public function makeXmlMuService($ToUserName, $FromUserName, $toService = false) {
		$service = '';
		if ($toService != false) {
			$service = "<TransInfo><KfAccount>$toService</KfAccount></TransInfo>";
		}
		$xml = sprintf("<xml>
			<ToUserName><![CDATA[%s]]></ToUserName>
			<FromUserName><![CDATA[%s]]></FromUserName>
			<CreateTime>%s</CreateTime>
			<MsgType><![CDATA[transfer_customer_service]]></MsgType>
			%s
			</xml>", $ToUserName, $FromUserName, time(), $service);
		return $xml;
	}
}
?>