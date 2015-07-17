<?php echo $header; ?>
<body>
<div data-role="page" id="checkoutpage" data-theme="a" data-title="<?php echo $heading_title; ?>">
<style type="text/css">
.ui-table-columntoggle-btn {
    display: none !important;
}
</style>
	<?php echo $titlebar; ?>
	<div data-role="content">
		<h4 class="ui-bar" style="margin:3px 0 1px 0;">订单明细</h4>
		<div class="ui-body ui-body-b ui-corner-all" style="margin-bottom:.4em;">
		  <table data-role="table" data-mode="columntoggle" class="ui-responsive table-stroke">
		    <thead>
		      <tr>
		        <td data-priority="1"><?php echo $column_name; ?></td>
		        <td data-priority="2"><?php echo $column_quantity; ?></td>
		        <td data-priority="3"><?php echo $column_total; ?></td>
		      </tr>
		    </thead>
		    <tbody>
		      <?php foreach ($products as $product) { ?>
		      <tr>
		        <td><?php echo $product['name']; ?></td>
		        <td><?php echo $product['quantity']; ?></td>
		        <td><?php echo $product['total']; ?></td>
		      </tr>
		      <?php } ?>
		      <tr>
		      <tfoot>
		        <?php foreach ($totals as $total) {  ?>
		        <tr>
		        <td> </td>
		        <td><?php echo $total['title']; ?>:</td>
		        <td><?php echo $total['text']; ?></td>
		      </tr>
		      <?php } ?>
		    </tbody>
		  </table>
		</div>
		
		<form id="weixin_payment" name="weixin_payment" method="post" action="<?php echo $weixin_payment; ?>">
	    <?php echo $shipping; ?>
	    </form>
		
		<a onclick="if (check_form()) {$('#weixin_payment').submit();}" class="ui-btn ui-btn-a ui-corner-all"><?php echo $text_pay_btn; ?></a>
	</div>
</div>
</body>