<?php echo $header; ?>
<body>
<div data-role="page" id="errpage" data-theme="a" data-title="<?php echo $heading_title; ?>">
	<?php echo $titlebar; ?>
	<div data-role="content">
		<div class="ui-body ui-body-b ui-corner-all">
			<p><?php echo $text_error; ?></p>
		</div>
	</div>
	<?php if (isset($navi)) echo $navi;?>
</div>
</body>
