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
  <?php if ($success) { ?>
  <div class="success"><?php echo $success; ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/setting.png" alt="" /> <?php echo $heading_title; ?></h1>
	  <div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a><a href="<?php echo $cancel; ?>" class="button"><?php echo $button_cancel; ?></a></div>
    </div>
    <div class="content">
      <div id="tabs" class="htabs">
      		<a href="#tab-general">基础设置</a>
      		<a href="#tab-actions">首页活动设置</a>
      </div>
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <div id="tab-general">
          <table class="form">
            <tr>
              <td><span class="required">*</span> <?php echo $label_minum_order; ?></td>
              <td><input type="text" name="minum_order" value="<?php echo $minum_order; ?>" size="10" />
                <?php if ($error_minum_order) { ?>
                <span class="error"><?php echo $error_minum_order; ?></span>
                <?php } ?></td>
            </tr>
            <tr>
              <td><span class="required">*</span> <?php echo $label_help_interval; ?></td>
              <td><input type="text" name="help_interval" value="<?php echo $help_interval; ?>" size="10" />
                <?php if ($error_help_interval) { ?>
                <span class="error"><?php echo $error_help_interval; ?></span>
                <?php } ?></td>
            </tr>
            <tr>
              <td><span class="required">*</span> <?php echo $label_shipping_interval; ?></td>
              <td><input type="text" name="shipping_interval" value="<?php echo $shipping_interval; ?>" size="10" />
                <?php if ($error_shipping_interval) { ?>
                <span class="error"><?php echo $error_shipping_interval; ?></span>
                <?php } ?></td>
            </tr>
            <tr>
              <td><span class="required">*</span> <?php echo $label_store_closed; ?></td>
              <td><input type="text" name="store_closed" value="<?php echo $store_closed; ?>" size="10" />
            </tr>
          </table>
        </div>
        <div id="tab-actions">
          <table id="action_list" class="list">
            <thead>
              <tr>
                <td class="left">图像</td>
                <td class="left">链接</td>
                <td></td>
              </tr>
            </thead>
            <?php $action_row = 0; ?>
            <?php foreach ($actions as $act) { ?>
            <tbody id="action-row<?php echo $action_row; ?>">
              <tr>
                <td class="left">
                	<div class="image"><img src="<?php echo $act->thumb; ?>" alt="" id="thumb<?php echo $action_row; ?>" />
	                  <input type="hidden" name="action[<?php echo $action_row; ?>][image]" value="<?php echo $act->image; ?>" id="image<?php echo $action_row; ?>" />
	                  <br />
	                  <a onclick="image_upload('image<?php echo $action_row; ?>', 'thumb<?php echo $action_row; ?>');">浏览</a>&nbsp;&nbsp;|&nbsp;&nbsp;
	                  <a onclick="$('#thumb<?php echo $action_row; ?>').attr('src', '<?php echo $no_image; ?>'); $('#image<?php echo $action_row; ?>').attr('value', '');">清除</a>
	                </div>
	            </td>
                <td class="left"><input type="text" name="action[<?php echo $action_row; ?>][url]" value="<?php echo $act->url; ?>" /></td>
                <td><a onclick="$('#action-row<?php echo $action_row; ?>').remove();" class="button">删除</a></td>
              </tr>
            </tbody>
            <?php $action_row++; ?>
            <?php } ?>
            <tfoot>
              <tr>
                <td colspan="1"></td>
                <td class="left"><a onclick="addSource();" class="button">新增</a></td>
              </tr>
            </tfoot>
          </table>
        </div>
     </form>
   </div>
  </div>
</div>
<script type="text/javascript"><!--
var action_row = <?php echo $action_row; ?>;

function addSource() {

	var thumb_id = "thumb" + action_row;
	var image_id = "image" + action_row;
	
	html = '<tbody id="action-row<?php echo $action_row; ?>">';
	html += '<tr>';
	html += '    <td class="left">';
	html += '	   	<div class="image"><img src="<?php echo $no_image; ?>" alt="" id="' +thumb_id+ '" />';
	html += '	        <input type="hidden" name="action['+ action_row+ '][image]" value="<?php echo $no_image; ?>" id="'+image_id+'" />';
	html += '	        <br />';
	html += '	        <a onclick="image_upload(\'' +image_id+ '\', \'' +thumb_id+ '\');">浏览</a>&nbsp;&nbsp;|&nbsp;&nbsp;';
	html += '	        <a onclick="$(\'#' +thumb_id+ '\').attr(\'src\', \'<?php echo $no_image; ?>\'); $(\'#' +image_id+ '\').attr(\'value\', \'\');">清除</a>';
	html += '	    </div>';
	html += '	 </td>';
	html += '	 <td class="left"><input type="text" name="action['+ action_row +'][url]" value="" size="100"/></td>';
	html += '	 <td><a onclick="$(\'#action-row<?php echo $action_row; ?>\').remove();" class="button">删除</a></td>';
	html += '</tr>';
	html += '</tbody>';

	$('#action_list tfoot').before(html);
	
	action_row++;
}

function image_upload(field, thumb) {
	$('#dialog').remove();
	
	$('#content').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;"><iframe src="index.php?route=common/filemanager&token=<?php echo $token; ?>&field=' + encodeURIComponent(field) + '" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto"></iframe></div>');
	
	$('#dialog').dialog({
		title: '选择图片',
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

$('#tabs a').tabs(); 
//--></script>
<?php echo $footer; ?> 