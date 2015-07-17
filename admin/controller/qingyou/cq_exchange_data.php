<?php
class ControllerQingyouCqExchangeData extends Controller {
    private $error = array();

    public function index() {

    }

    public function upload()
    {
        $this->load->model('qingyou/mq_exchange_data');
        $type = $this->request->get['type'];
        $this->data['upload'] = $this->model_qingyou_mq_exchange_data->uploadData($type);
//        $this->response->setOutput($this->render());
    }

    public function download()
    {
        $this->load->model('qingyou/mq_exchange_data');
        $shopNo = $this->request->get['shopNo'];
        $type = $this->request->get['type'];
        $this->data['download'] = $this->model_qingyou_mq_exchange_data->downloadData($shopNo, $type);
        $this->template = 'qingyou/vtq_exchange_data.tpl';
        $this->response->setOutput($this->render());
    }
}