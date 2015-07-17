<!-- 
<div id="footer">
  <div class="column">
    <h3><?php echo $text_information; ?></h3>
    <ul>
      <?php foreach ($informations as $information) { ?>
      <li><a href="<?php echo $information['href']; ?>">&raquo; <?php echo $information['title']; ?></a></li>
      <?php } ?>
    </ul>
  </div>
  <div class="column">
    <h3><?php echo $text_service; ?></h3>
    <ul>
      <li><a href="<?php echo $contact; ?>">&raquo; <?php echo $text_contact; ?></a></li>
       <li><a href="<?php echo $login; ?>">&raquo; <?php echo $text_login; ?></a></li>
      <li><a href="<?php echo $register; ?>">&raquo; <?php echo $text_register; ?></a></li>
    </ul>
  </div>
</div>

<div id="switch-version"><a href="<?php echo $desktop_version; ?>"><?php echo $text_desktop_version; ?></a></div>
 -->
</div>

<script type="text/javascript"><!--
$('.row-title').bind('click', function() {
	if ($(this).hasClass('active')) {
		$(this).removeClass('active');
	} else {
		$(this).addClass('active');
	}
		
	$(this).parent().find('.row-content').slideToggle('slow');
});
//--></script>

</body></html>