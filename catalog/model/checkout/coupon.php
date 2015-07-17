<?php
class ModelCheckoutCoupon extends Model {
	public function getCustomerCoupon($customer_id) {
		//折扣劵保留多条，代金劵合并
		$sql = "select * from oc_coupon_customer cc
			join oc_coupon c on cc.coupon_id=c.coupon_id where customer_id = $customer_id
			and status>0 and (current_date() >= date_start and current_date() <= date_end)
			and cc.amount > 0
			order by c.coupon_id";
		$result = $this->db->query($sql);
		if ($result->num_rows > 0) {
			
			$ret = array();
			
			foreach($result->rows as $row) {
				if ($row['type'] == 'F') { //代金劵
					$cash['type'] = 'F';
					if (isset($cash['amount']))
						$cash['amount'] += $row['amount'];
					else
						$cash['amount'] = $row['amount'];
					$cash['name'] = '代金劵  '.$this->currency->format($cash['amount']);
					$cash['coupon_id'] = 0;
				}
				else if ($row['type'] == 'P') { //折扣劵
					$r['type'] = 'P';
					$r['name'] = $row['name'];
					$r['discount'] = $row['discount'];
					$r['coupon_id'] = $row['coupon_id'];
					$ret[] = $r;
				}
			}
			
			if (isset($cash)) {
				$ret[] = $cash;
			}
			
			return $ret;
		}
		return null;
	}
	
