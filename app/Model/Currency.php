<?php

App::uses('AppModel', 'Model');

/**
 *Currency
 *
 * @package       app.Model
 */
class Currency extends AppModel {
	public $columnPrefix ="cur_";
	public $actsAs = array('ToString');
	public $toString = 'cur_display_name';
}
?>