<?php
class ModelQingyouMenu extends Model {
	public function addMenu($data) {
		$this->db->query("INSERT INTO qy_menu SET `name` = '" . $data['name'] .
			"', `disable` = '" . (int)$data['disable'] .
			"', `sort` = '" . (int)$data['sort'] .
			"', `desp` = '" . $data['desp'] .
			"', `image1` = '" . $data['image1'] .
			"', `image2` = '" . $data['image2'] .
			"', `image3` = '" . $data['image3'] . "'");

		$id = $this->db->getLastId();
		$this->cache->delete('menu');
		
		$this->editMenuRel($id, $data);
	}
	
	private function editMenuRel($id, $data) { //menu_id
		if (isset($data['groups'])) {
			$this->db->query("delete from qy_rel_menu_group where menu_id=$id");
			foreach ($data['groups'] as $group) {
				$this->db->query("insert into qy_rel_menu_group set menu_id=$id, menu_group_id=$group");
			}
		}
		
		if (isset($data['menu_food'])) {
			$this->db->query("delete from qy_rel_food_menu where menu_id=$id");
			foreach ($data['menu_food'] as $food) {
				$this->db->query("insert into qy_rel_food_menu set menu_id=$id, food_id=$food");
			}
		}
	}
	
	public function editMenu($id, $data) {
		$this->db->query("UPDATE qy_menu SET `name` = '" . $data['name'] .
			"', `disable` = '" . (int)$data['disable'] .
			"', `sort` = '" . (int)$data['sort'] .
			"', `desp` = '" . $data['desp'] .
			"', `image1` = '" . $data['image1'] .
			"', `image2` = '" . $data['image2'] .
			"', `image3` = '" . $data['image3'] .
			"' where `id`= " . $id);

		$this->cache->delete('menu');
		
		$this->editMenuRel($id, $data);
	}
	
	public function deleteMenu($id) {
		$this->db->query("DELETE FROM qy_menu WHERE id = '" . (int)$id . "'");
		$this->db->query("delete from qy_rel_menu_group where menu_id=$id");
		$this->db->query("delete from qy_rel_food_menu where menu_id=$id");
		$this->cache->delete('menu');
	} 
	
	public function getMenu($id) {
		$query = $this->db->query("SELECT * from qy_menu where id=$id");
		
		return $query->row;
	} 
	
	public function getMenus($data) {

		$sql = "SELECT * from qy_menu order by sort";
		
		if (is_array($data) && (isset($data['start']) || isset($data['limit'])) ) {
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