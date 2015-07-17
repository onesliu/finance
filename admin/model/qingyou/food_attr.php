<?php
class ModelQingyouFoodAttr extends Model {
	public function addFoodAttr($data) {
		$this->db->query("INSERT INTO qy_food_attr SET `name` = '" . $data['name'] .
			"', `disable` = '" . (int)$data['disable'] .
			"', `sort` = '" . (int)$data['sort'] .
			"', `desp` = '" . (int)$data['desp'] .
			"', `stype` = '" . $data['stype'] .
			"'");

		$id = $this->db->getLastId();
		$this->cache->delete('food_attr');
	}
	
	public function editFoodAttr($id, $data) {
		$this->db->query("UPDATE qy_food_attr SET `name` = '" . $data['name'] .
			"', `disable` = '" . (int)$data['disable'] .
			"', `sort` = '" . (int)$data['sort'] .
			"', `desp` = '" . (int)$data['desp'] .
			"', `stype` = '" . $data['stype'] .
			"' where `id`= " . $id);

		$this->cache->delete('food_attr');
	}
	
	public function deleteFoodAttr($id) {
		$this->db->query("DELETE FROM qy_food_attr WHERE id = '" . (int)$id . "'");
		
		$this->cache->delete('food_attr');
	} 
	
	public function getFoodAttr($id) {
		$query = $this->db->query("SELECT * from qy_food_attr where id=$id");
		
		return $query->row;
	} 
	
	public function getFoodAttrs($data) {

		$sql = "SELECT * from qy_food_attr where disable=0 order by stype,sort";
		
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
		
		$attrs = array();
		foreach($query->rows as $row) {
			$attrs[$row['stype']][] = $row;
		}
		return $attrs;
	}
				
	public function getTotalFoodAttr() {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM qy_food_attr");
		
		return $query->row['total'];
	}	
	
	public function getMenuFood($id) { //menu_id
		$query = $this->db->query("select f.* from qy_food f join qy_rel_food_menu fm on f.id=fm.food_id where fm.menu_id=$id");
		
		return $query->row;
	}

	public function getFoodMenu($id) { //food_id
		$query = $this->db->query("select m.* from qy_menu m join qy_rel_food_menu fm on m.id=fm.menu_id where fm.food_id=$id");
		
		return $query->row;
	}
	
}
?>