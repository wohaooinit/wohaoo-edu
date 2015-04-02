<?php
App::uses('AppModel', 'Model');

class Exam extends AppModel {
	public $columnPrefix ="exa_";
	public $actsAs = array('ToString');
	public $toString = 'id';
	
	public $belongsTo = array(
		'ExaCourseModule' => array(
			'className' => 'CourseModule',
			'foreignKey' => 'exa_course_module_id'
		)
	);
	
	public function __construct(){
		parent::__construct();
		$this->virtualFields['exa_answer_count'] = sprintf(
			'SELECT COUNT(*) FROM "tbl_exam_questions"  WHERE exq_exam_id=%s.id', 
				$this->alias
		);
		$this->virtualFields['exa_good_answer_count'] = sprintf(
			'SELECT COUNT(*) FROM "tbl_exam_questions"  WHERE exq_is_approved > 0  AND exq_exam_id=%s.id', 
				$this->alias
		);
	}
}
?>