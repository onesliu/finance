<?php echo $header; ?>
<body>
<div id="alertPopup" data-role="popup" data-theme="b" style="max-width:19.5em;">
	<div data-role="header" data-theme="b">
		<h1>提示</h1>
		<div class="ui-icon-info ui-btn-icon-notext ui-corner-all ui-btn-left"></div>
	</div>
	<div role="main" class="ui-content">
		<p></p>
		<center><a href="javascript:;" data-rel="back" class="ui-btn ui-mini ui-btn-inline ui-corner-all ui-shadow">知道了</a></center>
	</div>
</div>

<div data-role="page" data-theme="a" id="homepage">
	<div data-role="panel" id="user_panel" data-position="left" data-display="overlay" data-theme="b">
		<div class="ui-panel-inner">
			<ul data-role="listview" data-theme="b" class="menu-list">
				<li class="ui-li-has-thumb">
					<a href="javascript:;" id="loginStatus">
						<div class="my-btn-icon ui-icon-user40 ui-btn-icon-notext ui-btn-left"></div>
						<h2>未登录</h2>
						<p>普通会员</p>
					</a>
				</li>
			</ul>
		</div>
		<div class="ui-panel-inner">
			<ul data-role="listview" data-icon="false" data-theme="b" class="menu-list">
				<li><a href="#registerpage" style="border:0;" class="ui-btn ui-icon-user ui-btn-icon-left">立即注册</a></li>
				<li><a href="#applylistpage" style="border:0;" class="ui-btn ui-icon-shop ui-btn-icon-left">我的贷款</a></li>
				<li><a href="#invitepage" id="invitelink" style="border:0;" class="ui-btn ui-icon-tag ui-btn-icon-left">邀请会员</a></li>
				<li><a href="#userlevelpage" id="userlevellink" style="border:0;" class="ui-btn ui-icon-bullets ui-btn-icon-left">下级会员</a></li>
				<li><a href="javascript:;" class="ui-btn ui-icon-info ui-btn-icon-left">关于我们</a></li>
				<li><a href="javascript:;" data-rel="close" style="border:0;" class="ui-btn ui-icon-back ui-btn-icon-left">关闭</a></li>
			</ul>
		</div>
	</div>
	<div id="categoryDiv" data-role="header" data-position="fixed" data-theme="a" data-tap-toggle="false">
		<h1>寻钱宝</h1>
		<a href="#user_panel" class="ui-btn ui-icon-user ui-btn-icon-notext ui-corner-all ui-btn-left"></a>
		<div style="position:absolute;top:1.1em;left:3em;font-size:.8em;">账号</div>
	</div>
	<div data-role="content" style="padding:0;">
		<div class="">
			<p><center>
				<fieldset data-role="controlgroup" data-type="horizontal">
					<button data-icon="home" id="btnProduct">所有产品</button>
					<button data-icon="search" id="btnProductFilter">产品筛选</button>
					<button data-icon="location" data-iconpos="notext" id="btnSite">网点地图</button>
					<button data-icon="alert" data-iconpos="notext" id="btnFaq">常见问题</button>
					<!-- 
					<label for="btnMore" class="select">更多</label>
					<select name="menuMore" id="menuMore" data-mini="true" data-native-menu="false">
							<option>选择...</option>
							<option value="{{rvalue_id}}" {{#selected}}selected="selected"{{/selected}}>{{rvalue}}</option>
					</select>
					-->
				</fieldset>
			</center></p>
		</div>
		<div class="">
			<ul data-role="listview" data-theme="b" id="categorylist" style="margin:0;">
			</ul>
		</div>
	</div>
	<div class="pfooter footerbar" data-role="footer" data-position="fixed" data-theme="a" data-tap-toggle="false">
		<div>
			<div class="cart ui-icon-check ui-btn-icon-notext ui-corner-all ui-btn-left"></div>
			<a href="#applylistpage" class="abtn ui-btn ui-mini ui-corner-all ui-btn-right">我的贷款</a>
			<h3 class="sum">已申请<span class="price" id="sumCount">0</span>个贷款产品</h3>
		</div>
	</div>
	<script type="text/template" id="categoryItem">
		{{#info}}
		<li category_id="{{category1}} {{category2}}">
			<a href="javascript:;" class="productlist">
				<img src="{{no_image}}" _src="{{image}}" style="left:.4em;"/>
				<div>
					<div class="name" style="font-size:1.2em !important;">{{category2}}</div>
					<div class="desc">{{category1}}</div>
				</div>
				<span class="ui-li-count">{{cnt}}</span>
			</a>
		</li>
		{{/info}}
	</script>
</div>

<div data-role="page" data-theme="a" id="productpage">
	<div data-role="header" data-position="fixed" data-theme="a" data-tap-toggle="false">
		<a href="javascript:;" data-rel="back" class="ui-btn ui-icon-arrow-l ui-btn-icon-notext ui-corner-all"></a>
		<h1 id="plistName">产品列表</h1>
	</div>
	<div data-role="content">
		<div class="ui-body productlist">
			<ul data-role="listview" id="productlist">
			</ul>
		</div>
	</div>
	<div class="pfooter footerbar" data-role="footer" data-position="fixed" data-theme="a" data-tap-toggle="false">
		<div>
			<div class="cart ui-icon-check ui-btn-icon-notext ui-corner-all ui-btn-left"></div>
			<a href="#applylistpage" class="abtn ui-btn ui-mini ui-corner-all ui-btn-right">我的贷款</a>
			<h3 class="sum">已申请<span class="price" id="sumCount">0</span>个贷款产品</h3>
		</div>
	</div>
	<script type="text/template" id="productItem">
		{{#info}}
		<li product_id={{product_id}}>
			<img src="{{no_image}}" _src="{{image}}" />
			<div class="info">
				<div class="name">{{name}}</div>
				<div class="desc">额度：{{limit}}</div>
				<div class="desc">月利率：{{rate}}</div>
				<div class="desc">贷款期限：{{period}}</div>
				<div class="desc">还款方式：{{repayment}}</div>
				<div class="desc">适贷年龄：{{age}}</div>
			</div>
			<div class="btnWrap">
				<button class="ui-btn ui-mini ui-btn-inline ui-corner-all">填资料</button>
				<span>匹配度</span>
				<span style="color:red;">{{match}}</span>
			</div>
		</li>
		{{/info}}
	</script>
</div>

<div data-role="page" data-theme="a" id="detailpage">
	<div data-role="header" data-position="fixed" data-theme="a" data-tap-toggle="false">
		<a href="javascript:;" data-rel="back" class="ui-btn ui-icon-arrow-l ui-btn-icon-notext ui-corner-all"></a>
		<h1 id="productName">产品名</h1>
	</div>
	<div data-role="content">
		<div class="ui-body" id="pinfo1"></div>
		<div class="ui-body line-box">
			<p>申请条件填写</p>
			<p id="pinfo2"></p>
		</div>
		<div class="ui-body line-box">
			<p>附加条件</p>
			<p id="remark"></p>
		</div>
		<div class="ui-body line-box">
			<p>所需材料</p>
			<p id="material"></p>
		</div>
		<script type="text/template" id="productInfo1">
			<div class="name">{{name}}</div>
			<div class="desc">额度：{{limit}}</div>
			<div class="desc">月利率：{{rate}}</div>
			<div class="desc">贷款期限：{{period}}</div>
			<div class="desc">还款方式：{{repayment}}</div>
			<div class="desc">适贷年龄：{{age}}</div>
		</script>
		
		<script type="text/template" id="productInfo2">
			<form>
			{{#require}}
				{{#isset}}
				<label for="req{{uuid}}" class="select">{{name}}</label>
				<select name="req{{uuid}}" id="req{{uuid}}" rid="{{require_id}}" 
					data-mini="true" data-native-menu="true">
					<option>选择...</option>
					{{#rvs}}
					<option value="{{rvalue_id}}" {{#selected}}selected="selected"{{/selected}}>{{rvalue}}</option>
					{{/rvs}}
				</select>
				{{/isset}}
				{{#isnumber}}
				<label for="req{{uuid}}">{{name}}</label>
				<input type="tel" name="req{{uuid}}" id="req{{uuid}} data-clear-btn="true" 
					data-mini="true" rid="{{require_id}}" rvid="{{#rvs}}{{rvalue_id}}{{/rvs}}" value="{{crvalue}}" />
				{{/isnumber}}
			{{/require}}
			</form>
		</script>
	</div>
	<div class="footerbar" data-role="footer" data-position="fixed" data-theme="a" data-tap-toggle="false">
		<div>
			<center><a href="#applyDialog" data-rel="popup" data-position-to="window" data-transition="slideup" class="abtn ui-btn ui-corner-all">立即申请</a></center>
		</div>
	</div>
	
	<div id="applyDialog" data-role="popup" data-theme="a" class="ui-corner-all">
		<div role="main" class="ui-content" style="padding: 1em 1em">
			<a href="javascript:;" data-rel="back" class="ui-btn ui-corner-all ui-icon-delete ui-btn-icon-notext ui-btn-right">Close</a>
			<p>申请额度（万）</p>
			<input type="tel" name="applimit" id="applimit" data-clear-btn="true" placeholder="申请额度" />
			<p style="display:none;color:red;"></p>
			<select name="appperiod" id="appperiod" data-native-menu="true">
				<option>还款期限（月）</option>
			</select>
			<p style="display:none;color:red;"></p>
			<center><a href="javascript:;" id="applyBtn" class="ui-btn ui-mini ui-btn-inline ui-corner-all ui-shadow">确认申请</a>
			<p style="display:none;color:red;"></p>
			</center>
		</div>
	</div>
	
</div>

<div data-role="page" data-theme="a" id="loginpage">
	<div data-role="header" data-position="fixed" data-theme="a" data-tap-toggle="false">
		<a href="#homepage" id="btnloginback" class="ui-btn ui-icon-arrow-l ui-btn-icon-notext ui-corner-all"></a>
		<h1>登 录</h1>
	</div>
	<div data-role="content">
		<div class="ui-body">
			<form>
				<input name="user" type="text" data-clear-btn="true" placeholder="手机号码/电子邮件"></input>
				<input name="pwd" type="password" data-clear-btn="true" placeholder="密码"></input>
			</form>
			<p>&nbsp;</p>
			<p><a href="javascript:;" id="btnlogin" enable="true" class="btn-red ui-btn ui-corner-all">登 录</a></p>
		</div>
	</div>
</div>

<div data-role="page" data-theme="a" id="logininfopage">
	<div data-role="header" data-position="fixed" data-theme="a" data-tap-toggle="false">
		<a href="javascript:;" data-rel="back" class="ui-btn ui-icon-arrow-l ui-btn-icon-notext ui-corner-all"></a>
		<h1>帐户信息</h1>
	</div>
	<div data-role="content">
		<div class="ui-body">
			<p id="login_info"></p>
		</div>
		<div class="ui-body line-box">
			<label for="new_email">用户名：</label>
			<input name="new_name" id="new_name" type="text" data-clear-btn="true" placeholder="用户名"></input>
			<label for="new_email">电子邮件：</label>
			<input name="new_email" id="new_email" type="email" data-clear-btn="true" placeholder="电子邮件"></input>
			<p style="margin-top:1em;"><a href="javascript:;" id="btnmodify" enable="true" class="ui-btn ui-corner-all">保存修改</a></p>
		</div>
		<div class="ui-body line-box">
			<p><a href="#newpwdpage" id="btnnewpwd" enable="true" class="ui-btn ui-corner-all">修改密码</a></p>
			<p><a href="javascript:;" id="btnlogout" enable="true" class="ui-btn ui-corner-all">退出登录</a></p>
		</div>
	</div>
</div>

<div data-role="page" data-theme="a" id="newpwdpage">
	<div data-role="header" data-position="fixed" data-theme="a" data-tap-toggle="false">
		<a href="javascript:;" data-rel="back" class="ui-btn ui-icon-arrow-l ui-btn-icon-notext ui-corner-all"></a>
		<h1>修改密码</h1>
	</div>
	<div data-role="content">
		<div class="ui-body">
			<form>
				<input name="newpwd1" type="password" data-clear-btn="true" placeholder="新密码"></input>
				<input name="newpwd2" type="password" data-clear-btn="true" placeholder="再次输入密码"></input>
			</form>
			<p>&nbsp;</p>
			<p><a href="javascript:;" id="btn_newpwd_ok" enable="true" class="btn-red ui-btn ui-corner-all">确 定</a></p>
		</div>
	</div>
</div>

<div data-role="page" data-theme="a" id="registerpage">
	<div data-role="header" data-position="fixed" data-theme="a" data-tap-toggle="false">
		<a href="javascript:;" data-rel="back" class="ui-btn ui-icon-arrow-l ui-btn-icon-notext ui-corner-all"></a>
		<h1>客户注册</h1>
	</div>
	<div data-role="content">
		<div class="ui-body">
			<p>请填写正确的电子邮件和手机号码，用于申请贷款时提交资料和与您联系。</p>
			<form>
				<input name="telephone" type="text" data-clear-btn="true" placeholder="手机号码"></input>
				<input name="email" type="email" data-clear-btn="true" placeholder="电子邮件"></input>
				<input name="username" type="text" data-clear-btn="true" placeholder="用户名"></input>
				<input name="password" type="password" data-clear-btn="true" placeholder="密码"></input>
				<input name="password2" type="password" data-clear-btn="true" placeholder="再次输入密码"></input>
				<input name="invitecode" type="text" data-clear-btn="true" placeholder="邀请码/邀请电话"></input>
				<p style="font-size:.9em;">邀请码可以从我司业务人员手中获得。同时，任何已注册的手机号码也是邀请码。</p>
			</form>
			<br/>
			<a href="javascript:;" id="btnregister" enable="true" class="btn-red ui-btn ui-corner-all">注 册</a>
		</div>
	</div>
</div>

<div data-role="page" data-theme="a" id="invitepage">
	<div data-role="header" data-position="fixed" data-theme="a" data-tap-toggle="false">
		<a href="javascript:;" data-rel="back" class="ui-btn ui-icon-arrow-l ui-btn-icon-notext ui-corner-all"></a>
		<h1>获取邀请码</h1>
	</div>
	<div data-role="content">
		<div class="ui-body">
			<p>以下产生的邀请码用于注册新的帐号，新帐号自动与您的帐号关联。</p>
		</div>
		<div id="userlevel0" class="ui-body">
			<center>
				<input id="invitebtn0" type="button" value="获取客户邀请码"></input>
				<input id="codelabel0" type="text" readonly value="" />
			</center>
		</div>
		<div id="userlevel1" class="ui-body line-box">
			<center>
				<input id="invitebtn1" type="button" value="获取业务员邀请码"></input>
				<input id="codelabel1" type="text" readonly value="" />
			</center>
		</div>
		<div id="userlevel2" class="ui-body line-box">
			<center>
				<input id="invitebtn2" type="button" value="获取代理商邀请码"></input>
				<input id="codelabel2" type="text" readonly value="" />
			</center>
		</div>
	</div>
</div>

<div data-role="page" data-theme="a" id="userlevelpage">
	<div data-role="header" data-position="fixed" data-theme="a" data-tap-toggle="false">
		<a href="javascript:;" id="userlevelback" class="ui-btn ui-icon-arrow-l ui-btn-icon-notext ui-corner-all"></a>
		<h1>下级会员</h1>
	</div>
	<div data-role="content" style="padding:0;">
		<div class="">
			<ul data-role="listview" data-theme="b" id="userlist" data-filter="true" data-filter-placeholder="查找..." style="margin:0;">
			</ul>
		</div>
	</div>
	<script type="text/template" id="userlistItem">
		{{#info}}
		<li userid={{userid}} subcnt={{subcnt}}>
			<a href="javascript:;" class="productlist {{#iscustomer}}icon-hide{{/iscustomer}}">
				<img src="{{no_image}}" _src="{{image}}" style="left:.4em;"/>
				<div>
					<div class="name" style="font-size:1.2em !important;">{{userid}} {{name}}</div>
					<div class="desc">{{typename}}：{{email}}</div>
					<div class="desc">注册时间：{{date_added}}</div>
				</div>
				{{#isuser}}<span class="ui-li-count">{{subcnt}}</span>{{/isuser}}
			</a>
		</li>
		{{/info}}
	</script>
</div>

<div data-role="page" data-theme="a" id="searchpage">
	<div data-role="header" data-position="fixed" data-theme="a" data-tap-toggle="false">
		<a href="javascript:;" data-rel="back" class="ui-btn ui-icon-arrow-l ui-btn-icon-notext ui-corner-all"></a>
		<h1>筛选条件</h1>
	</div>
	<div data-role="content">
		<div class="ui-body">
			<p>请输入您的个人基本情况，以便对贷款产品进行筛选</p>
			<p id="baseRequire"></p>
		</div>
	</div>
	<div class="footerbar" data-role="footer" data-position="fixed" data-theme="a" data-tap-toggle="false">
		<div>
			<center><a href="#homepage" id="btnFilter" class="abtn ui-btn ui-mini ui-corner-all">筛 选</a></center>
		</div>
	</div>
</div>

<div data-role="page" data-theme="a" id="faqpage">
	<div data-role="header" data-position="fixed" data-theme="a" data-tap-toggle="false">
		<a href="javascript:;" data-rel="back" class="ui-btn ui-icon-arrow-l ui-btn-icon-notext ui-corner-all"></a>
		<h1>温馨提示</h1>
	</div>
	<div data-role="content">
		<div class="ui-body">
			<p>1. 注册后可以进行精确产品筛选及申请。</p>
			<p>2. 平台所提供贷款产品匹配度，不包含个人征信状况及负债状况。可能会因个人征信状况或负债状况导致申请失败。</p>
			<p>3. 请务必真实准确的填写相关信息，便于精确匹配。</p>
		</div>
	</div>
</div>


<!-- 申请相关页面 -->

<div data-role="page" data-theme="a" id="applylistpage">
	<div data-role="header" data-position="fixed" data-theme="a" data-tap-toggle="false">
		<a href="#homepage" class="ui-btn ui-icon-arrow-l ui-btn-icon-notext ui-corner-all"></a>
		<h1>我的贷款</h1>
	</div>
	<div data-role="content">
		<div class="ui-body productlist">
			<ul data-role="listview" id="applylist">
			</ul>
		</div>
	</div>
	<script type="text/template" id="applyItem">
		{{#info}}
		<li app_id={{app_id}}>
			<img src="{{no_image}}" _src="{{image}}" />
			<div class="info">
				<div class="name">{{product_name}}</div>
				<div class="desc">申请时间：{{date_added}}</div>
				<div class="desc">申请额度：{{rst_limit}}万</div>
				<div class="desc">还款期限：{{rst_period}}个月</div>
				{{#isover}}<div class="desc">月利率：{{rst_rate}}%</div>{{/isover}}
				{{#isuser}}<div class="desc">客户：{{customer_name}} {{customer_phone}}</div>{{/isuser}}
				{{#ishandling}}
				{{#iscustomer}}<div class="desc">代理人：{{user_name}} {{user_phone}}</div>{{/iscustomer}}
				{{/ishandling}}
			</div>
			<div class="btnWrap">
				<button class="ui-btn ui-mini ui-btn-inline ui-corner-all">{{appstatus}}</button>
				{{#ishandling}}<span style="color:red;">{{cur_step_name}}</span>{{/ishandling}}
			</div>
		</li>
		{{/info}}
	</script>
</div>

<div data-role="page" data-theme="a" id="applysteppage">
	<div data-role="header" data-position="fixed" data-theme="a" data-tap-toggle="false">
		<a href="javascript:;" data-rel="back" class="ui-btn ui-icon-arrow-l ui-btn-icon-notext ui-corner-all"></a>
		<h1>申请步骤</h1>
	</div>
	<div data-role="content">
		<div class="ui-body">
			<p>办理进度</p>
			<ul data-role="listview" data-inset="true" id="steplist">
			</ul>
		</div>
	</div>
	<script type="text/template" id="stepItem">
		{{#info}}
		<li step_id={{step_id}}>
			<div>
				<p style="float:left;font-size:1em !important;{{namecolor}}">{{step_name}}</p>
				<p style="float:right;font-size:1em !important;{{actioncolor}}">{{stepstatus}}</p>
			</div>
		</li>
		{{/info}}
	</script>
	<div id="stepDialog" data-role="popup" data-theme="a" class="ui-corner-all">
		<div role="main" class="ui-content" style="padding: 1em 1em">
			<a href="javascript:;" data-rel="back" class="ui-btn ui-corner-all ui-icon-delete ui-btn-icon-notext ui-btn-right">Close</a>
			<select name="stepStatus" id="stepStatus" data-native-menu="true">
			</select>
			<p style="display:none;color:red;"></p>
			<center><a href="javascript:;" id="stepStatusBtn" class="ui-btn ui-mini ui-btn-inline ui-corner-all ui-shadow">确 定</a>
			<p style="display:none;color:red;"></p>
			</center>
		</div>
	</div>
</div>

<div data-role="page" data-theme="a" id="applydetailpage">
	<div data-role="header" data-position="fixed" data-theme="a" data-tap-toggle="false">
		<a href="javascript:;" data-rel="back" class="ui-btn ui-icon-arrow-l ui-btn-icon-notext ui-corner-all"></a>
		<h1>申请详情</h1>
	</div>
	<div data-role="content" id="detailContent">
	</div>
	<script type="text/template" id="applydetail">
		<div class="ui-body">
			<h3>{{product_name}}</h3>
			<p>{{category1}} {{category2}}</p>
			<p>申请时间：{{date_added}}</p>
			{{#isover}}
			<p>结束时间：{{date_over}}</p>
			<p>利率：{{rst_rate}}%</p>
			{{/isover}}
			<p>申请额度：{{rst_limit}}万</p>
			<p>还款期限：{{rst_period}}个月</p>
		</div>
		<div class="ui-body line-box">
			{{#isuser}}
			<p>机构：{{belong}}</p>
			<p>客户：{{customer_name}} {{customer_phone}}</p>
			{{/isuser}}
			{{#iscustomer}}<p>代理人：{{user_name}} {{user_phone}}</p>{{/iscustomer}}
			<p>申请状态：<span style="color:blue;">{{appstatus}}</span></p>
			{{#ishandling}}
			<p>当前步骤：{{cur_step_name}} {{stepstatus}}</p>
			{{/ishandling}}
		</div>
		<div class="ui-body line-box">
			<p>申请人条件：</p>
			{{#reqs}}
			<p>{{name}} {{value}}</p>
			{{/reqs}}
		</div>
		<div class="ui-body line-box">
			<p>{{{material}}}</p>
		</div>
		<div class="ui-body line-box">
			<p>{{{remark}}}</p>
		</div>
		{{#isuser}}
		<div class="ui-body line-box">
			<button class="ui-btn ui-corner-all">接 单</button>
		</div>
		{{/isuser}}
	</script>
</div>

</body>
