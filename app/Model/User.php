<?php

App::uses('AppModel', 'Model');

/**
 * User.
 *
 * @package       app.Model
 */
class User extends AppModel {
	public $columnPrefix ="usr_";
	public $actsAs = array('ToString');
	public $toString = 'usr_email';
	
	public $hasOne = array(
		'UsrPerson' => array(
			'className' => 'Person',
			'foreignKey' => 'per_user_id',
			'dependent' => true
		)
	);
	
	public function __construct(){
		parent::__construct();
		$this->validate = array(
			'usr_password' => array(
				'notempty' => array(
					'rule' => array('notempty'),
					'message' => __('User password cannot be empty'),
					'allowEmpty' => false,
					'required' => true
				)
			),
			'usr_type' => array(
				'notempty' => array(
					'rule' => array('notempty'),
					'message' => __('User type cannot be empty'),
					'allowEmpty' => false,
					'required' => true
				)
			)
		);
	}
	
	public $validate = false;
}
?>