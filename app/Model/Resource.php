<?php

App::uses('AppModel', 'Model');

class Resource extends AppModel {
	public $columnPrefix ="res_";
	public $actsAs = array('ToString');
	public $toString = 'res_model';
}
?>