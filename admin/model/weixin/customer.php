<?php
class ModelQingyouCustomer extends Model {
	public function updateOpenid($customers) { 
		$this->db->query("update oc_customer set unsubscribe=1");
		foreach($customers as $c) {
			$this->db->query("update oc_customer set unsubscribe=0 where email='$c'");
		}
	}
}
?>