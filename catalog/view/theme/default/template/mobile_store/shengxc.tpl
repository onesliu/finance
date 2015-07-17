<?php echo $header; ?>
<body>
<div data-role="page" id="shengxc" data-theme="a" class="my-page" data-title="<?php echo $heading_title; ?>">
	<?php echo $titlebar; ?>
	<div data-role="content">
		<div class="colum-list">
		  	<ul data-role="listview" data-inset="true" data-theme="c">
				<?php foreach($menu_food as $food) {?>
				<li><a href="<?php echo $food['url']; ?>" class="ui-btn">
				    <img src="<?php echo $food['image1']; ?>"/>
				    <h2><?php echo $food['name']; ?> <span style="color:red;"><?php echo $food['price']; ?></span></h2>
				    <p>点击可查看详情</p>
				  </a>
				</li>
				<?php } ?>
		  	</ul>
	  	</div>
	</div>
	
	<div data-role="popup" id="positionWindow" data-transition="slideup" data-position-to="window" class="ui-content" data-theme="a">
		<p id="buy_alert"></p>
	</div>
	<?php echo $navi; ?>
</div>

</body>
