<?php  
class ControllerRestCategory extends Controller {
	public function index() {
		
		$cate = new stdClass();
		$cate->result = 0;
		$cate->info = array();
		
		$this->load->model('tool/image');
		$no_image = $this->model_tool_image->resize('no_image.jpg', $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
		$results = $this->db->query("select category1,category2,category_img,count(*) as cnt from f_product group by category1,category2");
		
		foreach ($results->rows as $row) {
			if ($row['category_img']) {
				$image = $this->model_tool_image->resize($row['category_img'], $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
			} else {
				$image = $no_image;
			}
			
			$c = new stdClass();
			$c->category1 = $row['category1'];
			$c->category2 = $row['category2'];
			$c->cnt = $row['cnt'];
			$c->no_image = $no_image;
			$c->image = $image;
			
			$cate->info[] = $c;
		}

		$cate->total = count($cate->info);
		$this->response->setOutput(json_encode($cate));
	}
	
	public function match() {
		
		$cate = new stdClass();
		$cate->result = -1;
		$cate->total = 0;
		$cate->info = array();
		
		if (!$this->customer->isLogged()) {
			$cate->result = "nologin";
			$this->response->setOutput(json_encode($cate));
			return;
		}
		$customer_id = $this->customer->getId();
		
		$this->getChild('rest/products/match_all_product');

		$this->load->model('tool/image');
		$no_image = $this->model_tool_image->resize('no_image.jpg', $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
		
		$results2 = $this->db->query($sql = "select p.category1, p.category2, p.category_img, count(*) as cnt 
			from f_customer_product cp join f_product p on cp.product_id=p.product_id 
			where customer_id=$customer_id and cp.matching > 0
			group by p.category1, p.category2");
		if ($results2->num_rows <= 0) {
			$this->response->setOutput(json_encode($cate));
			return;
		}

		$results = $this->db->query("select category1,category2,category_img,count(*) as cnt 
			from f_product group by category1,category2");

		foreach($results2->rows as $r2) {
			foreach ($results->rows as $row) {
				if ($row["category1"] == $r2["category1"] &&
					$row["category2"] == $r2["category2"]) {
					if ($row['category_img']) {
						$image = $this->model_tool_image->resize($row['category_img'], $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
					} else {
						$image = $no_image;
					}
					$c = new stdClass();
					$c->category1 = $r2['category1'];
					$c->category2 = $r2['category2'];
					$c->cnt = $r2['cnt'];
					$c->no_image = $no_image;
					$c->image = $image;
					
					$cate->info[] = $c;
				}
			}
		}
		
		$cate->result = 0;
		$cate->total = count($cate->info);
		$this->response->setOutput(json_encode($cate));
	}
}
?>