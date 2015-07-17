<?php echo $header; ?>
<body>
<div data-role="page" id="weixinclosed" data-theme="a" data-title="商城维护">
	<?php if (isset($titlebar)) echo $titlebar; else { ?>
	<div data-role="header" data-position="fixed" data-theme="a" data-tap-toggle="false">
		<h1 style="padding:.4em 0;"><img src="<?php echo $logo; ?>" style="height:25px;" /></h1>
	</div>
	<?php } ?>
	<div data-role="content">
		<div class="ui-body ui-body-b ui-corner-all">
			<p><?php echo $error_msg; ?></p>
		</div>
		<?php if (isset($continue)) {?>
		<a href="<?php echo $continue; ?>" class="ui-btn ui-btn-a ui-corner-all"><?php echo $text_continue; ?></a>
		<?php } ?>
	</div>
	<?php if (isset($navi)) echo $navi;?>
</div>
</body>

