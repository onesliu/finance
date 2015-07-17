<span>
	<h4 style="margin:.2em 0;">可用优惠劵</h4>
	<?php if (isset($coupons)) { ?>
    <select id="coupon-select" name="coupon-select" onchange="selectcoupon(this.value);">
      	<option value="-1">不使用优惠劵</option>
      	<?php foreach($coupons as $coupon) { ?>
      	<option value="<?php echo $coupon['coupon_id']; ?>"><?php echo $coupon['name'];?></option>
      	<?php } ?>
    </select>
    <?php } else { ?>
    <p>没有可用优惠劵</p>
    <?php } ?>
</span>
<script type="text/javascript"><!--
var Coupon = function() {
	this.getTotal = function() {
		return 0.0;
	}
	this.setCoupon = function(discount, remain) {
	}
	this.resetCoupon = function() {
		$("#coupon-select").val(-1);
	}
}

var coupon = new Coupon();

function selectcoupon(coupon_id) {

	if (coupon_id == -1) {
		coupon.resetCoupon();
		return;
	}
	
	var url = '<?php echo $coupon_url; ?>';
	url += '&order_total=' + coupon.getTotal() + '&coupon_id=' + coupon_id;
	
	$.get(url, null, function(data){
			var ret = eval("("+data+")");
			
			if (ret.status == 0) {
				coupon.setCoupon(ret.discount, ret.remain);
			}
			else {
				alert('计算优惠金额错误');
				coupon.resetCoupon();
			}
	});
}
//--></script>