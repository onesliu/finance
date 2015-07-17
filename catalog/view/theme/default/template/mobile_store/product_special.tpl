<?php echo $header; ?>
<body>
<div data-role="page" id="product_page" data-theme="a" data-title="<?php echo $heading_title; ?>">
	<?php echo $titlebar; ?>
	<div data-role="content">
		<div>
			<div class="swiper-container">
				<div class="swiper-wrapper">
					<?php foreach($images as $img) {?>
					<div class="swiper-slide">
						<img src="<?php echo $img['popup']; ?>" />
					</div>
					<?php } ?>
				</div>
				<div class="swiper-pagination swiper-pagination-white"></div>
			</div>

			<div>
				<h3 style="margin-top:.4em;margin-bottom:.4em;"><?php echo $heading_title; ?></h3>
				<h4 style="margin-top:.4em;">
					<span style="color:red;">￥</span><span style="color:red;" id="price"><?php echo $sellprice; ?></span>
					<span class="light-font">/<?php echo $sellunit; ?></span>
					<span class="light-font" style="margin-left: 1em;">已售<?php echo ($sellcount*$unitcount).$unit; ?></span>
				</h4>
			</div>
		</div>
		
		<form id="preorder" name="preorder" method="post" action="<?php echo $special_url; ?>">
		<?php if ($options) { $osubject = ""; ?>
			<div class="line-box">
				<div style="margin-top:.3em;">请选择如下选项，价格会随之变化：</div>
				<?php for($i = 0; $i < count($options); $i++) {
					$option = $options[$i];
					$osubject .= $option['name'].",";
				?>
				<div class="option-block">
					<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
					<legend><?php echo $option['name']; ?>：</legend>
					<?php foreach ($option['option_value'] as $option_value) {
						$option_value["oid"] = "op".$option_value['product_option_value_id'];
						?>
						<input type="radio"
							name="options[<?php echo $option['product_option_id']; ?>]"
							id="op<?php echo $option_value['product_option_value_id']; ?>" 
							value="<?php echo $option_value['product_option_value_id']; ?>"
							onclick="change_price(<?php echo $i; ?>, '<?php echo $option_value['price_prefix'].$option_value['price']; ?>')" />
    					<label for="op<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?></label>
					<?php } ?>
					</fieldset>
				</div>
				<?php } ?>
			</div>
		<?php } ?>
		</form>
		
		<div class="line-box">
			<?php if ($attribute_groups) { ?>
				<?php foreach ($attribute_groups as $attribute_group) { ?>
				<?php foreach ($attribute_group['attribute'] as $attribute) { ?>
					<div class="attr-block"><?php echo $attribute['name']; ?>：<?php echo $attribute['text']; ?></div>
				<?php }} ?>
			<?php } ?>
		</div>
		
		<div class="line-box">
			<h4>商品详情</h4>
			<p><?php echo $description; ?></p>
		</div>
		
		<div data-role="popup" id="positionWindow" data-transition="slideup" data-position-to="window" class="ui-content" data-theme="a">
			<p id="buy_alert"></p>
		</div>
		
	</div>
	<div data-role="footer" data-position="fixed" data-theme="d" data-tap-toggle="false">
		<span><center>
			<input type="button" id="checkoutbtn" class="ui-btn ui-corner-all ui-shadow ui-btn-d ui-btn-inline" onclick="submit_order();" value="<?php echo $special_btn; ?>" />
		</center></span>
	</div>

	<script type="text/javascript"><!--
	$(document).on("pageinit", "#product_page", function(){
		setTimeout(function(){
			var mySwiper = new Swiper('.swiper-container',{
			    pagination: '.swiper-pagination',
			    paginationClickable: true,
			    autoplay: 2000,
			    loop: true
			  });
			mySwiper.update();
		}, 500);

		var price = <?php echo $sellprice; ?>;
		$('#price').text(price.toFixed(2));
		$("input[type='radio']").attr("checked",false).checkboxradio("refresh");
		$("#checkoutbtn").button( "enable" );
		$("#checkoutbtn").button( "refresh" );
	});

	var group = new Array();
	function change_price(groupid, of_price) {
		var price = <?php echo $sellprice; ?>;
		group[groupid] = (of_price-0);
		for(x in group) {
			price += group[x];
		}
		$('#price').text(price.toFixed(2));
	}

	function submit_order() {
		$("#checkoutbtn").button( "disable" );
		$("#checkoutbtn").button( "refresh" );
		$('#preorder').submit();
	}
	//--></script>
</div>
</body>
