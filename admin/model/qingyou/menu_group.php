<?php
class ModelQingyouMenuGroup extends Model {
	public function addGroup($data) {
		$this->db->query("INSERT INTO qy_menu_group SET `name` = '" . $data['name'] .
			"', `disable` = '" . (int)$data['disable'] .
			"', `sort` = '" . (int)$data['sort'] .
			"', `image` = '" . $data['image'] .
			"', `hasname` = '" . (int)$data['hasname'] . "'");

		$id = $this->db->getLastId();
		$this->cache->delete('menu_group');
	}
	
	public function editGroup($id, $data) {
		$this->db->query("UPDATE qy_menu_group SET `name` = '" . $data['name'] .
			"', `disable` = '" . (int)$data['disable'] .
			"', `sort` = '" . (int)$data['sort'] .
			"', `image` = '" . $data['image'] .
			"', `hasname` = '" . (int)$data['hasname'] .
			"' where `id`= " . $id);

		$this->cache->delete('menu_group');
	}
	
	public function deleteGroup($id) {
		$this->db->query("DELETE FROM qy_menu_group WHERE id = '" . (int)$id . "'");
		
		$this->cache->delete('menu_group');
	} 
	
	public function getGroup($id) {
		$query = $this->db->query("SELECT * from qy_menu_group where id=$id");
		
		return $query->row;
	} 
	
	public function getGroups($data) {

		$sql = "SELECT * from qy_menu_group order by sort";
		
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}				

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}	
		 
			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
						
		$query = $this->db->query($sql);
		
		return $query->rows;
	}
				
	public function getTotalGroups() {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM qy_menu_group");
		
		return $query->row['total'];
	}	
}
?>