<?php

App::uses('AppModel', 'Model');

/**
 *Attribute
 *
 * @package       app.Model
 */
class  Attribute extends AppModel {
	public $columnPrefix ="att_";
	public $actsAs = array('ToString');
	public $toString = 'att_property_name';
	
	public $fields = array("Attribute.att_property_name", "Attribute.id", "Attribute.att_content", 
						"Attribute.att_serie_num", "Attribute.att_created",  "Attribute.att_class_name",
						"Definition.atd_display_name", "Definition.atd_is_date", "Definition.atd_max_revisions",
						"Definition.atd_is_image", "Definition.atd_is_data", "Definition.atd_data_model",
						"Definition.atd_data_key", "Definition.atd_is_password", "Definition.atd_is_email",
						"Definition.atd_index", "Definition.atd_is_editable", "Definition.atd_is_number",
						"Definition.atd_is_currency");
	
	public $joins = array(
			array(
				'table' => 'attribute_definitions',
				'alias' => 'Definition',
				'type' => 'inner',
				'conditions' => array('Definition.atd_property_name = Attribute.att_property_name')
			)
		);
}
?>