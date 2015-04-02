<?php

App::uses('AppModel', 'Model');

class Document extends AppModel {
	public $useTable = "files";
	public $columnPrefix ="fil_";
	public $actsAs = array('ToString');
	public $toString = 'fil_name';
}
?>