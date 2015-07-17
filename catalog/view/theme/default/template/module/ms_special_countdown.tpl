<?php if ($products) { ?>
<div class="android-box">
  <div class="row-title no-mb <?php echo $show_class; ?>"><?php echo $heading_title; ?></div>
  <div class="row-content no-bt <?php echo $show_class; ?>">
    <div class="product-list">
      <?php foreach ($products as $product) { ?>
        <div class="borders-inside">
		  <?php if ($product['thumb']) { ?>
		  <div class="image"><a href="<?php echo $product['href']; ?>" ><img src="<?php echo $product['thumb']; ?>" title="<?php echo $product['name']; ?>" alt="<?php echo $product['name']; ?>" /></a></div>
		  <?php } ?>
		  <?php if ($product['show_special_countdown']){ ?>
		  <div class="android-counter">	  
			  <div class="as-counter" id="counter_<?php echo $module;?>_<?php echo $product['product_id']; ?>">
				<?php if ($product['days'] > 0) { ?>
					<span class="digit_time"><?php echo $product['days']; ?></span> <span class="time_unit"><?php echo $heading_days; ?></span>  
				<?php } ?>	
				<span class="digit_time"><?php echo $product['hours']; ?></span> <span class="time_unit"><?php echo $heading_hours; ?></span>
			  </div>	
		  </div>	  
		  <?php } ?>
		  <div class="name"><a href="<?php echo $product['href']; ?>" ><?php echo $product['name']; ?></a></div>
		  <div class="description"><?php echo $product['description']; ?></div>
		  
		  <div class="cart"><a onclick="addToCart('<?php echo $product['product_id']; ?>');" class="button" ><span><?php echo $button_cart; ?></span></a></div>
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
  </div>
</div>
<?php } ?>