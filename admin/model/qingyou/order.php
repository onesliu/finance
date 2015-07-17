<?php
class ModelQingyouOrder extends Model {
	
	private $psql = "select order_id, date_added as order_createtime, o.order_status_id, os.name as order_status,
		o.order_type, customer_id, CONCAT(firstname,lastname) as customer_name, telephone as customer_phone,
		CONCAT(shipping_firstname,shipping_lastname) as shipping_name, shipping_telephone, comment, comment2,
		shipping_address_1 as shipping_addr, shipping_time, o.iscash, o.costpay, o.cashpay, o.ismodify 
		from oc_order o join oc_order_status os on o.order_status_id = os.order_status_id %s order by order_id;";
	
	public function getOrders($last_orderid, $districtid, $history, $order_type = null) {
		
		$condition = "";
		if ($history == null || $history == 0) {
			$condition = "where o.order_status_id < 4";
		}
		else {
			$condition = "where o.order_status_id >= 4";
		}
		
		if ($last_orderid != null) {
			$condition .= " and o.order_id > $last_orderid";
		}
		
		if ($districtid > 0) {
			$condition .= " and shipping_district_id = $districtid";
		}
		
		if ($order_type != null) {
			$condition .= " and order_type <> $order_type";
		}
		
		$data = array();
		$sql = sprintf($this->psql, $condition);
		
		$query = $this->db->query($sql);
		//$this->log->write($history);
		foreach ($query->rows as $result) {
			$o = new stdClass();
			foreach($result as $key => $val) {
				$o->$key = $val;
			}
			$o->products = $this->getProducts($o->order_id);
			$data[] = $o;
		}

		return $data;
	}
	
	public function searchOrders($data) {
		
		if (isset($data['begin']))
			$condition = "where o.date_added>='" .$data['begin'] ."'";
		else
			return false;
			
		if (isset($data['end']))
			$condition .= " and o.date_added<='" .$data['end'] ."'";
			
		if (isset($data['districtid']))
			$condition .= " and shipping_district_id=" .$data['districtid'];
			
		$product_id = 0;
		if (isset($data['productid'])) {
			$condition .= " and o.order_type=2";
			$product_id = $data['productid'];
		}
		
		if (isset($data['statusid'])) {
			if ($data['statusid'] > 0 && $data['statusid'] <= 6)
				$condition .= " and o.order_status_id=" .$data['statusid'];
			else if ($data['statusid'] == 7)
				$condition .= " and o.order_status_id<4";
			else if ($data['statusid'] == 8)
				$condition .= " and o.order_status_id>=4";
		}
		
		$sql = sprintf($this->psql, $condition);
		
		$ret = array();
		$query = $this->db->query($sql);
		foreach ($query->rows as $result) {
			$o = new stdClass();
			foreach($result as $key => $val) {
				$o->$key = $val;
			}
			$o->products = $this->getProducts($o->order_id);
			if ($product_id == 0)
				$ret[] = $o;
			else if ($product_id > 0 && $o->products[0]->product_id == $product_id)
				$ret[] = $o;
		}

		return $ret;
	}
	
	public function getOrderCustomer($orderid) {
		$q = $this->db->query("select email from oc_customer where customer_id=(select customer_id from oc_order where order_id=$orderid)");
		if ($q->num_rows != 0) {
			return $q->row['email'];
		}
		return false;
	}
	
	public function updateOrder($order) {
		$query = $this->db->query("select order_status_id from " .DB_PREFIX. "order where order_id=".$order->order_id);
		if ($query->num_rows != 0) {
			if ($query->row['order_status_id'] == $order->order_status)
				return false;
		}
		else {
			return false;
		}
		
		$costpay = "";
		if (isset($order->costpay)) {
			$costpay = ", costpay=".$order->costpay;
		}
		
		$cashpay = "";
		if (isset($order->cashpay)) {
			$cashpay = ", cashpay=".$order->cashpay;
		}
		
		$iscash = "";
		if (isset($order->iscash)) {
			$iscash = ", iscash=".$order->iscash;
		}
		
		$ismodify = ", ismodify=1";
		
		$sql = "update " .DB_PREFIX. "order set order_status_id=".$order->order_status.
			", total=".$order->realtotal. $costpay. $cashpay. $iscash. $ismodify.
			" where order_id=".$order->order_id;
		$this->db->query($sql);
		
		$sql = "update " .DB_PREFIX. "order_total set value=".$order->realtotal.
			", text='".$this->currency->format($order->realtotal).
			"' where code in ('total','sub_total') and order_id=".$order->order_id;
		$this->db->query($sql);
		
		$sql = "insert into ".DB_PREFIX."order_history set order_id=".$order->order_id.
			",order_status_id=".$order->order_status.
			",notify=1,date_added=NOW()";
		$this->db->query($sql);
		return true;
	}
	
	public function updateOrderPay($order_id, $payxml) {
		$query = $this->db->query("select order_status_id from " .DB_PREFIX. "order where order_id=".$order_id);
		if ($query->num_rows != 0) {
			if ($query->row['order_status_id'] != 2)
				return;
		}
		else {
			return;
		}
		
		$sql = "update " .DB_PREFIX. "order set order_status_id=3, weixin_pay_result='".
				$this->db->escape($payxml)."' where order_id=".$order_id;
		$this->db->query($sql);
	}
	
