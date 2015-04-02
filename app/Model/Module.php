<?php

App::uses('AppModel', 'Model');

class Module extends AppModel {
	public $columnPrefix ="mod_";
	public $actsAs = array('ToString');
	public $toString = 'mod_name';
	
	public $belongsTo = array(
		'ModCurriculum' => array(
			'className' => 'Curriculum',
			'foreignKey' => 'mod_curriculum_id',
			'counterCache' => 'cur_module_count'
		),
		'PrevModule' => array(
			'className' => 'Module',
			'foreignKey' => 'mod_prev_module_id'
		),
		'NextModule' => array(
			'className' => 'Module',
			'foreignKey' => 'mod_next_module_id'
		)
	);
	
	public function __construct(){
		parent::__construct();
	}
}
?>