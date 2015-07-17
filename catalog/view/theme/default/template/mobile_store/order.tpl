<?php echo $header; ?>
<body>
<div data-role="page" id="orderpage" data-theme="a" data-title="<?php echo $heading_title; ?>">
	<?php echo $titlebar; ?>
	<div data-role="content">
	<?php if (!isset($orders) || count($orders) == 0) { ?>
		<div class="ui-body ui-body-b ui-corner-all">
			<p>您还没有订单</p>
		</div>
	<?php } else { ?>
		<ul data-role="listview" id="olist" data-inset="true" data-theme="c" data-divider-theme="c" data-count-theme="c">
			<li data-role="list-divider">订单列表</li>
			<?php require('order_more.tpl'); ?>
		</ul>
		
		<div id="omore" style="text-align: center; display: none;">正在加载......</div>
	<?php } ?>
	</div>

	<script type="text/javascript"><!--
	var url_more="<?php echo $pagination->url; ?>";
	var um_page=<?php echo $pagination->page; ?>;
	var um_pages=<?php echo $pagination->num_pages; ?>;
	$(document).on("pageinit","#orderpage",function(){
		<?php if ($pagination->page < $pagination->num_pages) { ?>
			auto_scroll2(domore);
		<?php } ?>
	});

	var starting = false;
	function domore(){
		if (starting == true) return;
		
		$('omore').show();
		starting = true;

		um_page++;
		url = "<?php echo $pagination->url; ?>" + "&page=" + um_page;
		$.get(url, function(data,status) {
			$("#olist").append(data);
			$("#olist").listview('refresh');
			$("#olist").find("li:last").slideDown(300);
			
			if (um_page >= um_pages) {
				auto_scroll2(null);
			}
			
			starting = false;
			$('omore').hide();
		});
	};

	//--></script> 
	<?php echo $navi; ?>
</div>
</body>