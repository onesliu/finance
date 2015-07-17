<?php
class ModelQingyouMenu extends Model {

	public function getMenu($id) {
		$query = $this->db->query("SELECT * from qy_menu where id=$id");
		
		return $query->row;
	} 
	
	public function getMenus($groupid = 0) {

		if ($groupid == 0)
			$sql = "select * from qy_menu order by sort";
		else
			$sql = "select * from qy_menu m join qy_rel_menu_group g on m.id=g.menu_id where g.menu_group_id=$groupid order by m.sort";
		$query = $this->db->query($sql);
		return $query->rows;
	}
				
	public function getTotalMenus() {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM qy_menu");
		
		return $query->row['total'];
	}
	
	public function getMenuGroups($id) {
		$query = $this->db->query("select g.* from qy_rel_menu_group mg join qy_menu_group g on mg.menu_group_id=g.id where mg.menu_id=$id");
		
		return $query->rows;
	}
}
?>