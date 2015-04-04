<?php

/**
 * Application model for Cake.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {
	public static $DEFAULT_ERROR_MESSAGE = '';
	public static $CONFIG_LOAD_ERROR = '';
	public static $UNDEFINED_CONFIG_ERROR = '';
	public $QUERY_KEYS = array('page' => 1, 'conditions' => 1, 'fields' => 1, 'order' => 1
									, 'group' => 1, 'joins' => 1, 'limit' => 1, 'callbacks' => 1, 
									'offset' => 1, 'recursive' => 1, 'maxLimit' => 1, 'contain' => 1,
									'paramType' => 1);
									
	public $columnPrefix ="";
	
	public function __construct(){
		parent::__construct();
		self::$DEFAULT_ERROR_MESSAGE = __("Internal Error: contact your administrator");
		self::$CONFIG_LOAD_ERROR = __('Unable to load configurations');
		self::$UNDEFINED_CONFIG_ERROR = __('Undefined configuration');
	}
	
	/**
	 *Overrides the Model find method, in order to inject
	 *language settings (locale) and the virtual deletion flag (is_deleted).
	 *This method also ensures that the query options array is valid.
	 *@param $type the  type of the query,
	 *@param $query, the find options
	 */
	public function find($type = 'first', $query = array()) {
		//check $query keys
		$qkeys = array_keys($query);
		foreach($qkeys as $qkey){
			if(!isset($this->QUERY_KEYS[$qkey]))
				throw new Exception(sprintf("Invalid find options key: %s", $qkey));
		}
		
		//locale processing
		$supportedLanguages = Configure::read('Config.languages');
		if(!$supportedLanguages || !$this->locale || !$supportedLanguages[$this->locale]){
			$this->locale = Configure::read('Config.language');
		}
		//add is_deleted => 0 condition to every request
		if(isset($query['conditions']))
			array_push($query['conditions'], $this->alias . '.' . $this->columnPrefix . 'is_deleted = 0');
		else
			$query['conditions'] = array($this->alias . '.' . $this->columnPrefix . 'is_deleted = 0');
		return parent::find($type, $query);
	}
	
	/** 
	 * Makes a subquery
	 * example: $this->subquery('count', array('conditions' => array('UserBigAliasHere.parent_id = User.id')), 'UserBigAliasHere') 
	 * @param strin|array $type The type o the query (only available 'count') or the $options 
	 * @param string|array $options The options array or $alias in case of $type be a array 
	 * @param string $alias You can use this intead of $options['alias'] if you want 
	 */ 
	public function subquery($type, $options = null, $alias = null){ 
		$fields = array(); 
		if(is_string($type)){ 
			$isString = true; 
		}else{ 
			$alias = $options; 
			$options = $type; 
		} 
	 
		if($alias === null){ 
			$alias = $this->alias . '2'; 
		} 
	 
		if(isset($isString)){ 
			switch ($type){ 
				case 'count': 
					$fields = array('COUNT(*)'); 
					break; 
				default: 
					$fields = array('id'); 
					break; 
			} 
		} 
	 
		$dbo = $this->getDataSource(); 
			 
		$default = array( 
			'fields' => $fields, 
			'table' => $dbo->fullTableName($this), 
			'alias' => $alias, 
			'limit' => null, 
			'offset' => null, 
			'joins' => array(), 
			'conditions' => array(), 
			'order' => null, 
			'group' => null 
		); 
	 
		$params = array_merge($default, $options); 
		$subQuery = $dbo->buildStatement($params, $this); 
	 
		return $subQuery; 
	}
	
	protected function __extractSafeData($source = array()){
	}
	
	public function getLastQuery()
	{
		$dbo = $this->getDatasource();
		$logs = $dbo->getLog();
		$lastLog = end($logs['log']);
		return $lastLog['query'];
	}
}
