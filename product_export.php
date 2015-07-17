<?php
// Configuration
if (file_exists('config.php')) {
	require_once('config.php');
}  

// Startup
require_once(DIR_SYSTEM . 'startup.php');

// Application Classes
require_once(DIR_SYSTEM . 'library/customer.php');
require_once(DIR_SYSTEM . 'library/affiliate.php');
require_once(DIR_SYSTEM . 'library/currency.php');
require_once(DIR_SYSTEM . 'library/tax.php');
require_once(DIR_SYSTEM . 'library/weight.php');
require_once(DIR_SYSTEM . 'library/length.php');
require_once(DIR_SYSTEM . 'library/cart.php');
require_once(DIR_SYSTEM . 'library/coupon.php');

// Database 
$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

$query = $db->query("select name,price from oc_product p join oc_product_description pd on p.product_id=pd.product_id where product_type==1 order by ean");
foreach ($query->rows as $p) {
	echo $p['name'].','.$p['price'],"\n";
}

?>