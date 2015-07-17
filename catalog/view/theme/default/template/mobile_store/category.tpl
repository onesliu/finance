<?php echo $header; ?>
<body>
<div data-role="page" id="categorypage" data-theme="a" class="my-page" data-title="<?php echo $heading_title; ?>">
	<?php echo $titlebar; ?>
	<div data-role="content">
		<div class="colum-list">
	  	<ul data-role="listview" id="plist" data-inset="true" data-split-icon="cart" data-theme="c" data-divider-theme="c" data-count-theme="c">
	  		<li data-role="list-divider"><?php echo $heading_title; ?></li> 
			<?php require('category_more.tpl'); ?>
	  	</ul>
	  	</div>
	</div>
	
	<div id="bmore" style="text-align: center; display: none;">正在加载......</div>
	<div data-role="popup" id="positionWindow1" data-transition="slideup" data-position-to="window" class="ui-content" data-theme="a">
		<p id="buy_alert1"></p>
	</div>
	
	<?php echo $navi; ?>
	
	<script type="text/javascript"><!--
	function addToCart(product_id, quantity) {
		quantity = typeof(quantity) != 'undefined' ? quantity : 1;

		$.ajax({
			url: 'index.php?route=mobile_store/cart/add',
			type: 'post',
			data: 'product_id=' + product_id + '&quantity=' + quantity,
			dataType: 'json',
			success: function(json) {
				if (json.success) {
					$('#buy_alert1').html(json.success);
					
					$('#positionWindow1').popup( 'reposition', 'positionTo: window' );
					$('#positionWindow1').popup('open', { positionTo: "window" });
					setTimeout(function() {
						$("#positionWindow1").popup('close');
					}, 1000);
				}
			}
		});
	}
	
	var url_more="<?php echo $pagination->url; ?>";
	var um_page=<?php echo $pagination->page; ?>;
	var um_pages=<?php echo $pagination->num_pages; ?>;
	$(document).on("pageinit","#categorypage",function(){
		$(document).off('swiperight swipeleft');
		<?php if ($pagination->page < $pagination->num_pages) { ?>
			auto_scroll2(domore);
		<?php } ?>
	});

	var starting = false;
	function domore(){
		if (starting == true) return;
		
		//$.mobile.loading('show');
		$('#bmore').show();
		starting = true;
		
		um_page++;
		url = "<?php echo $pagination->url; ?>" + "&page=" + um_page;
		$.get(url, function(data,status) {
			$("#plist").append(data);
			$("#plist").listview('refresh');
			$("#plist").find("li:last").slideDown(300);
			
			if (um_page >= um_pages) {
				auto_scroll2(null);
			}
			
			starting = false;
			//$.mobile.loading('hide');
			$('#bmore').hide();
		});
	}

	function searchproduct() {
        var url = "<?php echo $searchurl; ?>";
        var filter = $('#product_search').val();
        url += '&filter=' + filter;
        $( ":mobile-pagecontainer" ).pagecontainer( "change", url, {transition:"fade",dataUrl:"search"});
	}
	//--></script> 
</div>

</body>
