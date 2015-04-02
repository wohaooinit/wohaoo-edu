<?php
	class RegionsController extends Controller {
		public function autocomplete($use_codes = false, $query = false){
			if(isset($this->request->query['use_codes']) )
				$use_codes = $this->request->query['use_codes'];
			if(isset($this->request->query['query']) )
				$query = $this->request->query['query'];
			if(!$query)
				$query = "";
			$this->log("<regions.autocomplete use-codes=${use_codes}>", 'debug');
			
			$region_list = array();
			
			$conditions = array();
			
			$reg_country = "";
			try{
				$conditions = array('LOWER(Region.reg_display_name) LIKE ' => '%' . strtolower($query) . '%');
				
				$find_options = array(
					"conditions" => $conditions
					);
				
				//set locale
				//NOTE: this is no more necessary because it is handled by AppModel::find
				$this->Region->locale = Configure::read('Config.language');
		
				$regions = $this->Region->find('all', $find_options);
			
				foreach($regions as $region){
					if($use_codes)
						$region_list[$region['Region']['reg_code']] = $region['Region']['reg_display_name'];
					else
						$region_list[$region['Region']['id']] = $region['Region']['reg_display_name'];
				}
				//sort regions alphabetically by display name
				asort($region_list);//preserve index association
				if(!empty($this->request->params["requested"])){
					$this->log("</regions.autocomplete>", 'debug');
					return $region_list;
				}
			}catch(Exception $e){
			}
			//$this->viewClass = 'Json';
			$this->set('region_list', $region_list);
			$this->set('_serialize', array('region_list'));
			$this->log("</regions.autocomplete>", 'debug');
		}
	}
?>