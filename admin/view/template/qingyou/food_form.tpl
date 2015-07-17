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
      <div id="tabs" class="htabs">
      		<a href="#tab-general"><?php echo $tab_general; ?></a>
      		<a href="#tab-source"><?php echo $tab_source; ?></a>
      		<a href="#tab-make"><?php echo $tab_make; ?></a>
      </div>
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
      	<div id="tab-general">
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
	                <td><textarea name="desp" cols="100" rows="5"><?php echo $desp; ?></textarea></td>
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
	              <td><?php echo $entry_attr; ?></td>
	              <td><?php foreach($attrs as $attr_class) { ?>
	              	<div class="scrollbox">
	                  <?php foreach($attr_class as $attr) { ?>
	                  <div class="even">
	                    <input type="checkbox" name="attrs[]" value="<?php echo $attr['id']; ?>" <?php if (in_array($attr['id'], $food_attrs)) echo "checked=\"checked\""; ?> />
	                    <?php echo $attr['name']; ?>
	                  </div>
	                  <?php } ?>
	                </div>
	                <?php } ?>
	              </td>
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
        </div>
        <div id="tab-source">
	        <table id="food_source" class="list">
            <thead>
              <tr>
                <td class="left"><?php echo $entry_source_name; ?></td>
                <td class="left"><?php echo $entry_source_type; ?></td>
                <td class="left"><?php echo '推荐原料'; ?></td>
                <td class="left"><?php echo $entry_sort_order; ?></td>
                <td></td>
              </tr>
            </thead>
            <?php $source_row = 0; ?>
            <?php foreach ($food_sources as $source) { ?>
            <tbody id="source-row<?php echo $source_row; ?>">
              <tr>
                <td class="left"><select name="source[<?php echo $source_row; ?>][product_id]">
                	<?php foreach($allsources as $s) { ?>
                	<option value="<?php echo $s['product_id']; ?>" <?php if ($s['product_id'] == $source['product_id']) echo "selected=\"selected\""; ?>><?php echo $s['name']; ?></option>
                	<?php } ?>
                </select>
                <td class="left"><select name="source[<?php echo $source_row; ?>][type]">
                	<option value="0" <?php if ($source['source_type']==0) echo "selected=\"selected\""; ?>><?php echo $text_main_source; ?></option>
                	<option value="1" <?php if ($source['source_type']==1) echo "selected=\"selected\""; ?>><?php echo $text_other_source; ?></option>
                </select></td>
                <td class="left"><input type="text" name="source[<?php echo $source_row; ?>][groupid]" value="<?php echo $source['groupid']; ?>" size="1" /></td>
                <td class="left"><input type="text" name="source[<?php echo $source_row; ?>][sort]" value="<?php echo $source['sort']; ?>" size="1" /></td>
                <td class="left"><a onclick="$('#source-row<?php echo $source_row; ?>').remove();" class="button"><?php echo $button_remove; ?></a></td>
              </tr>
            </tbody>
            <?php $source_row++; ?>
            <?php } ?>
            <tfoot>
              <tr>
                <td colspan="3"></td>
                <td class="left"><a onclick="addSource();" class="button"><?php echo $button_insert; ?></a></td>
              </tr>
            </tfoot>
          </table>
        </div>
        <div id="tab-make">
        	<table class="form">
	            <tr>
	              <td><?php echo $entry_make_video; ?></td>
	              <td><input type="text" name="make_video" value="<?php echo $make_video; ?>" size="100" /></td>
	            </tr>
	            <tr>
	              <td><?php echo $entry_make_url; ?></td>
	              <td><input type="text" name="make_url" value="<?php echo $make_url; ?>" size="100" /></td>
	            </tr>
	        </table>
	        
	        <table id="makestep" class="list">
            <thead>
              <tr>
                <td class="left"><?php echo $entry_step_image; ?></td>
                <td class="left"><?php echo $entry_step_desp; ?></td>
                <td class="left"><?php echo $entry_sort_order; ?></td>
                <td></td>
              </tr>
            </thead>
            <?php $row = 0; ?>
            <?php foreach ($make_steps as $step) { ?>
            <tbody id="make_steps<?php echo $row; ?>">
              <tr>
                <td class="left"><div class="image"><img src="<?php echo $step['thumb']; ?>" alt="" id="stepthumb<?php echo $row; ?>" />
                    <input type="hidden" name="step[<?php echo $row; ?>][image]" value="<?php echo $step['image']; ?>" id="stepimage<?php echo $row; ?>" />
                    <br />
                    <a onclick="image_upload('stepimage<?php echo $row; ?>', 'stepthumb<?php echo $row; ?>');"><?php echo $text_browse; ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="$('#stepthumb<?php echo $row; ?>').attr('src', '<?php echo $no_image; ?>'); $('#stepimage<?php echo $row; ?>').attr('value', '');"><?php echo $text_clear; ?></a></div></td>
                <td class="left"><textarea name="step[<?php echo $row; ?>][desp]" cols="100" rows="5"><?php echo $step['desp']; ?></textarea></td>
                <td class="left"><input type="text" name="step[<?php echo $row; ?>][step]" value="<?php echo $step['step']; ?>" size="2" /></td>
                <td class="left"><a onclick="$('#make_steps<?php echo $row; ?>').remove();" class="button"><?php echo $button_remove; ?></a></td>
              </tr>
            </tbody>
            <?php $row++; ?>
            <?php } ?>
            <tfoot>
              <tr>
                <td colspan="3"></td>
                <td class="left"><a onclick="addStep();" class="button"><?php echo $button_insert; ?></a></td>
              </tr>
            </tfoot>
          </table>
        </div>
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
var source_row = <?php echo $source_row; ?>;

