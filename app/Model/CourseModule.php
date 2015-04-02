<?php

App::uses('AppModel', 'Model');

class CourseModule extends AppModel {
	public $columnPrefix ="com_";
	public $actsAs = array('ToString');
	public $toString = 'id';
}
?>