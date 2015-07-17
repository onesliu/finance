<body>
<div data-role="page" id="kforderdetail" data-theme="a">
<style type="text/css">
.ui-table-columntoggle-btn {
    display: none !important;
}
.couponshow {}
</style>
	<div data-role="header" data-position="fixed" data-theme="a">
		<a href="#" data-rel="back" class="ui-btn ui-icon-arrow-l ui-btn-icon-notext ui-corner-all"></a>
	    <h1 style="padding:.4em 0;"><img src="<?php echo $logo; ?>" style="height:25px;" /></h1>
	</div>
	<div data-role="content">
		<?php if (!isset($heading_title)) { ?>
			<div class="ui-body ui-body-b ui-corner-all">
				<p>没有订单</p>
			</div>
		<?php } else { ?>
			<div class="ui-body ui-body-b ui-corner-all" style="margin-bottom:.4em;">
				<p><?php echo $text_order_id; ?><?php echo $order_id; ?></p>
				<p><?php echo $text_date_added; ?><?php echo $date_added; ?></p>
			</div>
			<div class="ui-body ui-body-b ui-corner-all" style="margin-bottom:.4em;">
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
			        <td><?php echo $product['quantity']; ?></td>
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
			      <?php } ?>
			    </tbody>
			  </table>
			</div>
			<div class="ui-body ui-body-b ui-corner-all" style="margin-bottom:.4em;">
			  <table data-role="table" data-mode="columntoggle" class="ui-responsive table-stripe">
			    <thead>
			      <tr>
			        <td data-priority="1"><?php echo $text_history; ?></td>
			        <td data-priority="2"><?php echo $column_status; ?></td>
			        <td data-priority="3"><?php echo $column_comment; ?></td>
			      </tr>
			    </thead>
			    <tbody>
			      <?php foreach ($histories as $history) { ?>
			      <tr>
			        <td><?php echo $history['date_added']; ?></td>
			        <td><?php echo $history['status']; ?></td>
			        <td><?php echo $history['comment']; ?></td>
			      </tr>
			      <?php } ?>
			    </tbody>
			  </table>
			</div>
			<div class="ui-body ui-body-b ui-corner-all" style="margin-bottom:.4em;">
				<table data-role="table" data-mode="columntoggle" class="ui-responsive table-stroke">
				    <thead>
				      <tr>
				        <td data-priority="1">配送信息</td>
				      </tr>
				    </thead>
				    <tbody>
				    	<tr><td><?php echo $shipping_address; ?></td></tr>
				    	<tr><td><?php echo $shipping_district; ?><br/><?php echo $shipping_district_addr; ?></td></tr>
				    	<tr><td>希望收货时间：<?php echo $shipping_time; ?></td></tr>
				    </tbody>
			  	</table>
			</div>
		<?php } ?>
	</div>
</div>
</body>
