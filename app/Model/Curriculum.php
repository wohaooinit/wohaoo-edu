<?php

App::uses('AppModel', 'Model');

class Curriculum extends AppModel {
	public $columnPrefix ="cur_";
	public $actsAs = array('ToString');
	public $toString = 'cur_name';
	
	public $belongsTo = array(
		'CurLang' => array(
			'className' => 'Lang',
			'foreignKey' => 'cur_lang_id',
			'dependent' => false
		),
		'CurFeesCurrency' => array(
			'className' => 'Currency',
			'foreignKey' => 'cur_enroll_fees_cur_id',
			'dependent' => false
		)
	);
}
?>