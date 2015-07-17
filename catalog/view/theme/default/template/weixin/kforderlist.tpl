<body>
<div data-role="page" id="kforderlist" data-theme="a">
	<div data-role="header" data-position="fixed" data-theme="a">
	    <h1 style="padding:.4em 0;"><img src="<?php echo $logo; ?>" style="height:25px;" /></h1>
	</div>
	<div data-role="content">
		<?php if (!isset($orders) || count($orders) == 0) { ?>
			<div class="ui-body ui-body-b ui-corner-all">
				<p>没有订单</p>
			</div>
		<?php } elseif ($pagination->page == 1) { ?>
			<ul data-role="listview" id="olist" data-inset="true" data-theme="c" data-divider-theme="c" data-count-theme="c">
				<li data-role="list-divider">订单列表</li>
				<?php require('kfordermore.tpl'); ?>
			</ul>
			<?php if ($pagination->page < $pagination->num_pages) { ?>
				<a id="bmore" href="#" onclick="OnMoreOrder();" class="ui-btn ui-btn-a ui-corner-all">更多...</a>
			<?php } ?>
		<?php } ?>
	</div>

	<script type="text/javascript"><!--
	var url_more="<?php echo $pagination->url; ?>";
	var um_page=<?php echo $pagination->page; ?>;
	var um_pages=<?php echo $pagination->num_pages; ?>;
	function OnMoreOrder() {
		um_page++;
		url = "<?php echo $pagination->url; ?>" + "&page=" + um_page;
		$.get(url, function(data,status) {
			$("#olist").append(data);
			$("#olist").listview('refresh');
			$("#olist").find("li:last").slideDown(300);
			if (um_page >= um_pages) {
				$("#bmore").hide();
			}
		});
	}
	//--></script>
</div>
</body>


