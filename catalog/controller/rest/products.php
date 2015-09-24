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
		
		$results = $this->db->query("select * from f_product where category1='".$cats[0]."' and category2='".$cats[1]."' order by minrate desc");
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
			if (isset($customer_id)) {
				$c->matching = $this->match($c->product_id, $customer_id);
				$this->update_matching($customer_id, $c->product_id, $c->matching);
			}
			
			if ($limit != 0 && $c->matching <= 0) continue;
			$product->info[] = $c;
		}
		
		if (isset($customer_id)) {
			usort($product->info, array($this, "match_cmp"));
		}
		
		$product->result = 0;
		$this->response->setOutput(json_encode($product));
	}
	
	public function match_all_product() {
		if (!$this->customer->isLogged())
			return;
		$customer_id = $this->customer->getId();

		$results = $this->db->query("select * from f_product");
		foreach ($results->rows as $row) {
			$matching = $this->match($row['product_id'], $customer_id);
			$this->update_matching($customer_id, $row['product_id'], $matching);
		}
	}
	
	public function match_cmp($a, $b) {
		if ($a->matching == $b->matching)
			return 0;
		return ($a->matching > $b->matching)? -1: 1;
	}
	
	private $reqs;
	private $exps;
	private function match($product_id, $customer_id) {
		$result = $this->db->query($sql1 = "select product_id,pr.require_id,value_set,expresion,r.require_group,
		(select value_type from f_require_value rv where r.require_group=rv.require_group limit 1) as value_type
		from f_product_require pr join f_require r on pr.require_id=r.require_id where product_id=$product_id");
		$result2 = $this->db->query($sql2 = "select require_id,customer_id,cr.rvalue_id,cr.rvalue as crvalue 
		from f_customer_require cr where cr.customer_id=$customer_id");
		
		foreach($result->rows as &$r) {
			$r["customer_id"] = null;
			$r["rvalue_id"] = null;
			$r["crvalue"] = null;
			foreach($result2->rows as &$r2) {
				if ($r["require_id"] == $r2["require_id"]) {
					$r["customer_id"] = $r2["customer_id"];
					$r["rvalue_id"] = $r2["rvalue_id"];
					$r["crvalue"] = $r2["crvalue"];
				}
			}
		}
		
		//$this->log->write("$product_id, $customer_id");
		//$this->log->write("$sql1");
		//$this->log->write("$sql2");
		
		/* 首先判断各个条件是否为真，其次计算所有表达式的值
		 * 条件分两类，一类是包含在表达式中的，一类是没有包含在表达式中的（非表达式条件）
		 * 匹配度计算：为真的(非表达式条件 + 表达式) / 所有非表达式条件+所有表达式
		*/
		$this->reqs = array();
		$exps = array();
		foreach($result->rows as $row) {
			$req = new stdClass();
			$req->set = 0;  // 0: 条件未设置值，1: 设置并为真值，-1: 设置并为假值 设置为假匹配度计算为0
			$req->exp_in = false;
			$this->reqs[$row["require_id"]] = $req;
			if ($row["customer_id"]) {
				if ($row["value_type"] == "number") {
					$value_set = explode("-", $row["value_set"]);
					if (count($value_set) == 1) {
						if ($row["crvalue"] != $value_set[0]) //单个数值如果不相等就返回0匹配度
							$req->set = -1;
						else
							$req->set = 1;
					} else if (count($value_set) == 2) {
						if ($row["crvalue"] < $value_set[0] || $row["crvalue"] > $value_set[1])
							$req->set = -1; //数值范围如果超出就返回0匹配度
						else
							$req->set = 1;
					}
				}
				else if ($row["value_type"] == "set" || $row["value_type"] == "order") {
					$value_set = explode(",", $row["value_set"]);
					$exp = $row["expresion"];
					if ($exp && strlen($exp) > 0 && !in_array($exp, $exps))
						$exps[] = $exp;
					
					$rvalue_id = $row["rvalue_id"];
					
					if ($rvalue_id != null) {
						if (in_array($rvalue_id, $value_set)){
							$req->set = 1;
						} else {
							$req->set = -1;
						}
					}
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
		$negtive = 1;
		foreach($this->reqs as $rid => &$req) {
			if ($req->exp_in == false) {
				$nomatch_total++;
				if ($req->set == 1)
					$nomatch_cnt++;
				else if ($req->set == -1)
					$negtive = 0;
			}
		}

		/*
		if ($product_id == 38) {
			$this->log->write(print_r($result->rows,true));
			$this->log->write(print_r($this->reqs,true));
			$this->log->write(print_r($exps,true));
		}
		*/
		//$this->log->write("$product_id, $customer_id, ($nomatch_cnt + $exp_cnt) / ($nomatch_total + $exp_total)");
		return round((($nomatch_cnt + $exp_cnt) / ($nomatch_total + $exp_total) * 100) * $negtive);
	}
	
	public function callback($ms) {
		$b = false;
		if ($this->reqs && isset($this->reqs[$ms[0]])) {
			$req = &$this->reqs[$ms[0]];
			$b = ($req->set == 1);
			$req->exp_in = true;
			//$this->log->write("req[$ms[0]]->set : ". $req->set ." b: $b");
		} else if (!isset($this->reqs[$ms[0]])) {
			$this->log->write("no reqid: $ms[0]");
			$this->log->write(print_r($this->reqs,true));
		}
		
		if ($b == true)
			return "(true)";
		else
			return "(false)";
	}
	
	private function eval_expression($exp) {
		if (!$exp) return false;
		$expstr = preg_replace_callback("|\d+|", array($this, "callback"), $exp);
		$ev = false;
		//$this->log->write($exp ." ". $expstr);
		eval("\$ev = $expstr;");
		return $ev;
	}
	
	private function update_matching($customer_id, $product_id, $matching) {
		$this->db->query("insert into f_customer_product set 
			customer_id=$customer_id, product_id=$product_id, matching=$matching 
			on duplicate key update 
			matching=$matching");
	}
}
?>