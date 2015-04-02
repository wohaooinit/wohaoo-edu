<?php

App::uses('AppModel', 'Model');

class Country extends AppModel {
	public $columnPrefix ="con_";
	public $actsAs = array('ToString');
	public $toString = 'con_display_name';
}
?>