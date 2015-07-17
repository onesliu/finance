<?php echo $header; ?>
<body>
<div data-role="page" data-theme="a" id="menupage">
	<?php echo $titlebar; ?>
	<div data-role="content">
	    <div class="ui-body ui-body-b ui-corner-all">
			<div id="menuswipe" class="img-box">
				<?php if (isset($menu['image1'])) {?>
				<img class="img-list slide out" src="<?php echo $menu['image1']; ?>" />
				<?php } if (isset($menu['image2'])) {?>
				<img class="img-list slide out" src="<?php echo $menu['image2']; ?>" />
				<?php } if (isset($menu['image3'])) {?>
				<img class="img-list slide out" src="<?php echo $menu['image3']; ?>" />
				<?php } ?>
				<div class="img-index"></div>
			</div>
			<h3 style="display:inline-block;margin-bottom:.4em;"><?php echo $menu['name']; ?></h3>
			<p><?php echo $menu['desp']; ?></p>
		</div>
		
		<?php $menu_food = $menu['menu_food']; require('food_list.tpl'); ?>
		<?php $sources = $menu['sources']; require('material_list.tpl'); ?>
		
		<script type="text/javascript"><!--
		$(document).on("pageinit", "#menupage", function(){
			setSwipeImg('#menuswipe', "swipe");
		});
		//--></script>
	</div>
	<?php echo $navi; ?>
</div>
</body>