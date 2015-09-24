<?php echo $header; ?>
<body>
<div id="alertPopup" data-rel="popup" data-theme="b" style="max-width:19.5em;">
	<div data-role="header" data-theme="b">
		<h1>��ʾ</h1>
		<div class="ui-icon-info ui-btn-icon-notext ui-corner-all ui-btn-left"></div>
	</div>
	<div role="main" class="ui-content">
		<p></p>
		<center><a href="javascript:;" data-rel="back" class="ui-btn ui-mini ui-btn-inline ui-corner-all ui-shadow">֪����</a></center>
	</div>
</div>

<div data-role="page" data-theme="a" id="applylistpage">
	<div data-role="header" data-position="fixed" data-theme="a" data-tap-toggle="false">
		<a href="javascript:;" data-rel="back" class="ui-btn ui-icon-arrow-l ui-btn-icon-notext ui-corner-all"></a>
		<h1>�����б�</h1>
	</div>
	<div data-role="content">
		<div class="ui-body productlist">
			<ul data-role="listview" id="productlist">
			</ul>
		</div>
	</div>
	<script type="text/template" id="applyItem">
		{{#info}}
		<li app_id={{app_id}}>
			<img src="{{no_image}}" _src="{{image}}" />
			<div class="info">
				<div class="name">{{product_name}}</div>
				<div class="desc">����ʱ�䣺{{date_added}}</div>
				<div class="desc">�����ʣ�{{rate}}</div>
				<div class="desc">�����ȣ�{{rst_limit}}</div>
				<div class="desc">�������ޣ�{{rst_period}}</div>
				{{#isover}}<div class="desc">���ʣ�{{rst_rate}}</div>{{/isover}}
				{{#isuser}}<div class="desc">�ͻ���{{customer_name}} {{customer_phone}}</div>{{/isuser}}
				{{#iscustomer}}<div class="desc">�����ˣ�{{user_name}} {{user_phone}}</div>{{/iscustomer}}}}
			</div>
			<div class="btnWrap">
				<button class="ui-btn ui-mini ui-btn-inline ui-corner-all">{{app_status}}</button>
				<span style="color:red;">{{step_status}}</span>
			</div>
		</li>
		{{/info}}
	</script>
</div>

<div data-role="page" data-theme="a" id="applysteppage">
	<div data-role="header" data-position="fixed" data-theme="a" data-tap-toggle="false">
		<a href="javascript:;" data-rel="back" class="ui-btn ui-icon-arrow-l ui-btn-icon-notext ui-corner-all"></a>
		<h1>���벽��</h1>
	</div>
	<div data-role="content">
		<div class="ui-body productlist">
			<ul data-role="listview" id="productlist">
			</ul>
		</div>
	</div>
	<script type="text/template" id="stepItem">
		{{#info}}
		<li step_id={{step_id}}>
			<img src="{{no_image}}" _src="{{image}}" />
			<div class="info">
				<div class="name">{{step_name}}</div>
			</div>
			<div class="btnWrap">
				<button class="ui-btn ui-mini ui-btn-inline ui-corner-all">{{step_status}}</button>
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
			<center><a href="javascript:;" id="stepStatusBtn" class="ui-btn ui-mini ui-btn-inline ui-corner-all ui-shadow">ȷ ��</a></center>
			<p class="end" style="display:none;color:red;"></p>
		</div>
	</div>
</div>

<div data-role="page" data-theme="a" id="applydetailpage">
	<div data-role="header" data-position="fixed" data-theme="a" data-tap-toggle="false">
		<a href="javascript:;" data-rel="back" class="ui-btn ui-icon-arrow-l ui-btn-icon-notext ui-corner-all"></a>
		<h1>��������</h1>
	</div>
	<div data-role="content">
	</div>
	<script type="text/template" id="applydetail">
		<div class="ui-body">
			<h3>{{product_name}}</h3>
			<p>{{category1}} {{category2}}</p>
			<p>����ʱ�䣺{{date_added}}</p>
			{{#isover}}
			<p>����ʱ�䣺{{date_over}}</p>
			<p>���ʣ�{{rst_rate}}</p>
			{{/isover}}
			<p>�����ȣ�{{rst_limit}}</p>
			<p>�������ޣ�{{rst_period}}</p>
		</div>
		<div class="ui-body line-box">
			{{#isuser}}
			<p>������{{belong}}</p>
			<p>�ͻ���{{customer_name}} {{customer_phone}}</p>
			{{/isuser}}
			{{#iscustomer}}<p>�����ˣ�{{user_name}} {{user_phone}}</p>{{/iscustomer}}}}
			<p>{{app_status}} {{step_status}}</p>
		</div>
		<div class="ui-body line-box">{{material}}</div>
		<div class="ui-body line-box">{{remark}}</div>
		{{#isuser}}
		<div class="ui-body line-box">
			<button class="ui-btn ui-corner-all">�� ��</button>
		</div>
		{{/isuser}}
	</script>
</div>

</body>
