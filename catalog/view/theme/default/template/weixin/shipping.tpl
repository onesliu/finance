<?php echo $header; ?>
<body>
<style type="text/css">
.labelh{margin:.2em 0;}
</style>
<div data-role="page" data-theme="a" id="shipping_page" data-title="订单地址">
	<?php echo $titlebar; ?>
	<div data-role="content">
		<form id="weixin_payment" name="weixin_payment" method="post" action="<?php echo $checkout; ?>">
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
					<span class="checkout-heading">点击选择收货地址</span>
				</p>
		    </div>
		
		    <div class="ui-body line-box">
			    <h4 class="labelh">就近称重门店</h4>
			    <select id="district-select" name="district-select">
		      		<option value="0">请选择就近的称重门店</option>
		      		<?php foreach($shipping_districts as $district) {
		      			if (isset($address) && ($district['id'] == $address['district_id'])) {
		      		?>
		      		<option selected="selected" value="<?php echo $district['id']; ?>"><?php echo $district['name'];?></option>
		      		<?php } else { ?>
		      		<option value="<?php echo $district['id']; ?>"><?php echo $district['name'];?></option>
		      		<?php }} ?>
		      	</select>
		      	<h4 class="labelh">收货时间</h4>
		      	<select name="time-select">
		      		<?php foreach($shipping_time as $sk => $st) {?>
		      		<option value="<?php echo $sk; ?>"><?php echo $st; ?></option>
		      		<?php } ?>
		      	</select>
		      	<span><?php echo "上午".$first_shipping_time."截止下单，下午".$last_shipping_time."截止下单。"; ?><br/>
		      	</span>
		    </div>
	    </form>
	    <div class="ui-body line-box" style="text-align:center;">
	    	<?php if (isset($gap_time)) { ?>
	    	<input type="button" disabled="" class="ui-btn ui-corner-all ui-shadow ui-btn-d ui-btn-inline" value="<?php echo $gap_time; ?>后可下明日订单" />
	    	<?php } else {?>
			<input type="button" id="checkoutbtn" onclick="checkout();" class="ui-btn ui-corner-all ui-shadow ui-btn-d ui-btn-inline" value="<?php echo $checkout_btn;?>" />
			<?php } ?>
		</div>
	</div>
	<script type="text/javascript"><!--
	
	function checkout(){
		if (check_form()) {
			$("#checkoutbtn").button( "disable" );
			$("#checkoutbtn").button( "refresh" );
			$('#weixin_payment').submit();
		}
	};

	function check_form() {
		if ($('#addr').css("display") == "none") {
			alert('请编辑一个收货地址（电话），我们服务过程中如果遇到任何问题将会电话联络您。');
			return false;
		}
		
		if ($('#district-select option:selected').val() == 0) {
			alert('请选择一个就近的服务门店。该门店一旦和您的收货地址匹配，以后都由他们为您服务。');
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
