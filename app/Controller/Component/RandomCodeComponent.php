<?php

App::uses('Component', 'Controller');

class RandomCodeComponent extends Component
{
	/**
	 * Default values to be merged with settings
	 *
	 * @var array
	 */
	private $__defaults = array(
		'characters' => 6
	);
	
	/**
	 * Settings for this Component
	 *
	 * @var array
	 */
	public $settings = array();
	
	/**
	 * Constructor
	 *
	 * @param ComponentCollection $collection A ComponentCollection this component can use to lazy load its components
	 * @param array $settings Array of configuration settings.
	 */
	public function __construct(ComponentCollection $collection, $settings = array())
	{
		parent::__construct($collection, array_merge($this->__defaults, $settings));
	}
	/**
	 * Generate random alphanumeric code to specified character length
	 *
	 * @access public
	 * @return string The generated code
	 */
	public function getRandomCode($size = false)
	{
		if(!$size)
			$size = $this->settings['characters'];
		$valid = 'abcdefghijklmnpqrstuvwxyz123456789';
		return substr(str_shuffle($valid), 0, $this->settings['characters']);
	 }
}

?>