	public function getProducts($orderid) {
		
		$this->load->model('tool/image');
		
		$condition = "";
		if ($orderid != null) {
			$condition = "where order_id = $orderid";
		}
		
		$data = array();
		
		$query = $this->db->query("select op.order_id, op.product_id, op.name as product_name, p.ean, p.jan, p.image,
		p.mpn as perprice, p.upc as perunit, p.weight as perweight, p.sku as unit, wd.title as weightunit,
		op.quantity, round(op.price,2) as price, round(op.total,2) as total, realweight, 
		round(realtotal,2) as realtotal, p.product_type from " .DB_PREFIX. "order_product op 
		join " .DB_PREFIX. "product p on p.product_id=op.product_id 
		join oc_weight_class_description wd on p.weight_class_id=wd.weight_class_id $condition");
		
		foreach ($query->rows as $result) {
			$o = new stdClass();
			foreach($result as $key => $val) {
				$o->$key = $val;
				if ($key == "image") {
					if ($val != "") {
						$image = $this->model_tool_image->resize($val, $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
					} else {
						$image = $this->model_tool_image->resize('no_image.jpg', $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
					}
					$o->$key = $image;
				}
			}
			
			$option_value = "";
			$opt = $this->db->query("select * from oc_order_option oo join oc_product_option_value ov 
				on ov.product_option_value_id=oo.product_option_value_id where order_id=".$result['order_id']);
			foreach ($opt->rows as $option) {
				$option_value .= $option['value'] ." ";
				$oprice = (double)($option['price_prefix'].$option['price']);
				$o->perprice = (string)($o->perprice + $oprice);
				$o->price = (string)($o->price + $oprice);
			}
			
			$o->option = trim($option_value);
			if ($o->option != "")
				$o->product_name .= "(".$o->option.")";
			$data[] = $o;
		}
		
		return $data;
	}
	
	public function updateProduct($orderid, $product) {
		$sql = "update " .DB_PREFIX. "order_product set realweight=".$product->realweight
			.", realtotal=".$product->realtotal
			.", weight=".$product->realweight
			.", total=".$product->realtotal
			." where order_id=".$orderid." and product_id=".$product->product_id;
		$query = $this->db->query($sql);
	}
	
	public function getStatus() {
		
		$query = $this->db->query("select order_status_id,name from " .DB_PREFIX. "order_status");
		
		$data = array();
		foreach ($query->rows as $result) {
			$data[$result['order_status_id']] = $result['name'];
		}
		
		return $data;
	}
	
	public function getStatusMsg() {
		
		$query = $this->db->query("select order_status_id,wxtitle,wxmsg from " .DB_PREFIX. "order_status");
		
		$data = array();
		foreach ($query->rows as $result) {
			$omsg = new stdClass();
			$omsg->wxtitle = $result['wxtitle'];
			$omsg->wxmsg = $result['wxmsg'];
			$data[$result['order_status_id']] = $omsg;
		}
		
		return $data;
	}
	
	public function getDistricts() {
		
		$query = $this->db->query("select * from " .DB_PREFIX. "district");
		
		$data = array();
		foreach ($query->rows as $result) {
			$o = new stdClass();
			foreach($result as $key => $val) {
				$o->$key = $val;
			}
			$data[] = $o;
		}
		
		return $data;
	}
	
	public function getSpecials() {
		$query = $this->db->query("select p.product_id, pd.name, p.status, p.price from oc_product p join oc_product_description pd on p.product_id=pd.product_id where product_type=2");
		
		$data = array();
		foreach ($query->rows as $result) {
			$o = new stdClass();
			foreach($result as $key => $val) {
				$o->$key = $val;
			}
			$data[] = $o;
		}
		
		return $data;
	}
	
	public function getBalance($districtid) {
		$condition = "";
		if ($districtid > 0)
			$condition = "where shop_id=$districtid";
		
		$sql = "select last_balance_date,NOW() as cdate from qy_balance $condition order by id desc limit 1";
		$query = $this->db->query($sql);
		
		$ret = new stdClass();
		if ($query->num_rows == 0) {
			$ret->last_balance_date = '0';
			$ret->current_date = date('Y-m-d H:i:s');
		}
		else {
			$ret->last_balance_date = $query->row['last_balance_date'];
			$ret->current_date = $query->row['cdate'];
		}
		
		$condition = "";
		if ($districtid > 0)
			$condition = "and shipping_district_id=$districtid";
			
		$sql = "SELECT order_id,total FROM oc_order WHERE order_status_id = 4 AND balance = 0 $condition";
		$query = $this->db->query($sql);
		
		$ret->count = (int)$query->num_rows;
		$total = (float)0.0;
		$order_ids = array();
		foreach ($query->rows as $row) {
			$total += (float)$row['total'];
			$order_ids[] = $row['order_id'];
		}
		$ret->total = $total;
		$this->session->data['balance_order_id'] = $order_ids;
		
		return $ret;
	}
	
	public function setBalance($districtid) {
		
		if (!isset($this->session->data['balance_order_id']))
			return false;
			
		$order_ids = $this->session->data['balance_order_id'];
		
		if (count($order_ids) == 0)
			return false;
		
		$condition = implode(",", $order_ids);

		$sql = "update oc_order set balance=1 where order_id in ($condition)";
		if ( $this->db->query($sql) == false)
			return false;
		
		$sql = "insert into qy_balance set shop_id=$districtid, last_balance_date=now()";
		if ( $this->db->query($sql) == false)
			return false;

		return true;
	}
}