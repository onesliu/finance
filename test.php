<?php
require('config.php');
require('system/library/wxtools.php');
require('system/helper/json.php');

$menu_def = '{
	"button":[
		{
			"type":"view",
			"name":"我要买菜",
			"url":"AUTO_LOGIN:mobile_store/home"
		},
		{
			"type":"view",
			"name":"我的订单",
			"url":"AUTO_LOGIN:mobile_store/order&id=5"
		},
		{
			"name":"菜鸽子",
			"sub_button":[
				{
					"type":"view",
					"name":"关于我们",
					"url":"AUTO_LOGIN:mobile_store/about"
				}
			]
		}
	]
}';

$data = array();
$data['first'] = "您的订单已付款成功";
$data['keyword1'] = "order_id";
$data['keyword2'] = "time";
$data['keyword3'] = "amount";
$data['keyword4'] = "bank";
$data['remark'] = "如有任何疑问请拨打客服电话18180423915";;

$wx = new WeixinTools();
$str = $wx->prepareMenu($menu_def, '123456');
echo $str, "\n";
return;

$str = $wx->makeModelMsg('123', '456', "http://abc.com?r=123", $data);
echo $str, "\n";

$messages = array();
$messages[0]["title"] = "交易提醒 ";
$messages[0]["description"] = "123";
$messages[0]["url"] = "http://abc.com?r=123";
$messages[0]["picurl"] = "";
$messages[1]["title"] = "交易提醒 ";
$messages[1]["description"] = "456";
$messages[1]["url"] = "http://def.com?r=456";
$messages[1]["picurl"] = "";
$str = $wx->makeKfMsg('123', 'news', $messages);
echo $str, "\n";

$messages = "您好";
$str = $wx->makeKfMsg('123', 'text', $messages, "onesliu@caigezi2");
echo $str, "\n";

$redirect = 'mobile_store/test';
$ua = parse_url($redirect);
print_r($ua);
?>