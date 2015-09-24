<?php echo $header; ?>
<body>
<div data-role="page" data-theme="a" id="loginpage">
	<div data-role="header" data-position="fixed" data-theme="a" data-tap-toggle="false">
		<a href="javascript:;" data-rel="back" class="ui-btn ui-icon-arrow-l ui-btn-icon-notext ui-corner-all"></a>
		<h1>登 录</h1>
	</div>
	<div data-role="content">
		<div class="ui-body">
			<form>
				<input name="user" type="text" data-clear-btn="true" placeholder="电子邮件" <?php
					if (isset($user)) echo "value=$user";
				?> ></input>
				<input name="pwd" type="password" data-clear-btn="true" placeholder="密码"></input>
			</form>
			<p>&nbsp;</p>
			<a href="javascript:;" id="btnlogin" enable="true" class="btn-red ui-btn ui-corner-all">登 录</a>
			<a href="#registerpage" enable="true" data-prefetch="ture" class="ui-btn ui-corner-all">注 册</a>
		</div>
	</div>
	<div data-role="popup" id="loginPopup" class="ui-content" data-dismissible="false">
	  <a href="javascript:;" data-rel="back" class="ui-btn ui-corner-all ui-shadow ui-icon-delete ui-btn-icon-notext ui-btn-right ui-btn-a">Close</a>
	  <p></p>
	</div>
</div>

</body>
