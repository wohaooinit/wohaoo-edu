<?php

App::uses('AppHelper', 'Helper');

/**
 * Formatting helper
 *
 * @package       app.View.Helper
 */
class FormatHelper extends AppHelper {
	private $templates = array(
		'video' => '<video width=${width} height=${height} controls=controls allowfullscreen><source src="${mp4}" type="video/mp4"><source src="${ogv}" type="video/ogg"><source src="${webm}" type="video/webm"><p></p></video>',
		'audio' => '<audio><source src=\"${mp3}\" type=\"audio/mpeg\"><source src=\"${ogg}\" type=\"audio/ogg\"><source src=\"${wav}\" type=\"audio/wav\"><p></p></audio>',
		'embed' => '',
		'document' => '<iframe id=\"viewer\" src=\"/ViewerJS/#${pdf}\" width=\"${width}\" height=\"${height}\" allowfullscreen=\"\" webkitallowfullscreen=\"\"></iframe>'
	);
	
	private $default_options  = array('mp4' => '', 'ogv' => '', 'webm' => '', 'embed' => '', 
				'mp3' => '', 'ogg' => '', 'pdf' => '', 'wav' => '', 'width'=> 320, 'height' => 240);
				
	public function text($txt = "", $maxLength = 100){
		if(strlen($txt) >= ($maxLength - 4)){
			$txt = substr($txt, 0, $maxLength - 4);
			$txt .= " ...";
		}
		return $txt;
	}
	public function date($datetime){
		return date("F j, Y", $datetime);
	}
	
	public function datetime($datetime){
		return date("F j, Y \a\\t g:i a", $datetime);
	}
	
	public function url($model, $action, $model_id, $plugin = false){
		$controller = Inflector::underscore($model);
		$controller = Inflector::pluralize($controller);
		
		if($plugin)
			$controller = $plugin . "_" . $controller;
		return array('plugin' => $plugin, 'controller' => $controller, 'action' => $action, $model_id);
	}
	
	public function image($image_id, $width=50, $height=50, $options = array()){
		$attrs = "";
		foreach($options as $name => $value)
			$attrs .= $name . '="' . $value . '" ';
		if($image_id)
			return sprintf("<img src='/images/image?id=%d&width=%d&height=%d' width='%d' height='%d' %s>", 
					$image_id, $width, $height, $width, $height, $attrs);
		return sprintf("<img src='/img/pixel.gif' width='%d' height='%d'>", $image_id, $width, $height);
	}
	
	public function video($options = array()){
		$options = array_merge($this->default_options, $options);
		
		extract($options);
		if($embed){
			return $embed;
		}
		$php = $this->templates['video'];
		$html = "";
		eval("\$html = \"$php\";"); 
		
		return $html;
	}
	
	public function audio($options = array()){
		$options = array_merge($this->default_options, $options);
		
		extract($options);
		if($embed){
			return $embed;
		}
		$php = $this->templates['audio'];
		$html = "";
		eval("\$html = \"$php\";"); 
		
		return $html;
	}
	
	public function document($options = array()){
		$options = array_merge($this->default_options, $options);
		
		extract($options);
		if($embed){
			return $embed;
		}
		$php = $this->templates['document'];
		$html = "";
		eval("\$html = \"$php\";"); 
		
		return $html;
	}
	
	public function password($value, $replace = '*'){
		$length = strlen($value);
		$ret = '';
		for($i = 0; $i < $length; $i++){
			$ret .= $replace;
		}
		return $ret;
	}
	
	public function data($model, $field = 'id', $value, $conditions = false, $displayField = ''){
		$controller = Inflector::underscore($model);
		$controller = Inflector::pluralize($controller);
		$action = "autocomplete";
		$use_codes = 0;
		if($field !== 'id')
			$use_codes = 1;
		
		$map = $this->requestAction(
			array("controller" => $controller, "action" => $action, "plugin" => false), 
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
	}
	
	public function username($user_id = 0){
		$this->log("<username>", 'debug');
		
		$username = "";
		try{
			$user = $this->_View->requestAction("/users/user/${user_id}");
			if(!$user){
				$username = "";
				throw new Exception(__("User is unknown"));
			}
			if($user['usr_username']){
				$username = $user['usr_username'];
				throw new Exception(__("User is already defined"));
			}
			$short_last_name = $this->__firstLettersOnly($user['usr_last_name']);
			$username = $user['usr_first_name'] . ' ' .  $short_last_name;
			
		}catch(Exception $e){
		}
		$this->log("</username username=$username>", 'debug');
		return $username;
	}
	
	public function name($user_id = 0){
		$this->log("<name>", 'debug');
		
		$name = "";
		try{
			$user = $this->_View->requestAction("/users/user/${user_id}");
			if(!$user){
				$name = "";
				throw new Exception(__("User is unknown"));
			}
			$name = $user['per_prefix'] . ' ' . $user['per_first_name'] . ' ' .  $user['per_last_name']  . ', ' . $user['per_suffix'] ; 
			
		}catch(Exception $e){
			$this->log($e->getMessage());
		}
		$this->log("</name name=$name>", 'debug');
		return $name;
	}
	
	private function __firstLettersOnly($name = ""){
		$tokens = explode(" ", $name);
		$ret = "";
		foreach($tokens as $token){
			$letter = substr($token, 0, 1);
			$ret .= $letter . ".";
		}
		return $ret;
	}
	
	 protected function _formatPhp($phpString){
		$phpString = preg_replace("/\<\?php|\?\>/i", '', $phpString);
		$phpString = preg_replace('/\$/', '\$', $phpString);
		return $phpString;
	}
}

?>
