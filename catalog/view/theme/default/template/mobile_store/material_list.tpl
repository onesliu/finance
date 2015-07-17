<?php if ($source_major || $source_minor) { ?>
<form action="#" id="materials">
<ul data-role="listview" data-inset="true" data-shadow="false" data-theme="c" data-divider-theme="c" data-count-theme="c">

	<?php if ($source_major) { ?>
	<li data-role="list-divider">原料自由组合</li>
	<li class="ui-field-contain">
		<p style="white-space:normal;">如您需要不同的份量可以自由选择：</p>
		<fieldset data-role="controlgroup">
			<?php foreach ($source_major as $source) { $mid='m'.rand(); ?>
			<label for="<?php echo $mid; ?>" class="ui-alt-icon"><a href="#" onclick="chgpage('<?php echo $source['href'];?>');"><?php echo $source['final_show']; ?></a>
				<span style="color:red;"><?php echo $source['price_show']; ?></span><?php echo '/'.$source['sku']; ?></label>
			<input data-iconpos="right" type="checkbox"
				   name="product[<?php echo $source['product_id']; ?>]"
				   id="<?php echo $mid; ?>" 
				   <?php if ($source['status'] <= 0) echo 'disabled="disabled"'; ?>
				   <?php if ($source['groupid'] > 0) echo 'checked="checked"'; ?>
				   price="<?php echo $source['price'];?>"
				   onclick="calc_price();"
			/>
			<?php } ?>
		</fieldset>
	</li>
	<?php }
	if ($source_minor) { ?>
	<li data-role="list-divider">辅料及调味品</li>
	<li class="ui-field-contain">
		<fieldset data-role="controlgroup">
			<?php foreach ($source_minor as $source) { $mid='m'.rand(); ?>
			<label for="<?php echo $mid; ?>" class="ui-alt-icon"><a href="#" onclick="chgpage('<?php echo $source['href'];?>');"><?php echo $source['final_show']; ?></a>
				<span style="color:red;"><?php echo $source['price_show']; ?></span><?php echo '/'.$source['sku']; ?></label>
			<input data-iconpos="right" type="checkbox"
				   name="product[<?php echo $source['product_id']; ?>]"
				   id="<?php echo $mid; ?>" 
				   <?php if ($source['status'] <= 0) echo 'disabled="disabled"'; ?>
				   <?php if ($source['groupid'] > 0) echo 'checked="checked"'; ?>
				   price="<?php echo $source['price'];?>"
				   onclick="calc_price();"
			/>
			<?php } ?>
		</fieldset>
	</li>
	<?php } ?>
	
	<li class="ui-body ui-body-b">
		<h4 style="white-space:normal;">已选择的原料合计：<span id="total" style="color:red;font:bold;"></span></h4>
		<button type="button" onclick="addToCart();" class="ui-btn ui-btn-a ui-corner-all ui-icon-cart ui-btn-icon-right">加入购物车</button>
	</li>
	
	<div data-role="popup" id="positionWindow" data-transition="slideup" data-position-to="window" class="ui-content" data-theme="a">
		<p id="buy_alert"></p>
	</div>
	
	<script type="text/javascript"><!--
	function addToCart() {
		quantity = typeof(quantity) != 'undefined' ? quantity : 1;

		$.ajax({
			url: 'index.php?route=mobile_store/cart/add',
			type: 'post',
			data: $('#materials').serialize(),
			dataType: 'json',
			success: function(json) {
				if (json.success) {
					$('#buy_alert').html(json.success);
					
					$('#positionWindow').popup( 'reposition', 'positionTo: window' );
					$('#positionWindow').popup('open', { positionTo: "window" });
					setTimeout(function() {
						$("#positionWindow").popup('close');
					}, 1000);
				}
			}
		});
	}

	function calc_price() {
		var total = 0;
		$("input[type='checkbox']").each(function(){
			if ($(this).is(':checked') == true)
				total += ($(this).attr('price') - 0);
		});
		$('#total').text('￥' + total.toFixed(2));
	}

	//--></script> 
</ul>
</form>
<?php } ?>
