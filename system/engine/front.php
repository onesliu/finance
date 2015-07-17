<?php
final class Front {
	protected $registry;
	protected $pre_action = array();
	protected $error;
	
	public function __construct($registry) {
		$this->registry = $registry;
	}
	
	public function addPreAction($pre_action) {
		$this->pre_action[] = $pre_action;
	}
	
  	public function dispatch($action, $error) {
		$this->error = $error;
			
		foreach ($this->pre_action as $pre_action) {
			$result = $this->execute($pre_action);
					
			if ($result) {
				$action = $result;
				
				break;
			}
		}
			//$this->registry->get('log')->write(print_r($action,true));
		while ($action) {
			$action = $this->execute($action);
		}
  	}
    
	private function execute($action) {
		if (file_exists($action->getFile())) {
			//$this->registry->get('log')->write($action->getFile());
			require_once($action->getFile());
			
			$class = $action->getClass();
			//$this->registry->get('log')->write(print_r($class,true));
			$controller = new $class($this->registry);
			//$this->registry->get('log')->write(print_r($controller,true));
			if (is_callable(array($controller, $action->getMethod()))) {
				$action = call_user_func_array(array($controller, $action->getMethod()), $action->getArgs());
			} else {
				$action = $this->error;
			
				$this->error = '';
			}
		} else {
			$action = $this->error;
			
			$this->error = '';
		}
		
		return $action;
	}
}
?>