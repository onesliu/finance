<?php echo $header; ?>
<body>
<div data-role="page" id="weixinpaypage" data-theme="a" data-title="微信支付">
	<?php echo $titlebar; ?>
	<div data-role="content">
		<div class="ui-body ui-body-b ui-corner-all">
			<p>正在支付...</p>
		</div>
	</div>
	
	<script type="text/javascript"><!--
	
	/**
	 * 当页面加载完毕后执行，使用方法：
	 * WeixinApi.ready(function(Api){
	 *     // 从这里只用Api即是WeixinApi
	 * });
	 * @param readyCallback
	 */
	function wxJsBridgeReady(readyCallback) {
	    if (readyCallback && typeof readyCallback == 'function') {
	        var Api = this;
	        var wxReadyFunc = function () {
	            readyCallback(Api);
	        };
	        if (typeof window.WeixinJSBridge == "undefined"){
	            if (document.addEventListener) {
	                document.addEventListener('WeixinJSBridgeReady', wxReadyFunc, false);
	            } else if (document.attachEvent) {
	                document.attachEvent('WeixinJSBridgeReady', wxReadyFunc);
	                document.attachEvent('onWeixinJSBridgeReady', wxReadyFunc);
	            }
	        }else{
	            wxReadyFunc();
	        }
	    }
	}
	
	$(document).on("pageinit", "#weixinpaypage", function(){
		wxJsBridgeReady(function(){
			WeixinJSBridge.invoke('getBrandWCPayRequest', {
				"appId" : "<?php echo $appId; ?>",
				"signType" : "<?php echo $signType; ?>",
				"package" : "<?php echo $package; ?>",
				"timeStamp" : "<?php echo $timeStamp; ?>",
				"nonceStr" : "<?php echo $nonceStr; ?>",
				"paySign" : "<?php echo $paySign; ?>"
			},function(res){
				location.href = "<?php echo $pay_result; ?>"; 
			});
		});
	});
	//--></script>

</div>
</body>