function addSource() {
	html  = '<tbody id="source-row' + source_row + '">';
	html += '<tr>';
	html += '  <td class="left"><select name="source[' + source_row + '][product_id]">';
    <?php foreach($allsources as $s) { ?>
    html += '	<option value="<?php echo $s['product_id']; ?>"><?php echo $s['name']; ?></option>';
    <?php } ?>
    html += '	</select></td>';
	html += '  <td class="left"><select name="source[' + source_row + '][type]">';
	html += '  	<option value="0" selected="selected"><?php echo $text_main_source; ?></option>';
	html += '  	<option value="1"><?php echo $text_other_source; ?></option>';
	html += '  </select></td>';
	html += '  <td class="left"><input type="text" name="source[' + source_row + '][groupid]" value="0" size="1" /></td>';
	html += '  <td class="left"><input type="text" name="source[' + source_row + '][sort]" value="0" size="1" /></td>';
	html += '  <td class="left"><a onclick="$(\'#source-row' + source_row + '\').remove();" class="button"><?php echo $button_remove; ?></a></td>';
	html += '</tr>';
	html += '</tbody>';

	$('#food_source tfoot').before(html);
	
	source_row++;
}

var step_row = <?php echo $row; ?>;

function addStep() {
	html  = '<tbody id="make_steps' + step_row + '">';
	html += '<tr>';
	html += '<td class="left"><div class="image"><img src="<?php echo $no_image; ?>" alt="" id="stepthumb' + step_row + '" />';
	html += '<input type="hidden" name="step[' + step_row + '][image]" value="" id="stepimage' + step_row + '" />';
	html += '<br />';
	html += '<a onclick="image_upload(\'stepimage' + step_row + '\', \'stepthumb' + step_row + '\');"><?php echo $text_browse; ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="$(\'#stepthumb' + step_row + '\').attr(\'src\', \'<?php echo $no_image; ?>\'); $(\'#stepimage' + step_row + '\').attr(\'value\', \'\');"><?php echo $text_clear; ?></a></div></td>';
	html += '<td class="left"><textarea name="step[' + step_row + '][desp]" cols="100" rows="5"></textarea></td>';
	html += '<td class="left"><input type="text" name="step[' + step_row + '][step]" value="0" size="2" /></td>';
	html += '<td class="left"><a onclick="$(\'#make_steps' + step_row + '\').remove();" class="button"><?php echo $button_remove; ?></a></td>';
	html += '</tr>';
	html += '</tbody>';

	$('#makestep tfoot').before(html);
	
	step_row++;
}
--></script>
<script type="text/javascript"><!--
$('#tabs a').tabs(); 
//--></script> 
<?php echo $footer; ?>