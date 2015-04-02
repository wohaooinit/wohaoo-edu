<?php

App::uses('AppModel', 'Model');

class Assessment extends AppModel {
	public $columnPrefix ="ass_";
	public $actsAs = array('ToString');
	public $toString = 'ass_code';
	
	public $belongsTo = array(
		'AssModule' => array(
			'className' => 'Module',
			'foreignKey' => 'ass_module_id',
			'counterCache' => 'mod_assessment_count'
		)
	);
	  
	public function __construct(){
		parent::__construct();
		$this->virtualFields['ass_option_count'] = sprintf(
			'SELECT COUNT(*) FROM "tbl_options"  WHERE opt_assessment_id=%s.id', $this->alias
		);
	}
}

?>