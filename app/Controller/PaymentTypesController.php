<?php

App::uses('EduController', 'Controller');
/**
 * PaymentTypesController
 *
 * @property PaymentType $PaymentType
 */
class PaymentTypesController extends EduController {
	public function autocomplete($use_codes = false, $displayField = "", $query = false, $field = "id", $value = ""){
			if(isset($this->request->query['use_codes']) )
				$use_codes = $this->request->query['use_codes'];
			if(isset($this->request->query['query']) )
				$query = $this->request->query['query'];
			if(isset($this->request->query['field']) )
				$field = $this->request->query['field'];
			if(isset($this->request->query['value']) )
				$value = $this->request->query['value'];
			if(isset($this->request->query['viewClass']) )
				$this->viewClass = $this->request->query['viewClass'];
			if(!$query)
				$query = "";
			if(!$displayField )
				$displayField  = 'pat_display_name';
			$this->log("<PaymentTypes.autocomplete use-codes=${use_codes} displayField=$displayField query=$query field=$field value=$value>", 'debug');
			
			$conditions = array("LOWER(\"PaymentType\".\"$displayField\") LIKE " => '%' . strtolower($query) . '%');
			if($value){
				$conditions[] = "\"PaymentType\".\"$field\"=$value" ;
			}
			$params = array_merge($this->request->params['named']);
			foreach($this->request->query as $key => $value) $params[$key] = $value;
			foreach($params as $param => $value) {
				$columnType = $this->PaymentType->getColumnType($param);
				if(!empty($columnType)) {
					$conditions["\"PaymentType\".\"$param\""] = $value;
				}
			}
			//$conditions = array();
			$find_options = array(
				"conditions" => $conditions
				);
			
			//set locale
			//NOTE: this is no more necessary because it is handled by AppModel::find
			$this->PaymentType->locale = Configure::read('Config.PaymentTypeuage');
			
			$this->log("find_options:=" . var_export($find_options, true), 'debug');
		
			$PaymentTypes = $this->PaymentType->find('all', $find_options);
			$PaymentType_list = array();
			foreach($PaymentTypes as $PaymentType){
				if($use_codes)
					$PaymentType_list[$PaymentType['PaymentType']['pat_code']] = $PaymentType['PaymentType'][$displayField ];
				else
					$PaymentType_list[$PaymentType['PaymentType']['id']] = $PaymentType['PaymentType'][$displayField ];
			}
			//sort PaymentTypes alphabetically by display name
			asort($PaymentType_list);//preserve index association
			$this->log("PaymentType_list:=" . var_export($PaymentType_list, true), 'debug');
			
			if(!empty($this->request->params["requested"])){
				$this->log("</PaymentTypes.autocomplete>", 'debug');
				return $PaymentType_list;
			}
			//$this->viewClass = 'Json';
			if($this->viewClass == 'Json'){
				$this->log('Json request ...', 'debug');
				$items = array();
				foreach($PaymentType_list as $code => $PaymentType){
					$item = array();
					$item["model"] = "PaymentTypeuage";
					$item["id"] = $code;
					$item["code"] = $code;
					$item["name"] = $PaymentType;
					$items[] = $item;
				}
				$identifier = "id";
				$idAttribute = "id";
				$label = 'name';
				$this->set(compact("identifier", "idAttribute", "label", "items"));
				$this->set("_serialize", array("identifier", "idAttribute", "label", "items"));
			}else{
				$this->log('Non-Json request ...', 'debug');
				$this->set('PaymentType_list', $PaymentType_list);
				$this->set('_serialize', array('PaymentType_list'));
			}
			$this->log("</PaymentTypes.autocomplete>", 'debug');
		}
}
?>