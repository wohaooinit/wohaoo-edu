<?php 
class LanguageComponent extends Component {
	public $controller = null;

	public function initialize($controller, $settings = array()) {
		$this->controller = $controller;
	}

	public function translate($text = ""){
		return $text;
	}

}
?>