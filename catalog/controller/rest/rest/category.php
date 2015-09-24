<?php  
class ControllerRestCategory extends Controller {
	public function index() {
		
		$cate = new stdClass();
		$cate->result = 0;
		$cate->info = array();
		
		$this->load->model('tool/image');
		$no_image = $this->model_tool_image->resize('no_image.jpg', $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
		$results = $this->db->query("select category1,category2,count(*) as cnt from f_product group by category1,category2");
		
		foreach ($results->rows as $row) {
			$c = new stdClass();
			$c->category1 = $row['category1'];
			$c->category2 = $row['category2'];
			$c->cnt = $row['cnt'];
			$c->no_image = $no_image;
			$c->image = $no_image;
			
			$cate->info[] = $c;
		}
		
		$this->response->setOutput(json_encode($cate));
	}
}
?>