<?php

App::uses('AppModel', 'Model');

class Student extends AppModel {
	public $columnPrefix ="stu_";
	public $actsAs = array('ToString');
	public $toString = 'stu_code';
	
	public $belongsTo = array(
		'StuPerson' => array(
			'className' => 'Person',
			'foreignKey' => 'stu_person_id'
		)
	);
}
?>