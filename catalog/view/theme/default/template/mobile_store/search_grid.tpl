<?php echo $header; ?><?php echo $column_left; ?>
<div id="content" class="round"><?php echo $content_top; ?>
  
  <?php if ($products) { ?>
  <div class="product-filter">
    
    <div class="limit"><?php echo $text_limit; ?>
      <select onchange="location = this.value;">
        <?php foreach ($limits as $limits) { ?>
        <?php if ($limits['value'] == $limit) { ?>
        <option value="<?php echo $limits['href']; ?>" selected="selected"><?php echo $limits['text']; ?></option>
        <?php } else { ?>
        <option value="<?php echo $limits['href']; ?>"><?php echo $limits['text']; ?></option>
        <?php } ?>
        <?php } ?>
      </select>
    </div>
    <div class="sort"><?php echo $text_sort; ?>
      <select onchange="location = this.value;">
        <?php foreach ($sorts as $sorts) { ?>
        <?php if ($sorts['value'] == $sort . '-' . $order) { ?>
        <option value="<?php echo $sorts['href']; ?>" selected="selected"><?php echo $sorts['text']; ?></option>
        <?php } else { ?>
        <option value="<?php echo $sorts['href']; ?>"><?php echo $sorts['text']; ?></option>
        <?php } ?>
        <?php } ?>
      </select>
    </div>
  </div>
  <div class="product-compare"></div>
  <div class="product-grid">
    <?php foreach ($products as $product) { ?>
    <div class="no-borders">
      <?php if ($product['price']) { ?>
		  <div class="price">
			<?php if (!$product['special']) { ?>
			<?php echo $product['price']; ?>
			<?php } else { ?>
			<span class="price-old"><?php echo $product['price']; ?></span> <span class="price-new"><?php echo $product['special']; ?></span>
			<?php } ?>
		  </div>
		  <?php } ?>
		  <?php if ($product['thumb']) { ?>
		  <div class="image"><a href="<?php echo $product['href']; ?>" ><img src="<?php echo $product['thumb']; ?>" title="<?php echo $product['name']; ?>" alt="<?php echo $product['name']; ?>"/></a></div>
		  <?php } ?>
		  
		   <div class="name"><a href="<?php echo $product['href']; ?>" ><?php echo $product['name']; ?></a></div>
		  <div class="cart">
			<div onclick="addToCart('<?php echo $product['product_id']; ?>');" class="button-cart" ><span><?php echo $button_cart; ?></span></div>
		  </div>
    </div>
    <?php } ?>
  </div>
  <div class="pagination"><?php echo $pagination; ?></div>
  <?php } else { ?>
  <div class="content"><?php echo $text_empty; ?></div>
  <?php }?>
  <?php echo $content_bottom; ?></div> 

<?php if ( $filter_name != "") { ?> 
<script type="text/javascript">
	$('.product-list .name, .product-list .description').highlight('<?php echo $filter_name; ?>');
</script>
<?php } ?>
<?php echo $footer; ?>