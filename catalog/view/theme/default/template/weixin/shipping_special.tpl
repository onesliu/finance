<?php echo $header; ?>
<body>
<style type="text/css">
.labelh{margin:.2em 0;}
</style>
<div data-role="page" data-theme="a" id="shipping_special_page" data-title="<?php echo $name; ?>">
	<?php echo $titlebar; ?>
	<div data-role="content">
		<form id="weixin_payment" name="weixin_payment" method="post" action="#">
		<div>
			<img src="<?php echo $images[0]['popup']; ?>" style="width:100%;"/>
			<div class="ui-body">
				<h3 style="margin-top:.4em;margin-bottom:.4em;"><?php echo $name; ?></h3>
				<h4 style="margin-top:.4em;"><span style="color:red;">￥</span><span style="color:red;" id="price"><?php echo $price; ?></span><span class="light-font">/<?php echo $unit; ?></span></h4>
			</div>
		</div>
		
		<?php if ($options) { $osubject = ""; ?>
			<div class="ui-body line-box">
				<div>请选择如下选项，价格会随之变化：</div>
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
		
		<div class="ui-body line-box">
			<div style="width:25%;float:left;"><h4 class="labelh">购买数量</h4></div>
			<div style="width:75%;float:left;">
				<div class="ui-btn ui-btn-icon-notext ui-icon-minus ui-btn-inline ui-input-btn ui-corner-all"
					style="margin:0 1em;" onclick="changecount(-1);">
    			</div>
  				<span id="pcount" style="border:solid 1px #eeeeee;padding:.2em .6em">1</span>
  				<div class="ui-btn ui-btn-icon-notext ui-icon-plus ui-btn-inline ui-input-btn ui-corner-all"
  					style="margin:0 1em;" onclick="changecount(1);">
  				</div>
  			</div>
		</div>
		
	    <div class="ui-body line-box">
	    	<div style="width:25%;float:left;"><h4 class="labelh">订单合计</h4></div>
	    	<div style="width:75%;float:left;"><h4 class="labelh" id="total" style="color:red;">￥0.00</h4></div>
	    </div>

	    <div id="shipping_address" class="ui-body line-box" onclick="editaddr();">
		    <h4 class="labelh">收货地址</h4>
			<p id="addr" <?php if (!isset($address)) echo "style=\"display:none\""; ?> >
			    <div><span id="user_name"><?php if (isset($address)) {echo $address['firstname']; echo $address['lastname'];} ?></span>
			    	<span id="user_telephone"><?php if (isset($address)) {echo $address['telephone'];} ?></span></div>
			    <div id="user_addr"><?php if (isset($address)) {echo $address['address_1'];} ?></div>
			    <input type="hidden" name="user_name" id="user_name2" value="<?php if (isset($address)) {echo $address['firstname']; echo $address['lastname'];} ?>"></input>
			    <input type="hidden" name="user_telephone" id="user_telephone2" value="<?php if (isset($address)) {echo $address['telephone'];} ?>"></input>
			    <input type="hidden" name="user_addr" id="user_addr2" value="<?php if (isset($address)) {echo $address['address_1'];} ?>"></input>
			    <input type="hidden" name="user_city" id="user_city" value="<?php if (isset($address)) {echo $address['city'];} ?>"></input>
			    <input type="hidden" name="user_postcode" id="user_postcode" value="<?php if (isset($address)) {echo $address['postcode'];} ?>"></input>
	      	</p>
			<p id="addr_none" <?php if (isset($address)) echo "style=\"display:none\""; ?> >
				<span class="checkout-heading" style="color:#9999ff;">点击编辑收货地址</span>
			</p>
	    </div>
	    
		<div class="ui-body line-box" style="text-align:center;">
			<input type="button" id="checkoutbtn" onclick="checkout();" class="ui-btn ui-corner-all ui-shadow ui-btn-d ui-btn-inline" value="立 即 预 订" />
		</div>
		</form>
    </div>
	<script type="text/javascript"><!--
	$(document).on("pageinit", "#shipping_special_page", function(){
		var price = <?php echo $price; ?>;
		$('#price').text(price.toFixed(2));

		<?php foreach($option_selected as $k => $v) { ?>
		$("#op<?php echo $v; ?>").prop( "checked", true ).checkboxradio( "refresh" );
		$("#op<?php echo $v; ?>").click();
		<?php } ?>
	});

	function changecount(num) {
		var count = $('#pcount').text() - 0;
		count += num;
		if (count > 0) {
			$('#pcount').text(count);
			change_total();
		}
	}

	var group = new Array();
	function change_price(groupid, of_price) {
		var price = <?php echo $price; ?>;
		group[groupid] = (of_price-0);
		for(x in group) {
			price += group[x];
		}
		$('#price').text(price.toFixed(2));
		change_total();
	}

	function change_total() {
		var count = $('#pcount').text()-0;
		var price = $('#price').text()-0;
		var total = count * price;
		$('#total').text('￥' + total.toFixed(2));
	}

	function checkout(){
		if (typeof check_form != "undefined") {
			if (check_form()) {
				var action_url = "<?php echo $checkout_url; ?>" + '&quantity=' + $('#pcount').text();
				$("#checkoutbtn").button( "disable" );
				$("#checkoutbtn").button( "refresh" );
				$('#weixin_payment').attr("action", action_url);
				$('#weixin_payment').submit();
			}
		}
	};
	
	function check_form() {
		<?php if ($options) { ?>
			var option_count = <?php echo count($options); ?>;
			if (group.length < option_count) {
				alert('请选择：' + "<?php echo trim($osubject, ","); ?>");
				return false;
			}
		<?php } ?>
		
		if ($('#addr').css("display") == "none") {
			alert('请编辑一个收货地址（电话），我们服务过程中如果遇到任何问题将会电话联络您。');
			return false;
		}
		
		return true;
	}
	
	function editaddr() {
		if (typeof WeixinJSBridge == "undefined") {
			alert("请通过微信加载该页面");
		}
		else {
			WeixinJSBridge.invoke('editAddress',{
				"appId" : "<?php echo $addrParam['appId']; ?>",
				"scope" : "jsapi_address",
				"signType" : "sha1",
				"addrSign" : "<?php echo $addrParam['addrSign']; ?>",
				"timeStamp" : "<?php echo $addrParam['timeStamp']; ?>",
				"nonceStr" : "<?php echo $addrParam['nonceStr']; ?>",
				},function(res){
				//若res 中所带的返回值不为空，则表示用户选择该返回值作为收货地
				//址。否则若返回空，则表示用户取消了这一次编辑收货地址。
					if (res != null) {
						if (res.err_msg == "edit_address:ok") {
							$('#user_name').text(res.userName);
							$('#user_telephone').text(res.telNumber);
							$('#user_addr').text(res.proviceFirstStageName + 
												res.addressCitySecondStageName +
												res.addressCountiesThirdStageName + " " +
												res.addressDetailInfo);
							$('#user_name2').val(res.userName);
							$('#user_telephone2').val(res.telNumber);
							$('#user_addr2').val($('#user_addr').text());
							$('#user_city').val(res.addressCitySecondStageName);
							$('#user_postcode').val(res.addressPostalCode);
							
							$('#addr_none').css("display", "none");
							$('#addr').css("display", "block");
						}
					}
				});
		}
	}
	//--></script>
</div>
</body>
    