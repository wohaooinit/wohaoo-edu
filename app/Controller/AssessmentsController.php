<?php

	App::uses('EduController', 'Controller');
	
	class AssessmentsController extends EduController {
		public function beforeFilter(){
			parent::beforeFilter();
			$this->Auth->allow('map', 'autocomplete');
		}
		
		public function autocomplete($use_codes = false, $displayField = "", $query = false, $field = "id", $value = ""){
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
				$displayField  = 'ass_code';
				
			$this->log("<assessments.autocomplete use-codes=${use_codes} displayField=$displayField query=$query field=$field value=$value>", 'debug');
			
			$conditions = array("LOWER(\"Assessment\".\"$displayField\") LIKE " => '%' . strtolower($query) . '%');
			if($value){
				$conditions[] = "\"Assessment\".\"$field\"=$value" ;
			}
			$params = array_merge($this->request->params['named']);
			foreach($this->request->query as $key => $value) $params[$key] = $value;
			foreach($params as $param => $value) {
				$columnType = $this->Assessment->getColumnType($param);
				if(!empty($columnType)) {
					$conditions["\"Assessment\".\"$param\""] = $value;
				}
			}
			//$conditions = array();
			$find_options = array(
				"conditions" => $conditions
				);
			
			//set locale
			//NOTE: this is no more necessary because it is handled by AppModel::find
			$this->Assessment->locale = Configure::read('Config.language');
			
			$this->log("find_options:=" . var_export($find_options, true), 'debug');
		
			$assessments = $this->Assessment->find('all', $find_options);
			$assessment_list = array();
			foreach($assessments as $assessment){
				if($use_codes)
					$assessment_list[$assessment['Assessment']['ass_code']] = $assessment['Assessment'][$displayField ];
				else
					$assessment_list[$assessment['Assessment']['id']] = $assessment['Assessment'][$displayField ];
			}
			//sort assessments alphabetically by display name
			asort($assessment_list);//preserve index association
			$this->log("assessment_list:=" . var_export($assessment_list, true), 'debug');
			
			if(!empty($this->request->params["requested"])){
				$this->log("</assessments.autocomplete>", 'debug');
				return $assessment_list;
			}
			//$this->viewClass = 'Json';
			$this->set('assessment_list', $assessment_list);
			$this->set('_serialize', array('assessment_list'));
			$this->log("</assessments.autocomplete>", 'debug');
		}
	}
?>