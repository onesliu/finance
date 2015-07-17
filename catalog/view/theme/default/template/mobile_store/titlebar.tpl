<div data-role="panel" id="option_panel" data-position="right" data-display="overlay" data-theme="b">
	<ul data-role="listview" data-icon="false" data-theme="b" data-divider-theme="a" data-count-theme="b">
		<li><a href="#" data-rel="close">关闭</a></li>
		<li data-role="list-divider">个人中心</li>
		<?php foreach($options as $option) { ?>
		<li><a href="<?php echo $option['url']; ?>" data-transition="slidefade" data-direction="reverse"><?php echo $option['name']; ?></a></li>
		<?php } ?>
		<li data-role="list-divider"></li>
		<li><a href="<?php echo $about; ?>">关于我们</a></li>
		<li><a href="http://m.wsq.qq.com/264065938/t/4">我们的服务</a></li>
	</ul>
</div>
<div data-role="header" data-position="fixed" data-theme="a" data-tap-toggle="false">
<?php if (isset($product_page) && $product_page == true) {?>
    <style type="text/css">
		.ui-input-search {margin:0px 0 0px 0; height:26px;}
	</style>
    	<a href="#category_panel" class="ui-btn ui-icon-grid ui-corner-all ui-btn-icon-left">分类</a>
	   	<h1 style="padding:6px 0 6px 1.5em;margin:0 20%;"><input style="height:25px;min-height:0;font-size:.8em;" id="product_search" onchange="searchproduct();" type="search" name="product_search" value="" placeholder="查找商品..." /></h1>
<?php } else { ?>
		<?php if (isset($back)) {?>
		<a href="#" data-rel="back" class="ui-btn ui-icon-arrow-l ui-btn-icon-notext ui-corner-all"></a>
		<?php } elseif (isset($home_url)) {?>
		<a href="<?php echo $home_url; ?>" class="ui-btn ui-icon-home ui-btn-icon-notext ui-corner-all"></a>
		<?php } ?>
	    <h1 style="padding:.4em 0;"><img src="<?php echo $logo; ?>" style="height:25px;" /></h1>
<?php } ?>
    <a href="#option_panel" class="ui-btn ui-icon-user ui-btn-icon-notext ui-corner-all"></a>
</div>
