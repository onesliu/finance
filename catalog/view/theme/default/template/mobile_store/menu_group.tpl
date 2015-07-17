<?php echo $header; ?>
<body>
<div data-role="page" data-theme="a" id="menugrouppage" data-title="推荐菜谱">
	<?php echo $titlebar; ?>
	<div data-role="content">
		<ul data-role="listview" data-inset="true" data-theme="b" data-divider-theme="b" data-count-theme="b">
			<form>
				<input type="search" name="menu_group_search" id="menu_group_search" value="" placeholder="菜谱搜索..." />
			</form>
			<li data-role="list-divider">推荐菜谱</li>
			<?php foreach($menu_groups as $group) {?>
			<li><a href="<?php echo $group['url']; ?>" class="ui-btn" data-transition="slidefade">
			    <h2><?php echo $group['name']; ?></h2>
			    <p>
			    <img src="<?php echo $group['image']; ?>" height="180px" width="100%" /></p>
			  </a>
			</li>
			<?php } ?>
			<li data-icon="search"><a href="<?php echo $menu_search_url; ?>">更多菜谱选择</a></li>
		</ul>
	</div>
	<?php echo $navi; ?>
</div>
</body>
