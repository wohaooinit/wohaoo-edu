<?php
/**
 * TranslationFixture
 *
 */
class TranslationFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'locale' => array('type' => 'string', 'null' => false, 'length' => 6),
		'model' => array('type' => 'string', 'null' => false),
		'foreign_key' => array('type' => 'integer', 'null' => false),
		'field' => array('type' => 'string', 'null' => false),
		'contentid' => array('type' => 'text', 'null' => false, 'length' => 1073741824),
		'content' => array('type' => 'text', 'null' => true, 'length' => 1073741824),
		'created' => array('type' => 'integer', 'null' => true),
		'modified' => array('type' => 'integer', 'null' => true),
		'is_deleted' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'tbl_translations_locale_model_field_foreign_key_key' => array('unique' => true, 'column' => array('locale', 'model', 'field', 'foreign_key'))
		),
		'tableParameters' => array()
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'locale' => 'Lore',
			'model' => 'Lorem ipsum dolor sit amet',
			'foreign_key' => 1,
			'field' => 'Lorem ipsum dolor sit amet',
			'contentid' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'content' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'created' => 1,
			'modified' => 1,
			'is_deleted' => 1
		),
	);

}
