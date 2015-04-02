<?php 
class ConfigComponent extends Component {
	public $controller = null;

	public function initialize($controller, $settings = array()) {
		$this->controller = $controller;
	}

	public function read($name = "", $default = ""){
		$this->controller->loadModel('Config');
		$value = $this->controller->Config->read($name);
		if(!$value) return $default;
		return $value;
	}

}
?>