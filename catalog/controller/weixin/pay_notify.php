<?php
include_once(DIR_APPLICATION."controller/weixin/weixin.php");

class ControllerWeixinPayNotify extends ControllerWeixinWeixin { 
	public function index() {
		
		if ($this->weixin_init() != true) {
			$this->log->write("weixin notify: 微信接口初始化失败");
			return; //首次验证或初始化失败
		}
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$xmlstr = file_get_contents("php://input");
		
			$resHelper = new PayHelper();
			$res = $resHelper->parse_response($xmlstr);
			if (isset($res->return_code) == false || isset($res->result_code) == false ||
				(string)$res->return_code != 'SUCCESS' || (string)$res->result_code != 'SUCCESS') {
				$this->log->write("weixin notify paied fail: \n". $xmlstr);
				$this->success(); //返回通知正常接收
				return;
			}
			
			if ($resHelper->sign_verify($this->partnerkey) != true) {
				$this->log->write("weixin notify sign verify error: \n". $xmlstr);
				$this->error(); //返回通知校验错误
				return;
			}
			
			$this->paied_success($res, $xmlstr); //更新数据库订单支付成功
			$this->log->write("weixin pay notify success: ".$res->out_trade_no);
			$this->success(); //返回通知正常接收
		}
	}
	
	public function error() {
		$xml = new PayHelper();
		$xml->add_param('return_code', 'FAIL');
		$xml->add_param('return_msg', 'sign verify error');
		$this->response->setOutput($xml->make_param_xml());
	}

	private function success() {
		$xml = new PayHelper();
		$xml->add_param('return_code', 'SUCCESS');
		$xml->add_param('return_msg', 'notify ok');
		$this->response->setOutput($xml->make_param_xml());
	}
	
	private function paied_success($res, $result) {
		$this->load->model('checkout/order');
		$this->load->model('account/customer');
		
		$orderid = $res->out_trade_no;
		$order_info = $this->model_checkout_order->fastgetOrder($orderid);
		
		if ($order_info == false) {
			$this->log->write('weixin notified, but order can not find in db, order_id='.$orderid);
			return;
		}
		
		if ($order_info['order_status_id'] != 2) {
			$this->log->write('weixin notified, but order is not paying status, order_id='.$orderid.' order_status='.$order_info['order_status_id']);
			return;
		}
		
		$this->model_checkout_order->orderChangeStatus($order_info);
		
		$order_info['weixin_pay_result'] = $result;
		$this->model_checkout_order->fastupdate($orderid, $order_info);
		
		//发送支付成功消息
		$customer = $this->model_account_customer->getCustomer($order_info['customer_id']);
		if ($customer == null) {
			$this->log->write('找不到订单客户信息，无法发送支付成功消息');
			return;
		}
		
		$url = $this->url->link2('mobile_store/order/info', 'order_id='.$orderid);
		$time = $res->time_end;
		$amount = $this->currency->format($res->total_fee/100);
		$bank = $res->bank_type;
		$this->sendPayNotify($url, $orderid, $time, $amount, $bank, $customer['email']);
	}
	
}

?>