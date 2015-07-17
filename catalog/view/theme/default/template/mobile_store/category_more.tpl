  <?php foreach ($products as $product) { ?>
  	<li>
  		<a href="#" <?php if ($product['product_type'] == 2) echo 'class="ui-btn"'; ?> onclick="chgpage('<?php echo $product['href']; ?>')">
	  		<img src="<?php echo $product['thumb']; ?>" />
	  		<h2><?php echo $product['name']; ?> <span style="color:red;">
	  			<?php if ($product['product_type'] != 2) echo $product['price']; else echo $product['sellprice'];?>
	  			</span>/<?php if ($product['product_type'] != 2) echo $product['unit']; else echo $product['sellunit']; ?></h2>
	  		<p><?php echo $product['model']; ?></p>
		</a>
		<?php if ($product['product_type'] < 2) {?>
    	<a href="#1" onclick="addToCart(<?php echo $product['product_id'];?>);" class="ui-alt-icon"><?php echo $button_cart; ?></a>
    	<?php } ?>
  	</li>
  <?php } ?>