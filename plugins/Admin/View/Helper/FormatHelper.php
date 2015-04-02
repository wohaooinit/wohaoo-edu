<?php
App::uses('AppHelper', 'Helper');

/**
 * Formatting helper
 *
 * @package       app.View.Helper
 */
class FormatHelper extends AppHelper {
	public function date($datetime){
		return date("F j, Y", $datetime);
	}
	
	public function datetime($datetime){
		return date("F j, Y \a\\t g:i a", $datetime);
	}
	
	public function image($image_id, $width=50, $height=50){
		if($image_id)
			return sprintf("<img src='/images/image?id=%d&width=%d&height=%d'>", 
					$image_id, $width, $height);
		return sprintf("<img src='/img/pixel.gif' width='%d' height='%d'>", $image_id, $width, $height);
	}
	/*
	public function data($model, $field = 'id', $value, $conditions = false, $displayField = ''){
		$controller = Inflector::underscore($model);
		$controller = Inflector::pluralize($controller);
		$action = "autocomplete";
		$use_codes = 0;
		if($field !== 'id')
			$use_codes = 1;
		
		$map = $this->requestAction(
			array("controller" => $controller, "action" => $action), 
			array("pass" => array($use_codes, $displayField, "C", $field, $value))
			);
		
		if(!$map)
			return "";
		foreach($map as $key => $display){
			if($key === $value){
				return $display;
			}
		}
		return "";
	}*/
}

?>
