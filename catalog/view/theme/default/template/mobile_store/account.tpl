<?php echo $header; ?>
<div id="content" class="round"><?php echo $content_top; ?>
  <h1><?php echo $heading_title; ?></h1>
  <?php if ($success) { ?>
  <div class="success"><?php echo $success; ?></div>
  <?php } ?>
  <div class="link-list">
    <ul>
      <a href="<?php echo $edit; ?>"><li><?php echo $text_edit; ?></li></a>
      <a href="<?php echo $password; ?>"><li><?php echo $text_password; ?></li></a>
      <a href="<?php echo $address; ?>"><li><?php echo $text_address; ?></li></a>
      <a href="<?php echo $logout; ?>"><li><?php echo $text_logout; ?></li></a>
    </ul>
  </div>

  <?php echo $content_bottom; ?></div>
<?php echo $footer; ?> 