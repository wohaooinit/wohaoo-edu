<?php

App::uses('EduController', 'Controller');
/**
 * LangsController
 *
 * @property Lang $Lang
 */
class LangsController extends EduController {
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
				$displayField  = 'lan_display_name';
			$this->log("<langs.autocomplete use-codes=${use_codes} displayField=$displayField query=$query field=$field value=$value>", 'debug');
			
			$conditions = array("LOWER(\"Lang\".\"$displayField\") LIKE " => '%' . strtolower($query) . '%');
			if($value){
				$conditions[] = "\"Lang\".\"$field\"=$value" ;
			}
			$params = array_merge($this->request->params['named']);
			foreach($this->request->query as $key => $value) $params[$key] = $value;
			foreach($params as $param => $value) {
				$columnType = $this->Lang->getColumnType($param);
				if(!empty($columnType)) {
					$conditions["\"Lang\".\"$param\""] = $value;
				}
			}
			//$conditions = array();
			$find_options = array(
				"conditions" => $conditions
				);
			
			//set locale
			//NOTE: this is no more necessary because it is handled by AppModel::find
			$this->Lang->locale = Configure::read('Config.language');
			
			$this->log("find_options:=" . var_export($find_options, true), 'debug');
		
			$langs = $this->Lang->find('all', $find_options);
			$lang_list = array();
			foreach($langs as $lang){
				if($use_codes)
					$lang_list[$lang['Lang']['lan_code']] = $lang['Lang'][$displayField ];
				else
					$lang_list[$lang['Lang']['id']] = $lang['Lang'][$displayField ];
			}
			//sort langs alphabetically by display name
			asort($lang_list);//preserve index association
			$this->log("lang_list:=" . var_export($lang_list, true), 'debug');
			
			if(!empty($this->request->params["requested"])){
				$this->log("</langs.autocomplete>", 'debug');
				return $lang_list;
			}
			//$this->viewClass = 'Json';
			if($this->viewClass == 'Json'){
				$this->log('Json request ...', 'debug');
				$items = array();
				foreach($lang_list as $code => $lang){
					$item = array();
					$item["model"] = "language";
					$item["id"] = $code;
					$item["code"] = $code;
					$item["name"] = $lang;
					$items[] = $item;
				}
				$identifier = "id";
				$idAttribute = "id";
				$label = 'name';
				$this->set(compact("identifier", "idAttribute", "label", "items"));
				$this->set("_serialize", array("identifier", "idAttribute", "label", "items"));
			}else{
				$this->log('Non-Json request ...', 'debug');
				$this->set('lang_list', $lang_list);
				$this->set('_serialize', array('lang_list'));
			}
			$this->log("</langs.autocomplete>", 'debug');
		}
}
?>