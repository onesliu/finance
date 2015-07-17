
  <div class="autosuggest-close"></div>
  <?php if ($products) { ?>
    <div class="autosuggest-list">
    <?php foreach ($products as $product) { ?>
		<div>
		  <?php if ($product['thumb']) { ?>
		  <div class="image"><a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" title="<?php echo $product['name']; ?>" alt="<?php echo $product['name']; ?>" /></a></div>
		  <?php } ?>
		  <div class="name"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></div>
		  <div class="description"><?php echo $product['description']; ?></div>
		  
		  <div class="cart"><a onclick="addToCart(<?php echo $product['product_id']; ?>);" class="button"><span><?php echo $button_cart; ?></span></a></div>
		  <?php if ($product['price']) { ?>
		  <div class="price">
			<?php if (!$product['special']) { ?>
			<?php echo $product['price']; ?>
			<?php } else { ?>
			<span class="price-old"><?php echo $product['price']; ?></span> <span class="price-new"><?php echo $product['special']; ?></span>
			<?php } ?>
		  </div>
		  <?php } ?>
		</div>
    <?php } ?>
	</div>

  
  <?php } else { ?>
  <?php echo $text_empty; ?>
  <?php }?>

<?php if ( $filter_name != "") { ?> 
<script type="text/javascript">
	$('.autosuggest-list .name').highlight('<?php echo $filter_name; ?>');
</script>
<?php } ?>

<script type="text/javascript">
$(document).ready(function() {
	$('.autosuggest-close').bind('click', function(){
		$('#auto-suggestions').fadeOut();
	});
});
</script>