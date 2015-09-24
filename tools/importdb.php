<?php
ini_set('display_errors', 1);
require_once("xmlparse.php");

require('../config.php');
require(DIR_SYSTEM . 'library/db.php');
// Database 
$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

$xmlstr = file_get_contents("data.xml");
$xmlstr = str_replace("&#10;", "~", $xmlstr);
$xml = parse_xml($xmlstr);


$require_value_id = 1;
$require_values = array();
$require_groups = array();
$require_types = array();
$exp_req_id = array();

function eval_exp($e) {
	global $exp_req_id;
	$exp_req_id[] = $e[0];
	return $e[0];
}

function make_sql($type, $row) {
	global $require_value_id;
	global $require_values;
	global $require_groups;
	global $require_types;
	global $exp_req_id;
	
	$sql = array();
	if ($type == "products") {
		$age = explode("-", $row[14]);
		if (count($age) != 2) {
			$sql[] = "error age range: ".$row[14];
			return;
		}
		$sql[] = "insert into f_product set product_id='".$row[0].
		"', category1='".$row[2].
		"', category2='".$row[3].
		"', name='".$row[4].
		"', belong='".$row[5].
		"', minlimit='".$row[6].
		"', maxlimit='".$row[7].
		"', minrate='".$row[8].
		"', maxrate='".$row[9].
		"', minperiod='".$row[10].
		"', maxperiod='".$row[11].
		"', periodstep='".$row[12].
		"', repayment='".$row[13].
		"', minage='".$age[0].
		"', maxage='".$age[1].
		"', material='".$row[18].
		"', remark='".((isset($row[19]))?$row[19]:"").
		"' ON DUPLICATE KEY UPDATE ".
		"category1='".$row[2].
		"', category2='".$row[3].
		"', name='".$row[4].
		"', belong='".$row[5].
		"', minlimit='".$row[6].
		"', maxlimit='".$row[7].
		"', minrate='".$row[8].
		"', maxrate='".$row[9].
		"', minperiod='".$row[10].
		"', maxperiod='".$row[11].
		"', periodstep='".$row[12].
		"', repayment='".$row[13].
		"', minage='".$age[0].
		"', maxage='".$age[1].
		"', material='".$row[18].
		"', remark='".((isset($row[19]))?$row[19]:"").
		"'";
		
		$values = explode("<br>", trim($row[16], "<br>"));
		$c_cnt = 0;
		$value_set = array();
		$exps = array();
		foreach($values as $val) {
			$separ = array(":", "：");
			$slin = array();
			foreach($separ as $sep) {
				$slin = explode($sep, $val);
				if (count($slin) == 2)
					break;
			}
			if (count($slin) != 2) {
				echo "wrong require value or expresion: $val\n";
				continue;
			}
			$rid = $slin[0];
			$rrval = $slin[1];
			
			if ($rid == "c") {
				$exps[] = $rrval;
				$c_cnt++;
			} else {
				$rval = "";
				if (!isset($require_groups[$rid])) {
					echo "invalid require id: $rid, cann't find in require_groups array, product_id: $row[0]\n";
					continue;
				}
				$gid = $require_groups[$rid];
				$rv = $require_values[$gid];
				if ($require_types[$rid] == "set") {
					$vs = explode(",", $rrval);
					foreach($vs as $v) {
						$vidx = $v-1;
						if (isset($rv->values[$vidx]))
							$realv = $rv->values[$vidx];
						else {
							echo "product_id: $row[0], requires: $rid, rgroup_id: $gid, rvalue_idx: $vidx\n";
							print_r($rv->values);
						}
						$rval .= (string)$realv . ",";
					}
					$rval = trim($rval, ",");
				} else if ($require_types[$rid] == "order") {
					$op = substr($rrval, 0, 1);
					if ($op != '>' && $op != '<') {
						echo "product_id: $row[0], requires: $rid, rgroup_id: $gid, invalid op: $op\n";
					} else {
						$mval = substr($rrval, 1);
						$mkey = array_search($mval, $rv->realvalues);
						if ($mkey === false) {
							echo "product_id: $row[0], requires: $rid, rgroup_id: $gid, invalid order: $rrval\n";
							echo "can not find $mval in require values.\n";
							print_r($rv->realvalues);
						} else {
							foreach($rv->realvalues as $k => $v) {
								$rval .= "$k,";
								if ($v == $mval) {
									if ($op == "<") break;
									else $rval = "$k,";
								}
							}
							$rval = trim($rval, ",");
						}
					}
				}
				else {
					$rval = $rrval;
				}
				$value_set[$rid] = $rval;
			}
		}
		
		$reqs = explode(",", $row[15]);
		if (count($reqs) != (count($values) - $c_cnt) ) {
			echo "Warning! $row[0] require count not equal.\n";
		}
		
		$r_keys = array_keys($value_set);
		$r_diff = array_diff($r_keys, $reqs);
		if (count($r_diff) > 0) {
			echo "Warning! $row[0] require id not equal.\n";
		}
		
		if (count($reqs) > 1) {
			$sql[] = "delete from f_product_require where product_id=".$row[0];
			foreach($reqs as $rid) {
				if (!is_numeric($rid)) continue;
				
				$expresion = "";
				foreach($exps as $exp) {
					$exp_req_id = array();
					preg_replace_callback("|\d+|", "eval_exp", $exp);
					if (in_array($rid, $exp_req_id)) {
						$expresion = $exp;
						break;
					}
				}
				
				$sql[] = "insert into f_product_require set product_id=".$row[0].
				", require_id=".$rid.
				", value_set='".$value_set[$rid].
				"', expresion='".$expresion."'";
			}
		}
		
		$exds = explode(",", $row[1]);
		if (count($exds) > 1 || $exds[0] > 0) {
			$sql[] = "delete from f_product_exclude where product_id=".$row[0];
			foreach($exds as $exid) {
				$sql[] = "insert into f_product_exclude set product_id=".$row[0].
					", exclude_id=".$exid;
			}
		}
		
		$steps = explode("<br>", trim($row[17], "<br>"));
		$step_id = 1;
		foreach($steps as $step) {
			$sql[] = "insert into f_product_step set product_id=".$row[0].
				", step_id=$step_id".
				", step_name='".$step."'";
			$step_id++;
		}
		
	} else if ($type == "require_value") {
		
		$vals = explode(",", $row[2]);
		$rv = array();
		$realvals = array();
		foreach($vals as $val) {
			$sql[] = "insert into f_require_value set rvalue_id='" . $require_value_id++ .
			"', require_group='".$row[0].
			"', value_type='".$row[1].
			"', rvalue='".$val.
			"' ON DUPLICATE KEY UPDATE ".
			"require_group='".$row[0].
			"', value_type='".$row[1].
			"', rvalue='".$val.
			"'";
			
			$k = $require_value_id-1;
			$rv[] = $k;
			$realvals[$k] = $val;
		}
		$orv = new stdClass();
		$orv->values = $rv;
		$orv->realvalues = $realvals;
		$orv->value_type = $row[1];
		$require_values[$row[0]] = $orv;

	} else if ($type == "require") {
		$sql[] = "insert into f_require set require_id='" . $row[0].
		"', name='".$row[1].
		"', require_group='".$row[2].
		"', class_id='".$row[3].
		"' ON DUPLICATE KEY UPDATE ".
		"name='".$row[1].
		"', require_group='".$row[2].
		"', class_id='".$row[3].
		"'";
		
		$require_groups[$row[0]] = $row[2];
		$require_types[$row[0]] = $require_values[$row[2]]->value_type;
	}
	return $sql;
}

