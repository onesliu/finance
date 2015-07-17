<?php foreach($orders as $order) { ?>
<li><a href="#" onclick="chgpage('<?php echo $order["href"]; ?>');">
	<img src="<?php echo $order['products'][0]['image'];?>"></img>
	<h4 style="margin-top:0px;padding-right:3em;"><?php echo $order['productnames'];?></h4>
	<p style="margin-bottom:0px;"><?php echo $text_total.' '; ?><span style="color:red;"><?php echo $order["total"]; ?></span><br/>
	<?php echo $order["date_added"]; ?></p>
	<p class="ui-li-aside" style="color:<?php 
		switch ($order["order_status_id"]) {
			case '1': echo "blue"; break;
			case '2': echo "red"; break;
			case '3': echo "blue"; break;
			default: echo "black"; break;
		}
	?>;"><strong><?php echo $order["status"];?></strong></p>
</a></li>
<?php } ?>
