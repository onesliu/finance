<?php
class ControllerQingyouCqBalance extends Controller {
    private $error = array();

    public function index() {
        $this->load->model('qingyou/mq_balance');
        $this->data['balance'] = $this->model_qingyou_mq_balance->balance();
        $this->template = 'qingyou/vtq_balance.tpl';
        $this->response->setOutput($this->render());
    }
}