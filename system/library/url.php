<?php
class Url {
	private $url;
	private $ssl;
	private $index;
	private $rewrite = array();
	
	public function __construct($url, $ssl = '', $index = 'index.php') {
		$this->url = $url;
		$this->ssl = $ssl;
		$this->index = $index;
	}
		
	public function addRewrite($rewrite) {
		$this->rewrite[] = $rewrite;
	}
		
	public function link($route, $args = '', $connection = 'NONSSL') {
		if ($connection ==  'NONSSL') {
			$url = $this->url;
		} else {
			$url = $this->ssl;	
		}
		
		$url .= $this->index.'?route=' . $route;
			
		if ($connection == 'wxpay' && isset($this->session->data['oauth_code']) && isset($this->session->data['oauth_state'])) {
			$args .= '&showwxpaytitle=1&code=' . $this->session->data['oauth_code'] . "&state=" . $this->session->data['oauth_state'];
		}
		
		if ($args) {
			$url .= str_replace('&', '&amp;', '&' . ltrim($args, '&')); 
		}
		
		foreach ($this->rewrite as $rewrite) {
			$url = $rewrite->rewrite($url);
		}
				
		return $url;
	}
	
	public function link2($route, $args = '', $connection = 'NONSSL') {
		if ($connection ==  'NONSSL') {
			$url = $this->url;
		} else {
			$url = $this->ssl;	
		}
		
		$url .= $this->index.'?route=' . $route;
		
		if ($args) {
			$url .= '&' . ltrim($args, '&'); 
		}

		foreach ($this->rewrite as $rewrite) {
			$url = $rewrite->rewrite($url);
		}
				
		return $url;
	}
	
	public function link_rel($route, $args = '') {
		$url = $this->index.'?route=' . $route;
			
		if ($args) {
			$url .= str_replace('&', '&amp;', '&' . ltrim($args, '&')); 
		}
		
		foreach ($this->rewrite as $rewrite) {
			$url = $rewrite->rewrite($url);
		}
				
		return $url;
	}
	
	public function get_index() {
		return $this->url.$this->index;
	}
}
?>