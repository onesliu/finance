<?php echo $header; ?>
<body>
<div data-role="page" data-theme="a" id="cart_page" data-title="<?php echo $heading_title; ?>">
	<?php echo $titlebar; ?>
	<div data-role="content">
	  	<ul data-role="listview" id="cartlist" data-inset="true" data-theme="c" data-divider-theme="c" data-count-theme="c">
	  		<li data-role="list-divider"><?php echo $heading_title; ?></li>
	  	<?php foreach ($products as $product) { ?>
	  		<li id="<?php echo "p".$product['key']; ?>">
	  			<img src="<?php echo $product['thumb']; ?>" onclick="show_product('<?php echo $product['href']; ?>');"/>
	  			<h2 style="margin-top:0px"><?php echo $product['name'].' '; ?>
	  				<span id="number<?php echo $product['key']; ?>"><?php echo $product['quantity']; ?></span><?php echo $product['sellunit']; ?>
	  				（<?php if ($product['product_type'] == 1) echo "估计"; ?><span id="weight<?php echo $product['key']; ?>"><?php echo $product['weight']; ?></span><?php echo $product['weight_class'].' '; ?>）
	  			</h2>
	  			<p style="display:inline;">
	  				<?php if ($product['product_type'] == 1) echo "估计";?><span style="color:red;">￥<span id="total<?php echo $product['key'];?>" ><?php echo $product['total']; ?></span></span>
	  				<span id="price<?php echo $product['key'];?>" style="display:none;"><?php echo $product['sellprice'];?></span>
	  				<span id="perweight<?php echo $product['key'];?>" style="display:none;"><?php echo $product['perweight']; ?></span>
	  				<span id="min<?php echo $product['key'];?>" style="display:none;"><?php echo $product['minimum']; ?></span>
	  				<span id="producttype<?php echo $product['key'];?>" style="display:none;"><?php echo $product['product_type']; ?></span>
	  			</p>
  				<div style="display:inline;float:right;margin:0 0 0 0;">
	    			<button class="ui-btn ui-btn-icon-notext ui-icon-minus ui-btn-inline ui-corner-all ui-nodisc-icon ui-alt-icon" style="margin:0 0 0 0;border:0px;"
	    			onclick="changecount('<?php echo $product['key']; ?>', -1, '<?php echo $action; ?>');"></button>
	  				<span id="<?php echo "c".$product['key']; ?>"><?php echo $product['quantity']; ?></span>
	  				<button class="ui-btn ui-btn-icon-notext ui-icon-plus ui-btn-inline ui-corner-all ui-nodisc-icon ui-alt-icon" style="margin:0 0 0 0;border:0px;"
	  				onclick="changecount('<?php echo $product['key']; ?>', 1, '<?php echo $action; ?>');"></button>
		  			<button class="ui-btn ui-btn-icon-notext ui-icon-delete ui-btn-inline ui-corner-all" style="margin:0 0 0 1em;"
		  			onClick="delete_product('<?php echo $product['key']; ?>',
	    			'<?php echo $product['remove']; ?>','<?php echo $product['name']; ?>');" data-rel="popup"></button>
  				</div>
	  		</li>
	  	<?php } ?>
	  		<li>
	  			<h2><?php echo $totals[0]['title']; ?><span id="span1"></span>： <span id="totals" style="color:red;"><?php echo $totals[0]['text']; ?></span></h2>
	  			<p id="span2"></p>
	  			<span id="totalprice" style="display:none;"><?php echo $totals[0]['value']; ?></span>
	  		</li>
	  	</ul>
	  	
	  	<div class="ui-body" style="text-align:center;">
	  		<input type="button" id="checkoutbtn" onclick="checkout();" class="ui-btn ui-corner-all ui-shadow ui-btn-d ui-btn-inline" value="下单" />
	  	</div>
	</div>
	<?php echo $navi; ?>
	
	<div data-role="popup" id="popupDialog" data-position-to="window" data-transition="pop" data-theme="b" data-overlay-theme="b" data-dismissible="false" style="max-width:400px;">
		<div data-role="header" data-theme="b">
			<h1>删除商品</h1>
		</div>
		<div role="main" class="ui-content">
			<h3 class="ui-title">你确定要从购物车删除<span id="deletename" style="color:red;">该商品</span>吗？</h3>
			<p>删除后将无法恢复。</p>
			<a href="#" id="deleteconfirm" class="ui-btn ui-corner-all ui-shadow ui-btn-a">确定</a>
			<a href="#" class="ui-btn ui-corner-all ui-shadow ui-btn-a" data-rel="back">取消</a>
		</div>
	</div>

	<script type="text/javascript"><!--
	var minum_price = <?php echo $minum_order; ?>;
	var dsbtn = "未达到起送价格 ￥<?php echo $minum_order; ?>";
	var enbtn = "立 即 下 单";
	var order_type = <?php echo $order_type; ?>;
	var checkout_url = "<?php echo $checkout; ?>";

	$(document).on("pageinit", "#cart_page", function(){
		var en = true;
		var totals = <?php echo $totals[0]['value']; ?>;
		if (totals >= minum_price) en = false;
		$('#checkoutbtn').button({ disabled: en });
		changebtn(totals);
	});

	function checkout() {
		location.href = checkout_url;
	}
	
	function btn_text() {
		var span1 = "";
		var span2 = "该价格是准确价格，可以直接支付";
		if (order_type == 1) {
			span1 = "估计";
			span2 = "该价格只是估算价格，称重后会收到准确价格";
		}
		$("#span1").text(span1);
		$("#span2").text(span2);
		$("#cartlist").listview( "refresh" );
	}

	function changebtn(totals) {
		btn_text();
		if (totals >= minum_price) {
			$("#checkoutbtn").val(enbtn);
			$("#checkoutbtn").button( "enable" );
		}
		else {
			$("#checkoutbtn").val(dsbtn);
			$("#checkoutbtn").button( "disable" );
		}
		$("#checkoutbtn").button( "refresh" );
	}

	function delete_product(key, url, name) {
		$('#deletename').html(name);
		$('#deleteconfirm').click(function(){
			$.get(url, function(data, status){
				location.reload();
			});
		});
		$('#popupDialog').popup('open');
	}

	function show_product(url) {
		$.mobile.changePage(url);
	}

	function changecount(key, num, url) {
		var count = $("#c"+key).text() - 0;
		num = num - 0;
		count += num;
		var min = $("#min"+key).text() - 0;
		if (count < min) {
			count = min;
			return;
		}
		
		$.post(url, {
				"update": "true",
				"key": key,
				"num": count
			}, function(data,status){
				var ret = eval("("+data+")");
				
				if (ret.status == 0) {
					$('#c'+key).text(count);
					$('#number'+key).text(count);

					var t = $('#total'+key).text() - 0;
					var pw = $('#perweight'+key).text() - 0;
					var p = $('#price'+key).text() - 0;
					if ($('#weight'+key).size() > 0) {
						$('#weight'+key).text(pw * count);
						$('#total'+key).text((p * count).toFixed(2));

						var offset = p*count - t;
						var totals = $('#totalprice').text() - 0 + offset;
						$('#totals').text('￥'+totals.toFixed(2));
						$('#totalprice').text(totals);

						changebtn(totals);
					}
				}
		});
	}

	//--></script>
</div>
</body>
