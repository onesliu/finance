<ul data-role="listview" data-inset="true" data-theme="b" data-divider-theme="b" data-count-theme="b">
	<li data-role="list-divider">当前菜谱包含以下菜品</li>
	<?php foreach($menu_food as $food) {?>
	<li><a href="<?php echo $food['url']; ?>" class="ui-btn" data-transition="slidefade">
	    <img src="<?php echo $food['image1']; ?>"/>
	    <h2><?php echo $food['name']; ?></h2>
	    <p><?php echo $food['desp']; ?></p>
	  </a>
	</li>
	<?php } ?>
</ul>
