<?php 
class ConfigComponent extends Component {
	public $controller = null;

	public function initialize(Controller $controller) {
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