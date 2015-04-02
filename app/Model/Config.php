<?php
App::uses('AppModel', 'Model');

class Config extends AppModel {
	public $columnPrefix ="cfg_";
	public function read($entry = ""){
		$config = array();
		if(!isset($this->data) || !isset($this->data['Config'])){ 
			$this->data = $this->find('first', array());
		}
		$config = $this->data['Config'];
		if(isset($config[$entry]))
			return $config[$entry];
		return "";
	}
}
?>