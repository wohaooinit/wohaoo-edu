<?php

App::uses('AppModel', 'Model');

/**
 *Translation
 *
 * @package       app.Model
 */
class Translation extends AppModel {
	public $columnPrefix ="t9n_";
	public $actsAs = array('ToString');
	public $toString = 't9n_orig_text';
	
	public $belongsTo = array(
		'T9nPerson' => array(
			'className' => 'Person',
			'foreignKey' => 't9n_trans_person_id'
		)
	);
}
?>