<?php if ($categories) { ?>
<ul data-role="listview" data-icon="false" data-theme="b" data-divider-theme="a" data-count-theme="b">
	<li><a href="#" data-rel="close">关闭</a></li>
	<?php foreach($categories as $category) { if ($category['childs']) { $subcategories = $category['childs']; ?>
	<li data-role="list-divider"><?php echo $category['name']; ?></li>
	<?php foreach ($subcategories as $subcategory) { ?>
	<li><a href="<?php echo $subcategory['href']; ?>" data-transition="slidefade"><?php echo $subcategory['name']; ?>
	<span class="ui-li-count"><?php echo $subcategory['count']; ?></span></a></li>
	<?php } ?>
	<?php }} ?>
</ul>
<?php } ?>