function getLeaf($node) {
	if ($node->childs && $node->childs[0])
		return getLeaf($node->childs[0]);
	else
		return $node;
}

function echo_data($node) {
	global $db;
	
	if ( is_array($node) ) {
		foreach ( $node as $n) {
			echo_data( $n );
		}
	}

	if ( is_object($node) ) {
		if ($node->label == "Row") {
			$r = array();
			foreach($node->childs as $row) {
				$value = getLeaf($row)->value;
				$value = str_replace("~", "<br>", $value);
				$r[] = $db->escape($value);
			}
			if (count($r) > 15)
				$type = "products";
			else if (count($r) == 3)
				$type = "require_value";
			else if (count($r) >= 4 && count($r) <= 5)
				$type = "require";
			
			if (isset($type) && is_numeric($r[0])) {
				$sql = make_sql($type, $r);
				foreach($sql as $s) {
					//echo $s."\n";
					$db->query($s);
				}
			}
		}
		else {
			if ($node->label == "Worksheet") {
				if ($node->attrib && $node->attrib["ss:Name"])
					echo $node->attrib["ss:Name"] . "\n";
			}

			if ($node->childs) {
				foreach ( $node->childs as $n) {
					echo_data( $n );
				}
			}
		}
	}
}

$db->query("truncate f_product");
$db->query("truncate f_product_require");
$db->query("truncate f_product_exclude");
$db->query("truncate f_require");
$db->query("truncate f_require_value");
$db->query("truncate f_product_step");
echo_data($xml);
$db->query("update f_product set product_img='data/products.jpg'");
$db->query("update f_product set category_img='data/categories2.jpg'");
$db->query("update f_product set category_img='data/pcar.jpg' where category2='车信贷'");
$db->query("update f_product set category_img='data/pcar2.jpg' where category2='车抵押贷'");
$db->query("update f_product set category_img='data/phouse2.jpg' where category2='房信贷'");
$db->query("update f_product set category_img='data/phouse.jpg' where category2='房产抵押贷'");
?>