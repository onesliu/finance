<?php
include_once(DIR_APPLICATION."controller/weixin/weixin.php");

class ControllerWeixinRefund extends ControllerWeixinWeixin {
	
	public function index() {
		
		if ($this->weixin_init() != true) {
			return; //首次验证或初始化失败
		}
		
		if (!isset($this->request->get['order_id'])) {
			return;
		}
		$order_id = $this->request->get['order_id'];
		
		$this->load->model('sale/order');
		
		$order_info = $this->model_sale_order->getOrder($order_id);
		if ($order_info == false) {
			return;
		}
		
		if ($order_info['order_status_id'] != 3 ||  //已付款
			$order_info['order_status_id'] != 4 ||  //已完成
			$order_info['order_status_id'] != 5 ||  //已退款（部分退款）
			!isset($order_info['weixin_pay_result']) || 
			$order_info['weixin_pay_result'] == null ||
			$order_info['weixin_pay_result'] == "") {
				return;
		}
		
		$refund_fee = $order_info['total'];
		if (isset($this->request->get['refund_fee'])) {
			$refund_fee = $this->request->get['refund_fee'];
		}
		
		$refund_no = $order_info['refund_id'];
		
		$trade = new PayHelper();
		$trade->parse_response($order_info['weixin_pay_result']);

		$wxPayHelper = new PayHelper();
		$wxPayHelper->add_param("input_charset", 'UTF-8');
		$wxPayHelper->add_param("service_version", '1.1');
		$wxPayHelper->add_param("partner", (string)$this->partnerid);
		$wxPayHelper->add_param("transaction_id", (string)$trade->get('transaction_id'));
		$wxPayHelper->add_param("out_refund_no", (string)($order_id + $refund_no));
		$wxPayHelper->add_param("total_fee", (int)($order_info['total']*100));
		$wxPayHelper->add_param("refund_fee", (int)($refund_fee*100));
		$wxPayHelper->add_param("op_user_id", 'onesliu@10010465');
		$wxPayHelper->add_param("op_user_passwd", 'c9987fcc3669ef7a87ff39f1b30a36b1');
		
		$wxtools = new WeixinTools();
		$request = $wxPayHelper->make_request($this->partnerkey);
		$url = "https://mch.tenpay.com/refundapi/gateway/refund.xml";
		$response = $wxtools->sslPostToWx($url, $request);
		if ($response == false) {
			$this->log->write("weixin refund response error.");
			$this->error();
			return;
		}
		
		$resHelper = new PayHelper();
		$res = $resHelper->parse_response($response);
		if (isset($res->retcode) == false || (int)$res->retcode != 0) {
			$this->log->write("weixin refund response error: \n". $response);
			$this->error();
			return;
		}
		
		if ($resHelper->sign_verify($this->partnerkey) != true) {
			$this->log->write("weixin refund response sign verify error: \n". $response);
			$this->error();
			return;
		}
		
		//退款结果显示
		$trade_state = array('4' => '退款成功',
							'10' => '退款成功',
							'3' => '退款失败',
							'5' => '退款失败',
							'6' => '退款失败',
							'8' => '退款处理中',
							'9' => '退款处理中',
							'11' => '退款处理中',
							'1' => '不确定是否成功，需要重新发起退款',
							'2' => '不确定是否成功，需要重新发起退款',
							'7' => '退款转入到银行卡失败，资金回流到商户号，需人工干预');
		$this->data['error_msg'] = $trade_state[$res->refund_status];
		
		//保存退款结果
		if ($res->refund_status == 1 || $res->refund_status == 2) {
			
		}
		
		$this->response->setOutput($res['content']);
	}
	
}
?>