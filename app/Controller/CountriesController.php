<?php

	App::uses('EduController', 'Controller');
	
	class CountriesController extends EduController {
		public function beforeFilter(){
			parent::beforeFilter();
			$this->Auth->allow('map', 'autocomplete');
		}
		public function map($use_codes = false){
			$this->log("<countries.map>", 'debug');
			//set locale
			//NOTE: this is no more necessary because it is handled by AppModel::find
			$this->Country->locale = Configure::read('Config.language');
			
			$countries = $this->Country->find('all');
			$map = array();
			foreach($countries as $country){
				if($use_codes)
					$map[$country['Country']['con_code']] = $country['Country']['con_display_name'];
				else
					$map[$country['Country']['id']] = $country['Country']['con_display_name'];
			}
			//sort countries alphabetically by display name
			asort($map );//preserve index association
			if(!empty($this->request->params["requested"])){
				$this->log("</countries.map>", 'debug');
				return $map;
			}
			$this->log("</countries.map>", 'debug');
		}
		
		public function autocomplete($use_codes = false, $displayField = "", $query = false, $field = "id", $value = ""){
			$this->log("<countries.autocomplete use-codes=${use_codes} displayField=$displayField query=$query field=$field value=$value> ", 'debug');
			if(isset($this->request->query['use_codes']) )
				$use_codes = $this->request->query['use_codes'];
			if(isset($this->request->query['query']) )
				$query = $this->request->query['query'];
			if(isset($this->request->query['field']) )
				$field = $this->request->query['field'];
			if(isset($this->request->query['value']) )
				$value = $this->request->query['value'];
			if(!$query)
				$query = "";
			if(!$displayField )
				$displayField  = 'con_display_name';
			$conditions = array("LOWER(\"Country\".\"$displayField\") LIKE " => '%' . strtolower($query) . '%');
			if($value){
				$conditions[] = "\"Country\".\"$field\"=$value" ;
			}
			$params = array_merge($this->request->params['named']);
			foreach($this->request->query as $key => $value) $params[$key] = $value;
			foreach($params as $param => $value) {
				$columnType = $this->Country->getColumnType($param);
				if(!empty($columnType)) {
					$conditions["\"Country\".\"$param\""] = $value;
				}
			}
			//$conditions = array();
			$find_options = array(
				"conditions" => $conditions
				);
			
			//set locale
			//NOTE: this is no more necessary because it is handled by AppModel::find
			$this->Country->locale = Configure::read('Config.language');
			
			$this->log("find_options:=" . var_export($find_options, true), 'debug');
		
			$countries = $this->Country->find('all', $find_options);
			$country_list = array();
			foreach($countries as $country){
				if($use_codes)
					$country_list[$country['Country']['con_code']] = $country['Country'][$displayField ];
				else
					$country_list[$country['Country']['id']] = $country['Country'][$displayField ];
			}
			//sort countries alphabetically by display name
			asort($country_list);//preserve index association
			$this->log("country_list:=" . var_export($country_list, true), 'debug');
			
			if(!empty($this->request->params["requested"])){
				$this->log("</countries.autocomplete>", 'debug');
				return $country_list;
			}
			//$this->viewClass = 'Json';
			$this->set('country_list', $country_list);
			$this->set('_serialize', array('country_list'));
			$this->log("</countries.autocomplete>", 'debug');
		}
	}
?>