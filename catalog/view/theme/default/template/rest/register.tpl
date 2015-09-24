<?php echo $header; ?>
<body>
<div data-role="page" data-theme="a" id="registerpage">
	<div data-role="header" data-position="fixed" data-theme="a" data-tap-toggle="false">
		<a href="javascript:;" data-rel="back" class="ui-btn ui-icon-arrow-l ui-btn-icon-notext ui-corner-all"></a>
		<h1>客户注册</h1>
	</div>
	<div data-role="content">
		<div class="ui-body">
			<p>请填写正确的电子邮件和手机号码，用于申请贷款时提交资料和与您联系。</p>
			<form>
				<input name="email" type="email" data-clear-btn="true" placeholder="电子邮件"></input>
				<input name="telephone" type="text" data-clear-btn="true" placeholder="手机号码"></input>
				<input name="password" type="password" data-clear-btn="true" placeholder="密码"></input>
				<input name="password2" type="password" data-clear-btn="true" placeholder="再次输入密码"></input>
				<input name="invitecode" type="text" data-clear-btn="true" placeholder="邀请码"></input>
			</form>
			<a href="javascript:;" id="btnregister" enable="true" class="btn-red ui-btn ui-corner-all">注 册</a>
		</div>
	</div>
	<div data-role="popup" id="registerPopup" class="ui-content" data-dismissible="false">
	  <a href="javascript:;" data-rel="back" class="ui-btn ui-corner-all ui-shadow ui-icon-delete ui-btn-icon-notext ui-btn-right ui-btn-a">Close</a>
	  <p></p>
	</div>
</div>

</body>
