<?php
App::uses('EduController', 'Controller');
/**
 * CurrenciesController
 *
 * @property Currency $Currency
 */
class CurrenciesController extends EduController {
	public function beforeFilter(){
		parent::beforeFilter();
		$this->Auth->allow('autocomplete');
	}
	public function autocomplete($use_codes = false, $displayField = "", $query = false){
			if(isset($this->request->query['use_codes']) )
				$use_codes = $this->request->query['use_codes'];
			if(isset($this->request->query['query']) )
				$query = $this->request->query['query'];
			if(!$query){
				$query = '';
			}
			if(!$displayField)
				$displayField = "cur_display_name";
			$this->log("<currencies.autocomplete use-codes=${use_codes}>", 'debug');
			$conditions = array("LOWER(\"Currency\".\"cur_display_name\") LIKE" => "%" . strtolower($query) . "%");
			//$conditions = array();
			$find_options = array(
				"conditions" => $conditions,
				"limit" => 1
				);
			
			//set locale
			//NOTE: this is no more necessary because it is handled by AppModel::find
			$this->Currency->locale = Configure::read('Config.language');
		
			$currencies = $this->Currency->find('all', $find_options);
			$currency_list = array();
			foreach($currencies as $currency){
				if($use_codes)
					$currency_list[$currency['Currency']['cur_code']] = $currency['Currency'][$displayField];
				else
					$currency_list[$currency['Currency']['id']] = $currency['Currency'][$displayField];
			}
			//sort currencies alphabetically by display name
			asort($currency_list);//preserve index association
			if(!empty($this->request->params["requested"])){
				$this->log("</currencies.autocomplete>", 'debug');
				return $currency_list;
			}
			//$this->viewClass = 'Json';
			$this->set('currency_list', $currency_list);
			$this->set('_serialize', array('currency_list'));
			$this->log("</currencies.autocomplete>", 'debug');
		}
}
?>