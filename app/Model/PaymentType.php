<?php

App::uses('AppModel', 'Model');

class PaymentType extends AppModel {
	public $columnPrefix ="pat_";
	public $actsAs = array('ToString');
	public $toString = 'pat_display_name';
}
?>