<?php  
class ControllerRestProducts extends Controller {
	public function index() {
		
		if ($this->customer->isLogged()) {
			$customer_id = $this->customer->getId();
		}
		
		$product = new stdClass();
		$product->result = -1;

		if (isset($this->request->post['category'])) {
			$category = $this->request->post['category'];
		} else {
			$this->response->setOutput(json_encode($product));
			return;
		}
		$cats = explode(" ", $category);
		if (count($cats) != 2) {
			$this->response->setOutput(json_encode($product));
			return;
		}
		foreach($cats as &$cat) {
			$cat = trim($cat);
		}
		
		$limit = 0;
		if (isset($this->request->post['limit'])) {
			$limit = $this->request->post['limit'];
		}
		
		$this->load->model('tool/image');
		$no_image = $this->model_tool_image->resize('no_image.jpg', $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));

		$product->no_image = $no_image;
		$product->info = array();
		
		$results = $this->db->query("select * from f_product where category1='".$cats[0]."' and category2='".$cats[1]."'");
		foreach ($results->rows as $row) {
			if ($row['product_img']) {
				$image = $this->model_tool_image->resize($row['product_img'], $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
			} else {
				$image = $no_image;
			}

			$c = new stdClass();
			$c->product_id = $row['product_id'];
			$c->category1 = $row['category1'];
			$c->category2 = $row['category2'];
			$c->name = $row['name'];
			$c->minlimit = $row['minlimit'];
			$c->maxlimit = $row['maxlimit'];
			$c->minrate = $row['minrate'];
			$c->maxrate = $row['maxrate'];
			$c->minperiod = $row['minperiod'];
			$c->maxperiod = $row['maxperiod'];
			$c->periodstep = $row['periodstep'];
			$c->repayment = $row['repayment'];
			$c->minage = $row['minage'];
			$c->maxage = $row['maxage'];
			$c->template_id = $row['template_id'];
			$c->material = $row['material'];
			$c->remark = $row['remark'];
			$c->image = $image;
			if (isset($customer_id))
				$c->matching = $this->match($c->product_id, $customer_id);
			$product->info[] = $c;
		}
		
		$product->result = 0;
		$this->response->setOutput(json_encode($product));
	}
	
	private $reqs;
	private $exps;
	private function match($product_id, $customer_id) {
		$result = $this->db->query("select * from f_product_require pr 
			join f_require r on pr.require_id=r.require_id 
			left outer join f_customer_require cr on cr.require_id=r.require_id 
			where pr.product_id=$product_id and (cr.customer_id=$customer_id or cr.customer_id is null)");
			
		/* 首先判断各个条件是否为真，其次计算所有表达式的值
		 * 条件分两类，一类是包含在表达式中的，一类是没有包含在表达式中的（非表达式条件）
		 * 匹配度计算：为真的(非表达式条件 + 表达式) / 所有非表达式条件+所有表达式
		*/
		$this->reqs = array();
		$exps = array();
		foreach($result->rows as $row) {
			$req = new stdClass();
			$req->set = false;
			$req->exp_in = false;
			$this->reqs[$row["require_id"]] = $req;
			
			if ($row["customer_id"]) {
				$value_set = explode(",", $row["value_set"]);
				$exp = $row["expresion"];
				if (!in_array($exp, $exps))
					$exps[] = $exp;
				
				$rvalue_id = $row["rvalue_id"];
				
				if (in_array($rvalue_id, $value_set)){
					$req->set = true;
				}
			}
		}
		
		$exp_vals = array();
		$exp_cnt = 0;
		$exp_total = count($exps);
		foreach($exps as $exp) {
			$exp_vals[$exp] = $this->eval_expression($exp);
			if ($exp_vals[$exp])
				$exp_cnt++;
		}
		
		$this->exps = $exp_vals;
		
		$nomatch_cnt = 0;
		$nomatch_total = 0;
		foreach($this->reqs as $rid => &$req) {
			if ($req->exp_in == false) {
				$nomatch_total++;
				if ($req->set == true)
					$nomatch_cnt++;
			}
		}
		
		return ($nomatch_cnt + $exp_cnt) / ($nomatch_total + $exp_total);
	}
	
	public function callback($ms) {
		$b = false;
		if ($this->reqs && $this->reqs[$ms[0]] && $this->reqs[$ms[0]]->set) {
			$req = $this->reqs[$ms[0]];
			$b = $req->set;
			$req->exp_in = true;
		}
		return "(" . ($b)?"true":"false" . ")";
	}
	
	private function eval_expression($exp) {
		if (!$exp) return false;
		$expstr = preg_replace_callback("|\d+|", array($this, "callback"), $exp);
		$ev = false;
		eval("\$ev = $expstr;");
		return $ev;
	}
}
?>