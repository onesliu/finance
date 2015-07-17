<?php
class ModelQingyouFood extends Model {
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
	
	public function getFoodWithPrice($menu_id) {

		$join = "";
		$condition = "";
		
		if ($menu_id > 0) {
			$join = "join qy_rel_food_menu fm on fm.food_id=f.id";
			$condition = "and fm.menu_id=$menu_id";
		}
		$sql = "select f.*,sum(p.price) as price from qy_food f join qy_food_source fp on f.id=fp.food_id 
				join oc_product p on fp.product_id=p.product_id $join where fp.groupid > 0 $condition group by fp.food_id;";
		
		/*
		$cache_hash = md5($sql);
		$data = $this->cache->get('food_with_price.' . $cache_hash);
		if (!$data) {
			$data = $this->db->query($sql);
			$this->cache->set('food_with_price.' . $cache_hash, $data);
		}
		*/
		$data = $this->db->query($sql);
		return $data->rows;
	}
				
	public function getTotalFood() {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM qy_food");
		
		return $query->row['total'];
	}	
	
	public function getMenuFood($id) { //menu_id
		$query = $this->db->query("select f.* from qy_food f join qy_rel_food_menu fm on f.id=fm.food_id where fm.menu_id=$id order by f.sort");
		
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
		$query = $this->db->query("select distinct(p.product_id),pd.*,p.*,s.*,wd.title as weight_class
			from qy_food_source s join oc_product_description pd on s.product_id=pd.product_id 
			join oc_product p on s.product_id=p.product_id 
			join oc_weight_class_description wd on p.weight_class_id=wd.weight_class_id
			where food_id = $id
			order by s.groupid desc,s.sort");
		return $query->rows;
	}
	
	public function getFoodSourcesByMenu($menuid) {
		$query = $this->db->query("select distinct(p.product_id),pd.*,p.*,s.*,wd.title as weight_class
			from qy_food_source s join oc_product_description pd on s.product_id=pd.product_id 
			join oc_product p on s.product_id=p.product_id 
			join oc_weight_class_description wd on p.weight_class_id=wd.weight_class_id 
			join qy_rel_food_menu m on m.food_id=s.food_id 
			where m.menu_id = $menuid
			order by s.sort");
		return $query->rows;
	}
	
	public function getAllSources() {
		$query = $this->db->query("select pd.name,p.* from oc_product_description pd join oc_product p on p.product_id=pd.product_id where language_id=1;");
		return $query->rows;
	}

	public function getMakeSteps($id) {
		$query = $this->db->query("select m.* from qy_food f join qy_food_make m on f.id=m.food_id where f.id=$id order by m.step");
		return $query->rows;
	}
}
?>