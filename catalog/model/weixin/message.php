<?php
class ModelWeixinMessage extends Model {  
    
	private $colums;
	private $values;
	
	public function addAll($data) {
		if ($this->parseMessage($data) == true) {
			if ($this->WeixinMsgType == 'event') {
				$this->addEvent();
			}
			else {
				$this->addMessage();
			}
		}
	}
	
    private function addMessage() {
    	if (is_string($this->colums) && is_string($this->values)) {
    		$sql = sprintf("insert into %smessage %s values %s", DB_PREFIX, $this->colums, $this->values);
    		$this->db->query($sql);
    	}
    }
    
    private function addEvent() {
    	if ($this->WeixinFromUserName != '' && $this->WeixinCreateTime != '') {
    		$q = $this->db->query(sprintf("select count(*) as cnt from %sevent where FromUserName='%s' and 
    		CreateTime=%d", DB_PREFIX, $this->WeixinFromUserName, $this->WeixinCreateTime));
    		if ($q->row["cnt"] <= 0) {
    			$sql = sprintf("insert into %sevent %s values %s", DB_PREFIX, $this->colums, $this->values);
    			$this->db->query($sql);
    		}
    	}
    }
    
    public function getMessage($msgid, $type = 'message') {
    	$query = $this->db->query(sprintf("select * from %s%s where id=%d", DB_PREFIX, $type, $msgid));
    	return $query->row;
    }
    
    public function getMessageByCustomer($cid, $type='message') {
    	$query = $this->db->query(sprintf("select * from %s%s where customerid=%d", DB_PREFIX, $type, $cid));
    	return $query->rows;
    }
    
    private function parseMessage($xmlstr) {
    	$xmlDoc = new DOMDocument();
		if ($xmlDoc->loadXML($xmlstr) == false)
			return false;
			
		$fields = array('ToUserName', 'FromUserName', 'CreateTime', 'MsgType',
			'Content', 'MsgId', 'PicUrl', 'MediaId', 'Format', 'ThumbMediaId',
			'Location_X', 'Location_Y', 'Scale', 'Label', 'Title', 'Description',
			'Url', 'Event', 'EventKey', 'Ticket', 'Latitude', 'Longitude', 'Precision');
		
		$x = $xmlDoc->documentElement;
		$this->colums = '(';
		$this->values = '(';
		$other_values = '';
		foreach ($x->childNodes as $item)
		{
			if ($item->nodeName != "#text") {
				if (in_array($item->nodeName, $fields)) {
					$this->colums .= $item->nodeName.',';
					if (is_numeric($item->nodeValue))
						$this->values .= $item->nodeValue.',';
					else
						$this->values .= '\''.$item->nodeValue.'\',';
				}
				else {
					$other_values .= $item->nodeName.'='.$item->nodeValue.'\n';
				}
				
				$this->{'Weixin'.$item->nodeName} = $item->nodeValue;
			}
		}
		
		if ($other_values == '') {
			$this->colums = trim($this->colums, ',');
			$this->values = trim($this->values, ',');
		}
		else {
			$this->colums .= 'Others';
			$this->values .= '\''.$other_values.'\'';
		}
		$this->colums .= ')';
		$this->values .= ')';
		
		return true;
    } 
}
?>