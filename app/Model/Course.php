<?php

App::uses('AppModel', 'Model');

class Course extends AppModel {
	public $columnPrefix ="cou_";
	public $actsAs = array('ToString');
	public $toString = 'id';
	
	public $belongsTo = array(
		'CouCurriculum' => array(
			'className' => 'Curriculum',
			'foreignKey' => 'cou_curriculum_id'
		),
		'CouPayment' => array(
			'className' => 'Payment',
			'foreignKey' => 'cou_payment_id'
		)
	);
	
	public function __construct(){
		parent::__construct();
		$this->virtualFields['cou_module_count'] = sprintf(
			'SELECT COUNT(*) FROM "tbl_course_modules"  WHERE com_approved > 0 AND com_course_id=%s.id', 
				$this->alias
		);
	}
}
?>