<?php
App::uses('AppModel', 'Model');

class ExamQuestion extends AppModel {
	public $columnPrefix ="exq_";
	public $actsAs = array('ToString');
	public $toString = 'id';
	
	public function __construct(){
		parent::__construct();
	}
}
?>