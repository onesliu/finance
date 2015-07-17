<?php
//发行优惠劵通用类
class Coupon {
	private $db;
	private $log;
	
	public function __construct($registry) {
		$this->db = $registry->get('db');
		$this->log = $registry->get('log');
	}
	
	/* 说明：优惠劵有三种：现金劵、折扣劵、无限使用的折扣劵
	 * 参数：
	 * count：发行数量可以是0，只针对无限使用的折扣劵
	 * limit：false 针对后台配置现金劵可以超过“客户最大领取量”的限制
	 * 
	 * 返回值：
	 * FAILED：发行失败
	 * DISABLED：优惠劵停用
	 * EXPIRED：优惠劵过期
	 * COMPLETED：已经全部发行完
	 * LIMITED：超过客户最多发行量限制
	 * array($total_release, $total_amount)：返回最终发行数量和面值，折扣劵的面值始终是0
	 * */
	public function releaseCoupon($customer_id, $coupon_id, $count, $limit = true) {
		$query = $this->db->query("select * from oc_coupon where coupon_id=$coupon_id");
		if ($query->num_rows != 1) {
			return 'FAILED'; //发行失败，没有优惠劵
		}
		$row = $query->row;
		
		if ($row['status'] != null && $row['status'] == 0) {
			return 'DISABLED'; //优惠劵停用
		}
		
		if ($row['date_end'] != null) {
			$now = time();
			$endtime = strtotime($row['date_end']);
			if ($endtime < $now) {
				return 'EXPIRED'; //优惠劵过期
			}
		}
		
		if ($count < 0) { //发行张数<0，扣减
			$total_release = $count;
			if ($row['type'] == 'P') //如果是折扣劵
				$total_amount = 0;
			else
				$total_amount = $count * $row['discount'];
			
			$query = $this->db->query("select counts from oc_coupon_customer where coupon_id=$coupon_id and customer_id=$customer_id");
			if ($query->num_rows <= 0) { //未发行
				return array(0,0);
			}
			$customer_count = $query->row['counts'];
			
			if ($customer_count + $count <= 0) {
				$query = $this->db->query("delete from oc_coupon_customer where coupon_id=$coupon_id and customer_id=$customer_id");
				if ($query == false) {
					return 'FAILED'; //扣减失败，数据库问题
				}
				return array($customer_count, $total_amount);
			}
		}
		else if ($count == 0) { //发行张数是0，只针对无限折扣劵
			$total_release = 0;
			$total_amount = 0;
			if ($row['type'] != 'P') {
				return 'FAILED'; //不是折扣劵报错
			}
		}
		else if ($count > 0) { //发行有张数的现金劵或折扣劵
			
			$total_release = $count; //不限制总量时发行需求数量
			if ($row['uses_total'] > 0) { //大于0才检查总量
				$release_count = 0;
				$query = $this->db->query("select sum(counts) as RealseCount from oc_coupon_customer where coupon_id=$coupon_id");
				if ($query->num_rows > 0 && $query->row['RealseCount'] != null)
					$release_count = $query->row['RealseCount'];
					
				if ($release_count >= $row['uses_total']) {
					return 'COMPLETED'; //已经全部发行完
				}
				//计算不超过总量的可发行数量
				$total_release = $row['uses_total'] - $release_count;
				if ($total_release > $count)
					$total_release = $count; //最多不超过需要的发行量
			}
	
			if ($limit == true) { //需要检查是否超过客户最多发行量的限制
				$limit_release = $count; //不限制客户量时发行需求数量
				if ($row['uses_customer'] > 0) { //大于0才限制
					$customer_count = 0;
					$query = $this->db->query("select counts from oc_coupon_customer where coupon_id=$coupon_id and customer_id=$customer_id");
					if ($query->num_rows > 0)
						$customer_count = $query->row['counts'];
				
					if ($customer_count >= $row['uses_customer']) {
						return 'LIMITED'; //超过客户最多发行量限制
					}
					//计算客户还能发行多少
					$limit_release = $row['uses_customer'] - $customer_count;
					if ($limit_release > $count)
						$limit_release = $count; //最多不超过需求的发行量
				}
				
				$total_release = min($total_release, $limit_release); //在总量和客户限制中找出最小的可发行量
			}
			
			if ($row['type'] == 'F')
				$total_amount = $total_release * $row['discount']; //发行面值合计
			else
				$total_amount = 0;
		}

		//发行
		$query = $this->db->query("insert into oc_coupon_customer set coupon_id=$coupon_id, customer_id=$customer_id,
			counts=$total_release, amount=$total_amount on duplicate key update counts=counts+$total_release, amount=amount+$total_amount");
		if ($query == false) {
			return 'FAILED'; //发行失败，数据库问题
		}
		
		return array($total_release, $total_amount); //返回最终发行数量和面值
	}
}
?>