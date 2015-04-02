<?php

App::uses('AppModel', 'Model');

/**
 * Person.
 *
 * @package       app.Model
 */
class Person extends AppModel {
	public $columnPrefix ="per_";
	public $actsAs = array('ToString');
	public $toString = 'per_first_name';
	public $sequence = 'tbl_persons_id_seq';
	
	public $belongsTo = array(
		'PerCountry' => array(
			'className' => 'Country',
			'foreignKey' => 'per_country_id'
		),
		'PerRegion' => array(
			'className' => 'Region',
			'foreignKey' => 'per_region_id'
		),
		'PerUser' => array(
			'className' => 'User',
			'foreignKey' => 'per_user_id'
		)
	);
	
	public function __construct(){
		parent::__construct();
		$this->validate = array(
			'per_first_name' => array(
				'notempty' => array(
					'rule' => array('notempty'),
					'message' => __('Person first name cannot be empty'),
					'allowEmpty' => false,
					'required' => true
				)
			),
			'per_last_name' => array(
				'notempty' => array(
					'rule' => array('notempty'),
					'message' => __('Person last name cannot be empty'),
					'allowEmpty' => false,
					'required' => true
				)
			)
		);
	}
	
	public $validate = false;
}
?>