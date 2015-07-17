<div data-role="footer" data-position="fixed" data-theme="a" data-tap-toggle="false">
	<div data-role="navbar" data-iconpos="top" style="margin-top:0em;margin-bottom:0em;" >
      <ul>
        <li><a href="<?php echo $home;?>" data-icon="home" class="navi-btn"><?php echo $text_home; ?></a></li>
       	<!-- <li><a href="#category_panel" data-icon="grid" class="navi-btn"><?php echo $text_categories; ?></a>
        <li><a href="<?php echo $product;?>" data-icon="grid" class="navi-btn"><?php echo $text_products; ?></a> -->
        <li><a href="<?php echo $clean;?>" data-icon="grid" class="navi-btn"><?php echo $text_clean; ?></a>
        <li><a href="<?php echo $menu;?>" data-icon="bars" class="navi-btn" data-transition="slidefade"><?php echo $text_menu; ?></a></li>
        <li><a href="<?php echo $cart;?>" data-icon="cart" class="navi-btn" data-transition="slidefade"><?php echo $text_checkout; ?></a></li>
      </ul>
    </div>
</div>
<div data-role="panel" id="category_panel" data-position="left" data-display="overlay" data-theme="b">
	<?php echo $category_list; ?>
</div>

