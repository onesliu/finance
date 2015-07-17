<?php
class ModelQingyouMenuGroup extends Model {
	public function getGroup($id) {
		$query = $this->db->query("SELECT * from qy_menu_group where id=$id");
		
		return $query->row;
	} 
	
	public function getGroups() {

		$sql = "SELECT * from qy_menu_group order by sort";
		$query = $this->db->query($sql);
		return $query->rows;
	}

	public function getTotalGroups() {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM qy_menu_group");
		
		return $query->row['total'];
	}	
}
?>