	//代金劵
	//输入订单金额，返回优惠后金额，false优惠已失效
	public function cashCoupon($customer_id, $order_id, $total) {
		$result = $this->db->query("select * from oc_coupon_customer cc
			join oc_coupon c on cc.coupon_id=c.coupon_id where customer_id = $customer_id
			and status>0 and (current_date() >= date_start and current_date() <= date_end)
			and cc.amount > 0 and type='F'
			order by c.coupon_id");
	
		if ($result->num_rows <= 0)
			return false;
			
		foreach($result->rows as $row) {
			$coupon_id = $row['coupon_id'];
			if ($row['amount'] - $total >= 0) {
				$coupon_total = $total;
				$total = 0;
				$this->db->query("update oc_coupon_customer set amount=".($row['amount'] - $total));
			}
			else if ($row['amount'] - $total < 0) {
				$coupon_total = $row['amount'];
				$total -= $row['amount'];
				$this->db->query("update oc_coupon_customer set amount=0");
			}
			
			$this->db->query("insert into oc_coupon_history set coupon_id=$coupon_id, order_id=$order_id, customer_id=$customer_id,
				amount=$coupon_total, date_added=now()");
		}
		
		return $total;
	}
	
	//折扣劵
	//输入订单金额，返回优惠后金额，false优惠已失效
	public function discountCoupon($customer_id, $coupon_id, $order_id, $total) {
		$result = $this->db->query("select * from oc_coupon_customer cc
			join oc_coupon c on cc.coupon_id=c.coupon_id where customer_id = $customer_id
			and status>0 and (current_date() >= date_start and current_date() <= date_end)
			and cc.coupon_id = $coupon_id");

		if ($result->num_rows <= 0)
			return false;
			
		$remain = $total * $result->row['discount'] / 100;
		$coupon_total = $total - $remain;
		
		$this->db->query("insert into oc_coupon_history set coupon_id=$coupon_id, order_id=$order_id, customer_id=$customer_id,
				amount=$coupon_total, date_added=now()");
		
		return $remain;
	}
	
	public function commitCoupon($customer_id, $coupon_id, $order_id, $total) {
		if ($coupon_id == 0)
			$ret = $this->cashCoupon($customer_id, $order_id, $total);
		else
			$ret = $this->discountCoupon($customer_id, $coupon_id, $order_id, $total);
		
		if ($ret == false) return false;
		
		$coupon_total = $total - $ret;
		$this->db->query("update oc_order set coupon_total=$coupon_total where order_id=$order_id");
		return true;
	}
	
	public function getCoupon($code) {
		$status = true;
		
		$coupon_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "coupon` WHERE code = '" . $this->db->escape($code) . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) AND status = '1'");
			
		if ($coupon_query->num_rows) {
			if ($coupon_query->row['total'] >= $this->cart->getSubTotal()) {
				$status = false;
			}
		
			$coupon_history_query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "coupon_history` ch WHERE ch.coupon_id = '" . (int)$coupon_query->row['coupon_id'] . "'");

			if ($coupon_query->row['uses_total'] > 0 && ($coupon_history_query->row['total'] >= $coupon_query->row['uses_total'])) {
				$status = false;
			}
			
			if ($coupon_query->row['logged'] && !$this->customer->getId()) {
				$status = false;
			}
			
			if ($this->customer->getId()) {
				$coupon_history_query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "coupon_history` ch WHERE ch.coupon_id = '" . (int)$coupon_query->row['coupon_id'] . "' AND ch.customer_id = '" . (int)$this->customer->getId() . "'");
				
				if ($coupon_query->row['uses_customer'] > 0 && ($coupon_history_query->row['total'] >= $coupon_query->row['uses_customer'])) {
					$status = false;
				}
			}
		
			$product_data = array();
		
			// Products
			$coupon_product_data = array();
		
			$coupon_product_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "coupon_product` WHERE coupon_id = '" . (int)$coupon_query->row['coupon_id'] . "'");
			
			foreach ($coupon_product_query->rows as $product) {
				$coupon_product_data[] = $product['product_id'];
			}
			
			// Categories
			$coupon_category_data = array();
			
			$coupon_category_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "coupon_category` cc LEFT JOIN `" . DB_PREFIX . "category` c ON (cc.category_id = c.category_id) WHERE coupon_id = '" . (int)$coupon_query->row['coupon_id'] . "'");
			
			foreach ($coupon_category_query->rows as $category) {
				$coupon_category_data[] = $category;
			}			
			
			if ($coupon_product_data || $coupon_category_data) {
				foreach ($this->cart->getProducts() as $product) {
					if (in_array($product['product_id'], $coupon_product_data)) {
						$product_data[] = $product['product_id'];
						
						continue;
					}
					
					foreach ($coupon_category_data as $category) {
						$coupon_category_query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "product_to_category` p2c LEFT JOIN `" . DB_PREFIX . "category` c ON (p2c.category_id = c.category_id) WHERE p2c.`product_id` = '" . (int)$product['product_id'] . "' AND c.`left` BETWEEN '" . (int)$category['left'] . "' AND '" . (int)$category['right'] . "'");
						
						if ($coupon_category_query->row['total']) {
							$product_data[] = $product['product_id'];
							
							continue;
						}						
					}
				}	
			
				if (!$product_data) {
					$status = false;
				}
			}
		} else {
			$status = false;
		}
		
		if ($status) {
			return array(
				'coupon_id'     => $coupon_query->row['coupon_id'],
				'code'          => $coupon_query->row['code'],
				'name'          => $coupon_query->row['name'],
				'type'          => $coupon_query->row['type'],
				'discount'      => $coupon_query->row['discount'],
				'shipping'      => $coupon_query->row['shipping'],
				'total'         => $coupon_query->row['total'],
				'product'       => $product_data,
				'date_start'    => $coupon_query->row['date_start'],
				'date_end'      => $coupon_query->row['date_end'],
				'uses_total'    => $coupon_query->row['uses_total'],
				'uses_customer' => $coupon_query->row['uses_customer'],
				'status'        => $coupon_query->row['status'],
				'date_added'    => $coupon_query->row['date_added']
			);
		}
	}
	
	public function redeem($coupon_id, $order_id, $customer_id, $amount) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "coupon_history` SET coupon_id = '" . (int)$coupon_id . "', order_id = '" . (int)$order_id . "', customer_id = '" . (int)$customer_id . "', amount = '" . (float)$amount . "', date_added = NOW()");
	}
}
?>