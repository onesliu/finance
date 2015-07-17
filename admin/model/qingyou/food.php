<?php
class ModelQingyouFood extends Model {
	public function addFood($data) {
		$this->db->query("INSERT INTO qy_food SET `name` = '" . $data['name'] .
			"', `disable` = '" . (int)$data['disable'] .
			"', `sort` = '" . (int)$data['sort'] .
			"', `desp` = '" . $data['desp'] .
			"', `image1` = '" . $data['image1'] .
			"', `image2` = '" . $data['image2'] .
			"', `image3` = '" . $data['image3'] .
			"', `make_video` = '" . $data['make_video'] .
			"', `make_url` = '" . $data['make_url'] .
			"'");

		$id = $this->db->getLastId();
		$this->cache->delete('food');
		
		$this->editFoodRel($id, $data);
	}

	private function editFoodRel($id, $data) { //menu_id
		if (isset($data['attrs'])) {
			$this->db->query("delete from qy_rel_food_attr where food_id=$id");
			foreach ($data['attrs'] as $attr) {
				$this->db->query("insert into qy_rel_food_attr set food_id=$id, attr_id=$attr");
			}
		}
		
		if (isset($data['source'])) {
			$this->db->query("delete from qy_food_source where food_id=$id");
			foreach ($data['source'] as $source) {
				$this->db->query("insert into qy_food_source set food_id=$id, product_id=".$source['product_id'].
				", source_type=".$source['type'].", sort=".$source['sort'].", groupid=".$source['groupid']);
			}
		}

		if (isset($data['step'])) {
			$this->db->query("delete from qy_food_make where food_id=$id");
			foreach ($data['step'] as $step) {
				$this->db->query("insert into qy_food_make set food_id=$id, desp='".$step['desp'].
				"', image='".$step['image']."', step=".$step['step']);
			}
		}
	}
	
	public function editFood($id, $data) {
		$this->db->query("UPDATE qy_food SET `name` = '" . $data['name'] .
			"', `disable` = '" . (int)$data['disable'] .
			"', `sort` = '" . (int)$data['sort'] .
			"', `desp` = '" . $data['desp'] .
			"', `image1` = '" . $data['image1'] .
			"', `image2` = '" . $data['image2'] .
			"', `image3` = '" . $data['image3'] .
			"', `make_video` = '" . $data['make_video'] .
			"', `make_url` = '" . $data['make_url'] .
			"' where `id`= " . $id);

		$this->cache->delete('food');
		
		$this->editFoodRel($id, $data);
	}
	
	public function deleteFood($id) {
		$this->db->query("DELETE FROM qy_food WHERE id = '" . (int)$id . "'");
		
		$this->cache->delete('food');
	} 
	
	public function getFood($id) {
		$query = $this->db->query("SELECT * from qy_food where id=$id");
		
		return $query->row;
	} 
	
	public function getAllFood($data) {

		$sql = "SELECT * from qy_food order by sort";
		
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
				
	public function getTotalFood() {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM qy_food");
		
		return $query->row['total'];
	}	
	
	public function getMenuFood($id) { //menu_id
		$query = $this->db->query("select f.* from qy_food f join qy_rel_food_menu fm on f.id=fm.food_id where fm.menu_id=$id");
		
		return $query->rows;
	}

	public function getFoodAttrs($id) {
		$query = $this->db->query("select a.* from qy_rel_food_attr fa join qy_food_attr a on fa.attr_id=a.id where food_id=$id");
		$attrs = array();
		foreach($query->rows as $row) {
			$attrs[$row['stype']][] = $row;
		}
		return $attrs;
	}
	
	public function getFoodMenu($id) { //food_id
		$query = $this->db->query("select m.* from qy_menu m join qy_rel_food_menu fm on m.id=fm.menu_id where fm.food_id=$id");
		
		return $query->rows;
	}
	
	public function getFoodSources($id) {
		$query = $this->db->query("select p.*,s.* from qy_food_source s join oc_product_description p on s.product_id=p.product_id where food_id=$id order by groupid,name");
		return $query->rows;
	}
	
	public function getAllSources() {
		$query = $this->db->query("select pd.name,p.* from oc_product_description pd join oc_product p on p.product_id=pd.product_id where language_id=1 order by pd.name desc");
		return $query->rows;
	}

	public function getMakeSteps($id) {
		$query = $this->db->query("select m.* from qy_food f join qy_food_make m on f.id=m.food_id where f.id=$id order by m.step");
		return $query->rows;
	}
}
?>