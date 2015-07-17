<?php require('kfheader.tpl'); ?>
<body>
<div data-role="page" id="kforders" data-theme="a">
	<div data-role="content"></div>
	
	<script type="text/javascript"><!--

	function MCS_ClientNotify(EventData) {
	    var evdata = strToJson(EventData);
	    switch(evdata['event']){
	        case 'OnUserChange':{
	            OnUserChange(evdata);
	            break;
	        }
	        case 'OnMapMsgClick':{
	            OnMapMsgClick(evdata);
	            break;
	        }
	    }
	}
	
	function strToJson(str){
		var json = (new Function("return " + str))();
		return json;
	}
	
	function OnUserChange(evdata) {
		var url = "<?php echo $orderlist; ?>" + "&customer=" + evdata.useraccount;
		$( ":mobile-pagecontainer" ).pagecontainer( "change", url );
	}
	
	function OnMapMsgClick(evdata) {
	}
	
	//--></script>
</div>
</body>
