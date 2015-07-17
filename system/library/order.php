<?php

/*微信订单编号格式：
 * 平台：0-9 公众号支付(1)、小额刷卡(2)
 * 支付类型：0-9 JSAPI(0)、NATIVE(1)、APP支付(2)
 * 业务类型：0-9 菜鸽子(0)、快消品、等等
 * 时间：20040801150101 当前时间，年月日时分秒
 * 序号：00 同一时间发生的重复订单序号不同
*/
function new_wx_orderid($platform = 1, $paytype = 0, $btype = 0, $serial = 0, $time = '') {
	$stime = $time;
	if (strlen($time) != 14) $stime = strftime('%Y%m%d%H%M%S');
	return sprintf("%d%d%d%s%02d", $platform, $paytype, $btype, $stime, $serial);
}

function inc_order_serial($orderid) {
	list($platform, $paytype, $btype, $serial, $time) = sscanf($orderid, "%d%d%d%02d%s");
	$serial++;
	if ($serial == 0) return false;
	return new_wx_orderid($platform, $paytype, $btype, $serial, $time);
}

?>