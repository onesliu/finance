<?php if (isset($_SERVER['HTTP_USER_AGENT']) && !strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6')) echo '<?xml version="1.0" encoding="UTF-8"?>'. "\n"; ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" xml:lang="<?php echo $lang; ?>">
<head>
<title><?php echo $title; ?></title>
<base href="<?php echo $base; ?>" />
<meta name="viewport" content="width=100%; initial-scale=1.0; maximum-scale=1.0; minimum-scale=1.0; user-scalable=no;" />
<?php if ($mobile_store_charset) { ?>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<?php } ?>
<?php if ($description) { ?>
<meta name="description" content="<?php echo $description; ?>" />
<?php } ?>
<?php if ($keywords) { ?>
<meta name="keywords" content="<?php echo $keywords; ?>" />
<?php } ?>

<link type="text/css" rel="stylesheet" href="catalog/view/javascript/jquery_mobile/qingyou-theme.min.css?version=1.3" />
<link type="text/css" rel="stylesheet" href="catalog/view/javascript/jquery_mobile/jquery.mobile.icons.min.css" />
<link type="text/css" rel="stylesheet" href="catalog/view/javascript/jquery_mobile/jquery.mobile.custom.structure.min.css">
<link type="text/css" rel="stylesheet" href="catalog/view/javascript/jquery_mobile/qingyou-custom.css?version=1.4">
<link type="text/css" rel="stylesheet" href="catalog/view/javascript/jquery_mobile/gridlist.css?version=1.5">
<link type="text/css" rel="stylesheet" href="catalog/view/javascript/swiper/swiper.min.css" >

<script type="text/javascript" src="catalog/view/javascript/oop.js?version=1.1"></script>
<script type="text/javascript" src="catalog/view/javascript/jquery/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="catalog/view/javascript/mobile_store.js?version=1.4"></script>
<script type="text/javascript" src="catalog/view/javascript/jquery_mobile/jquery.mobile.custom.min.js"></script>
<script type="text/javascript" src="catalog/view/javascript/swiper/swiper.jquery.min.js"></script>
<script type="text/javascript" src="catalog/view/javascript/fastclick.js"></script>

<script type="text/javascript">
var showhelp = <?php echo $showhelp; ?>;
$(document).on('pagecreate', function(event){
	FastClick.attach(document.body);
});
</script>

</head>
