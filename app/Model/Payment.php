<?php

App::uses('AppModel', 'Model');

class Payment extends AppModel {
	public $columnPrefix ="pay_";
	public $actsAs = array('ToString');
	public $toString = array('id', 'pay_transaction_id');
	
	public $belongsTo = array(
		'PayType' => array(
			'className' => 'PaymentType',
			'foreignKey' => 'pay_type_id'
		),
		'PayCurrency' => array(
			'className' => 'Currency',
			'foreignKey' => 'pay_currency_id'
		)
	);
}
?>