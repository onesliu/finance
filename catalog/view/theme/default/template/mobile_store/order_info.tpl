<?php echo $header; ?>
<body>
<div data-role="page" id="orderinfopage" data-theme="a" data-title="<?php echo $heading_title; ?>">
<style type="text/css">
.ui-table-columntoggle-btn {
    display: none !important;
}
.couponshow {}
</style>
	<?php echo $titlebar; ?>
	<div data-role="content">
		<div class="ui-body" style="margin-bottom:.4em;">
			<p><?php echo $text_order_id; ?><?php echo $order_id; ?>
				<?php if (isset($cancel_order)) { ?>
				<span style="color:blue" onclick="onCancelOrder();"> [<?php echo $text_cancel_btn; ?>]</span>
				<?php } ?>
			</p>
			<p><?php echo $text_date_added; ?><?php echo $date_added; ?></p>
		</div>
		<div class="ui-body line-box">
		  <table data-role="table" data-mode="columntoggle" class="ui-responsive table-stripe">
		    <thead>
		      <tr>
		        <td data-priority="1"><?php echo $column_name; ?></td>
		        <td data-priority="2"><?php echo $column_quantity; ?></td>
		        <td data-priority="3"><?php echo $column_weight; ?></td>
		        <td data-priority="4"><?php echo $column_total; ?></td>
		      </tr>
		    </thead>
		    <tbody>
		      <?php foreach ($products as $product) { ?>
		      <tr>
		        <td><?php echo $product['name']; ?></td>
		        <td><?php echo $product['quantity'].$product['sellunit']; ?></td>
		        <td><?php echo $product['weight'].'g'; ?></td>
		        <td><?php echo $product['total']; ?></td>
		      </tr>
		      <?php } ?>
		      <tr>
		      <tfoot>
      		  <?php foreach ($totals as $total) {  ?>
      		  <tr>
      		  	<td> </td>
		        <td colspan="2"><?php echo $total['title']; ?>：</td>
		        <td><span id="total" style="color:red;"><?php echo $total['text']; ?></span>
		        	<span id="totalprice" style="display:none;"><?php echo $total['value']; ?></span>
		        </td>
		      </tr>
      		  <tr class="couponshow">
      		  	<td> </td>
		        <td colspan="2">优惠金额：</td>
		        <td><span id="discount" style="color:red;">￥0.00</span></td>
		      </tr>
      		  <tr class="couponshow">
      		  	<td> </td>
		        <td colspan="2">实际应付：</td>
		        <td><span id="realtotal" style="color:red;"><?php echo $total['text']; ?></span></td>
		      </tr>
		      <?php } ?>
		    </tbody>
		  </table>
		</div>
		<?php if (isset($weixin_payment)) {?>
		<div id="coupon" class="ui-body line-box">
		<?php echo $coupon; ?>
		</div>
		<?php } ?>
		<div class="ui-body line-box">
		  <table data-role="table" data-mode="columntoggle" class="ui-responsive table-stripe">
		    <thead>
		      <tr>
		        <td data-priority="1"><?php echo $text_history; ?></td>
		        <td data-priority="2"><?php echo $column_status; ?></td>
		      </tr>
		    </thead>
		    <tbody>
		      <?php foreach ($histories as $history) { ?>
		      <tr>
		        <td><?php echo $history['date_added']; ?></td>
		        <td><?php echo $history['status']; ?></td>
		      </tr>
		      <?php } ?>
		    </tbody>
		  </table>
		</div>
		<div class="ui-body line-box">
			<table data-role="table" data-mode="columntoggle" class="ui-responsive table-stroke">
			    <thead>
			      <tr>
			        <td data-priority="1">配送信息</td>
			      </tr>
			    </thead>
			    <tbody>
			    	<tr><td><?php echo $shipping_address; ?></td></tr>
			    	<?php if ($order_type < 2) { ?>
			    	<tr><td><?php echo $shipping_district; ?><br/><?php echo $shipping_district_addr; ?></td></tr>
			    	<tr><td>希望收货时间：<?php echo $shipping_time; ?></td></tr>
			    	<?php } ?>
			    </tbody>
		  	</table>
		</div>
		<?php if (isset($weixin_payment)) {?>
		<div data-role="footer" data-position="fixed" data-theme="b" data-tap-toggle="false">
			<span><center><a id="paybtn" href="<?php echo $weixin_payment; ?>" class="ui-btn ui-btn-a ui-shadow ui-corner-all ui-btn-inline"><?php echo $text_pay_btn;?></a>
			<span style="margin-left:2em;"> </span>
			<a id="cashpay" href="<?php echo $url_cashpay; ?>" class="ui-btn ui-btn-a ui-shadow ui-corner-all ui-btn-inline"><?php echo $text_cashpay;?></a>
			</center></span>
		</div>
		<?php } ?>
	</div>
	<script type="text/javascript"><!--
	<?php if (isset($cancel_order)) { ?>
	var cancel_url = "<?php echo $cancel_order; ?>";
	var cancel_text = "确定" + "<?php echo $text_cancel_btn; ?>" + "订单吗？";
	function onCancelOrder() {
		if (window.confirm(cancel_text)) {
			location.href = cancel_url;
		}
	}
	<?php } ?>
	
	<?php if (isset($weixin_payment)) {?>
	var CartCoupon = function() {
		this.superclass.call(this);
		this.getTotal = function() {
			return $('#totalprice').text() - 0;
		}
		this.setCoupon = function(discount, remain) {
			discount = discount - 0;
			remain = remain - 0;
			$('#discount').html('￥' + discount.toFixed(2));
			$('#realtotal').html('￥' + remain.toFixed(2));

			if (remain <= 0) {
				$("#paybtn").text("优惠劵支付");
			}
			else {
				$("#paybtn").text("<?php echo $text_pay_btn;?>");
			}
		}
		this.resetCoupon = function() {
			$('#discount').html('￥0.00');
			$("#realtotal").html($("#total").text());
			$("#paybtn").text("<?php echo $text_pay_btn;?>");

			var baseCoupon = new Coupon();
			baseCoupon.resetCoupon.call(this);
		}
	}
	<?php } ?>

	if (typeof selectcoupon != "undefined") {
		$("#coupon").show();
		$(".couponshow").show();
		CartCoupon = E.extend(CartCoupon, Coupon);
		coupon = new CartCoupon();
	}
	else {
		$("#coupon").hide();
		$(".couponshow").hide();
	}
	//--></script>
</div>
</body>
