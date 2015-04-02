<?php

App::uses('AppModel', 'Model');

class Option extends AppModel {
	public $columnPrefix ="opt_";
	public $actsAs = array('ToString');
	public $toString = 'opt_code';
}
?>