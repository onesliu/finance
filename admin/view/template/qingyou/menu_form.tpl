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
      <h1><img src="view/image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a><a href="<?php echo $cancel; ?>" class="button"><?php echo $button_cancel; ?></a></div>
    </div>
    <div class="content">
      <div id="tabs" class="htabs"><a href="#tab-general"><?php echo $tab_general; ?></a></div>
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
            <table class="form">
	            <tr>
	                <td><span class="required">*</span> <?php echo $entry_name; ?></td>
	                <td><input type="text" name="name" size="100" value="<?php echo $name; ?>" />
	                  <?php if (isset($error_name['name'])) { ?>
	                  <span class="error"><?php echo $error_name['name']; ?></span>
	                  <?php } ?></td>
	            </tr>
	            <tr>
	                <td><?php echo $entry_desp; ?></td>
	                <td><textarea name="desp" cols="40" rows="5"><?php echo $desp; ?></textarea></td>
	            </tr>
	            <tr>
	              <td><?php echo $entry_sort_order; ?></td>
	              <td><input type="text" name="sort" value="<?php echo $sort; ?>" size="1" /></td>
	            </tr>
	            <tr>
	              <td><?php echo $entry_disable; ?></td>
	              <td><select name="disable">
	                  <?php if ($disable) { ?>
	                  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
	                  <option value="0"><?php echo $text_disabled; ?></option>
	                  <?php } else { ?>
	                  <option value="1"><?php echo $text_enabled; ?></option>
	                  <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
	                  <?php } ?>
	                </select></td>
	            </tr>
	            <tr>
	              <td><?php echo $entry_group; ?></td>
	              <td><div class="scrollbox">
	              	  <?php $class = 'even'; ?>
	                  <?php foreach ($allgroups as $group) { ?>
	                  <?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
	                  <div class="<?php echo $class; ?>">
	                    <?php if (in_array($group['id'], $groups)) { ?>
	                    <input type="checkbox" name="groups[]" value="<?php echo $group['id']; ?>" checked="checked" />
	                    <?php echo $group['name']; ?>
	                    <?php } else { ?>
	                    <input type="checkbox" name="groups[]" value="<?php echo $group['id']; ?>" />
	                    <?php echo $group['name']; ?>
	                    <?php } ?>
	                  </div>
	                  <?php } ?>
	                </div></td>
	            </tr>
	            <tr>
	              <td><?php echo $entry_food; ?></td>
	              <td><div class="scrollbox">
	              	  <?php $class = 'even'; ?>
	                  <?php foreach ($allfood as $food) { ?>
	                  <?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
	                  <div class="<?php echo $class; ?>">
	                    <?php if (in_array($food['id'], $menu_food)) { ?>
	                    <input type="checkbox" name="menu_food[]" value="<?php echo $food['id']; ?>" checked="checked" />
	                    <?php echo $food['name']; ?>
	                    <?php } else { ?>
	                    <input type="checkbox" name="menu_food[]" value="<?php echo $food['id']; ?>" />
	                    <?php echo $food['name']; ?>
	                    <?php } ?>
	                  </div>
	                  <?php } ?>
	                </div></td>
	            </tr>
	            <tr>
	              <td><?php echo $entry_image; ?></td>
	              <td valign="top">
	              	<?php for($i = 1; $i <= 3; $i++) { ?>
	              	<div class="image">
	              		<img src="<?php echo ${"thumb$i"}; ?>" alt="" id="thumb<?php echo $i; ?>" />
	                  	<input type="hidden" name="image<?php echo $i; ?>" value="<?php echo ${"image$i"}; ?>" id="image<?php echo $i; ?>" />
	                  	<br />
	                  	<a onclick="image_upload('image<?php echo $i; ?>', 'thumb<?php echo $i; ?>');"><?php echo $text_browse; ?></a>
	                  	&nbsp;&nbsp;|&nbsp;&nbsp;
	                  	<a onclick="$('#thumb<?php echo $i; ?>').attr('src', '<?php echo $no_image; ?>'); $('#image<?php echo $i; ?>').attr('value', '');"><?php echo $text_clear; ?></a>
	                </div>
	                <?php } ?>
	              </td>
	            </tr>
          </table>
      </form>
    </div>
  </div>
</div>
<script type="text/javascript" src="view/javascript/ckeditor/ckeditor.js"></script> 
<script type="text/javascript"><!--
function image_upload(field, thumb) {
	$('#dialog').remove();
	
	$('#content').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;"><iframe src="index.php?route=common/filemanager&token=<?php echo $token; ?>&field=' + encodeURIComponent(field) + '" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto"></iframe></div>');
	
	$('#dialog').dialog({
		title: '<?php echo $text_image_manager; ?>',
		close: function (event, ui) {
			if ($('#' + field).attr('value')) {
				$.ajax({
					url: 'index.php?route=common/filemanager/image&token=<?php echo $token; ?>&image=' + encodeURIComponent($('#' + field).val()),
					dataType: 'text',
					success: function(data) {
						$('#' + thumb).replaceWith('<img src="' + data + '" alt="" id="' + thumb + '" />');
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
<script type="text/javascript"><!--
$('#tabs a').tabs(); 
//--></script> 
<?php echo $footer; ?>