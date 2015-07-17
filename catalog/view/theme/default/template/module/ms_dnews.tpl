<script type="text/javascript" src="https://www.google.com/jsapi?key=ABQIAAAAzU5E6W-kYkDWncD-gLlvEhRUEaat57mcPuL2sxLOgiGfdu1dyxRm8B-okEsXk6nmXsYNVPSFoKIYxA"></script>

<script type="text/javascript">     
	google.load("feeds", "1");
	$(document).ready(function() {
		$('#snews_<?php echo $module; ?>').dnews({ 
			feedurl: '<?php echo $feed_url; ?>', 
			showdetail: false, 
			controls: false,
			controlsalwaysvisible: false,
			entries: <?php echo $limit; ?>
		});
	});
</script>

<div style="position: relative;">
	<div class="news-wrapper" id="snews_<?php echo $module; ?>">
		<div class="news"></div>
	</div>
</div>

