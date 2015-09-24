<?php echo $header; ?>
<body>
<div data-role="page" data-theme="a" id="invitepage">
	<div data-role="header" data-position="fixed" data-theme="a" data-tap-toggle="false">
		<a href="javascript:;" data-rel="back" class="ui-btn ui-icon-arrow-l ui-btn-icon-notext ui-corner-all"></a>
		<h1>邀请客户</h1>
	</div>
	<div data-role="content">
		<div class="ui-body">
			<center>
				<input id="invitebtn" type="button" value="获取邀请码"></input>
				<input id="codelabel" type="text" readonly value="" />
			</center>
		</div>
	</div>
	<div data-role="popup" id="invitePopup" class="ui-content" data-dismissible="false">
	  <a href="javascript:;" data-rel="back" class="ui-btn ui-corner-all ui-shadow ui-icon-delete ui-btn-icon-notext ui-btn-right ui-btn-a">Close</a>
	  <p></p>
	</div>
</div>
</body>
