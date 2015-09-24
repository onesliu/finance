<?php
$exp = "(3|23)&(43&2&6)";

$a[3] = true;
$a[23] = false;

$b = new stdClass();
$b->b43 = true;
$b->b2 = true;
$b->b6 = true;

$e["3"] = '$a[3]';
$e["23"] = '$a[23]';
$e["43"] = '$b->b43';
$e["2"] = '$b->b2';
$e["6"] = '$b->b6';

class cls {
	static function call($ms) {
		global $e;	
		return "(" . $e[$ms[0]] . ")";
	}
}

$expstr = preg_replace_callback("|\d+|", array("cls", "call"), $exp);
//$ev = false;
eval("\$ev = $expstr;");
echo "$exp = $ev\n";
?>