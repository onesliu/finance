<?php echo $header; ?>
<div id="content">
<div class="breadcrumb">
  <?php foreach ($breadcrumbs as $breadcrumb) { ?>
  <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
  <?php } ?>
</div>
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<div class="box">
  <div class="heading">
    <h1><img src="view/image/module.png" alt="" /> <?php echo $heading_title; ?></h1>
    <div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $button_cancel; ?></a></div>
  </div>
  <div class="content">
    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
      <table class="form">
		  <tr>
            <td class="left"><?php echo $entry_logo; ?></td>
            <td class="left"><div class="image"><img src="<?php echo $thumb; ?>" alt="" id="thumb" /><br />
                  <input type="hidden" name="mobile_store_logo" value="<?php echo $mobile_store_logo; ?>" id="image" />
                  <a onclick="image_upload('image', 'thumb');"><?php echo $text_browse; ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="$('#thumb').attr('src', '<?php echo $no_image; ?>'); $('#image').attr('value', '');"><?php echo $text_clear; ?></a></div></td></td>
          </tr>
		  <tr>
            <td class="left"><?php echo $entry_choose_products; ?></td>
            <td class="left">
				<div class="choose-products">
					<div class="header"><?php echo $entry_available_products;?></div>
					<div class="content">
						<?php if ($mobile_store_available_products){ ?>
						<ul id="available_products" class="connectedSortable">
						<?php foreach($mobile_store_available_products as $product){ ?>
							<li id="<?php echo $product['product_id']; ?>">
								<div class="product-image"><img src="<?php echo $product['thumb']; ?>"></div> 
								<div class="product-description"><?php echo $product['name']; ?> - <?php echo $product['model']; ?></div>
							</li>
						<?php } ?>
						</ul>	
						<?php } ?>
					</div>	
				</div>
				<div class="choose-products">
					<div class="header"><?php echo $entry_selected_products;?></div>
					<div class="content">
					    <ul id="selected_products" class="connectedSortable">
						<?php if ($mobile_store_selected_products){ ?>
						<?php foreach($mobile_store_selected_products as $product){ ?>
							<li id="<?php echo $product['product_id']; ?>">
								<div class="product-image"><img src="<?php echo $product['thumb']; ?>"></div> 
								<div class="product-description"><?php echo $product['name']; ?> - <?php echo $product['model']; ?></div>
							</li>
						<?php } ?>
						</ul>	
						<?php } ?>
					</div>
				</div>
				
				<input type="hidden" name="mobile_store_selected_products" id="mobile_store_selected_products" value="<?php echo $mobile_store_selected_products_ids; ?>" />
			
            </td>
          </tr>

		  <tr>
            <td class="left"><span class="required">* </span><?php echo $entry_image; ?></td>
            <td class="left">
				<input name="mobile_store_image_width" value="<?php echo $mobile_store_image_width; ?>" size="5" />
				<input name="mobile_store_image_height" value="<?php echo $mobile_store_image_height; ?>" size="5" />
			<?php if ($error_image) { ?>
                <span class="error"><?php echo $error_image; ?></span>
            <?php } ?>
            </td>
          </tr>

		  <tr>
            <td class="left"><?php echo $entry_charset; ?></td>
            <td class="left">
				<select name="mobile_store_charset">
				<?php if ($mobile_store_charset) { ?>
					<option value="1" selected="selected"><?php echo $text_yes; ?></option>
					<option value="0"><?php echo $text_no; ?></option>
				<?php } else { ?>
					<option value="1"><?php echo $text_yes; ?></option>
					<option value="0" selected="selected"><?php echo $text_no; ?></option>
				<?php } ?>
				</select>
            </td>
          </tr> 
      </table>
    </form>
  </div>
</div>
<script type="text/javascript"><!--
$(function() {
	$( "#available_products, #selected_products" ).sortable({
		connectWith: ".connectedSortable",
		receive: function(event, ui) { 
			var sel_products = new Array();
	
			$('#selected_products li').each( function(index){
				sel_products.push( $(this).attr('id') );
			});
			
			$('#mobile_store_selected_products').val(sel_products.join(','));
			
		}
	}).disableSelection();
});

function image_upload(field, thumb) {
	$('#dialog').remove();
	
	$('#content').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;"><iframe src="index.php?route=common/filemanager&token=<?php echo $token; ?>&field=' + encodeURIComponent(field) + '" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto"></iframe></div>');
	
	$('#dialog').dialog({
		title: '<?php echo $text_image_manager; ?>',
		close: function (event, ui) {
			if ($('#' + field).attr('value')) {
				$.ajax({
					url: 'index.php?route=common/filemanager/image&token=<?php echo $token; ?>&image=' + encodeURIComponent($('#' + field).attr('value')),
					dataType: 'text',
					success: function(text) {
						$('#' + thumb).replaceWith('<img src="' + text + '" alt="" id="' + thumb + '" />');
					}
				});
			}
		},	
		bgiframe: false,
		width: 800,
		height: 400,
		resizable: false,
		modal: false
	});
};
//--></script>
<?php echo $footer; ?>