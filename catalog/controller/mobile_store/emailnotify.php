<?php  
class ControllerMobileStoreEmailNotify extends Controller {
	public function index() {
		//$product_url = rawurldecode( $this->request->post['product_url'] );
		$this->language->load('mobile_store/emailnotify');
		
		$subject = $this->language->get('text_subject');
		
		$message = nl2br($this->language->get('text_message') . "\n\n");
		
		$mail = new Mail();
		$mail->protocol = $this->config->get('config_mail_protocol');
		$mail->parameter = $this->config->get('config_mail_parameter');
		$mail->hostname = $this->config->get('config_smtp_host');
		$mail->username = $this->config->get('config_smtp_username');
		$mail->password = $this->config->get('config_smtp_password');
		$mail->port = $this->config->get('config_smtp_port');
		$mail->timeout = $this->config->get('config_smtp_timeout');				
		$mail->setTo($this->config->get('config_email'));
		$mail->setFrom($this->config->get('config_email'));
		$mail->setSender($this->config->get('config_name') . ' - Facebook Store');
		$mail->setSubject($subject);
		$mail->setHTML($message);
		$mail->send();
	}
}
?>