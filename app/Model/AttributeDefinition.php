<?php

App::uses('AppModel', 'Model');

/**
 *AttributeDefinition
 *
 * @package       app.Model
 */
class  AttributeDefinition extends AppModel {
	public $columnPrefix ="atd_";
	public $actsAs = array('ToString');
	public $toString = 'atd_display_name';
}
?>