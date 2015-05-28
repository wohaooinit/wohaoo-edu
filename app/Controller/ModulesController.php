<?php

	App::uses('EduController', 'Controller');
	
	class ModulesController extends EduController {
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
				$displayField  = 'mod_name';
				
			$this->log("<modules.autocomplete use-codes=${use_codes} displayField=$displayField query=$query field=$field value=$value> ", 'debug');
			
			$conditions = array("LOWER(\"Module\".\"$displayField\") LIKE " => '%' . strtolower($query) . '%');
			if($value){
				$conditions[] = "\"Module\".\"$field\"=$value" ;
			}
			$params = array_merge($this->request->params['named']);
			foreach($this->request->query as $key => $value) $params[$key] = $value;
			foreach($params as $param => $value) {
				$columnType = $this->Module->getColumnType($param);
				if(!empty($columnType)) {
					$conditions["\"Module\".\"$param\""] = $value;
				}
			}
			//$conditions = array();
			$find_options = array(
				"conditions" => $conditions
				);
			
			//set locale
			//NOTE: this is no more necessary because it is handled by AppModel::find
			$this->Module->locale = Configure::read('Config.language');
			
			$this->log("find_options:=" . var_export($find_options, true), 'debug');
		
			$modules = $this->Module->find('all', $find_options);
			$module_list = array();
			foreach($modules as $module){
				if($use_codes)
					$module_list[$module['Module']['mod_code']] = $module['Module'][$displayField ];
				else
					$module_list[$module['Module']['id']] = $module['Module'][$displayField ];
			}
			//sort modules alphabetically by display name
			asort($module_list);//preserve index association
			$this->log("module_list:=" . var_export($module_list, true), 'debug');
			
			if(!empty($this->request->params["requested"])){
				$this->log("</modules.autocomplete>", 'debug');
				return $module_list;
			}
			//$this->viewClass = 'Json';
			$this->set('module_list', $module_list);
			$this->set('_serialize', array('module_list'));
			$this->log("</modules.autocomplete>", 'debug');
		}
		
		 public function mod_index($curriculum_id = 0, $query = "", $max_id = 0, $min_id = 0, $list = "") {
			$conditions = array();
			$joins = array();
			$group = array();
			$modulesTableURL = array('controller' => 'modules', 'action' => 'index');

			if(isset($this->request->query['query']) )
				$query = $this->request->query['query'];
			if(isset($this->request->query['q']) )
				$query = $this->request->query['q'];
			if(isset($this->request->query['l']) )
				$list = $this->request->query['l'];
			if(isset($this->request->query['max_id']) )
				$max_id = $this->request->query['max_id'];
			if(isset($this->request->query['min_id']) )
				$min_id = $this->request->query['min_id'];
			$this->log("query=$query", 'debug');
			
			if($query){
				$query = trim($query);
				$this->log("final query=/$query/", 'debug');
				$conditions["LOWER(\"Module\".\"mod_name\" ||" .
						     " \"Module\".\"mod_description\") LIKE"] = 
									"%" . strtolower($query) . "%";
			}
			
			if($curriculum_id){
				$conditions["\"Module\".\"mod_curriculum_id\""] = 
									$curriculum_id;
			}
			
			//join get query & named params
			$params = array_merge($this->request->params['named']);
			foreach($this->request->query as $key => $value) $params[$key] = $value;

			foreach($params as $key => $value) {
				$split = explode('-', $key);
				$modelName = (sizeof($split) > 1) ? $split[0] : 'Module';
				$property = (sizeof($split) > 1) ? $split[1] : $key;
				if($modelName == 'Module' || !empty($this->Module->belongsTo[$modelName])) {
					$this->loadModel($modelName);
					$modelObj = new $modelName();
					if(!empty($modelObj)) {
						$columnType = $modelObj->getColumnType($property);
						if(!empty($columnType)){
							//add it to url
							$modulesTableURL[$key] = $value;
							//add it to conditions
							switch($columnType)
							{
								case 'string':
									$conditions[$modelName . '.' . $property . ' LIKE'] = '%'.$value.'%';
									break;
								default:
									$conditions[$modelName . '.' . $property] = $value;
									break;
							}
						}
					}
				}

			}

			$find_options = array();
			$find_options['joins'] = $joins;
			$find_options['group'] = $group;
			$find_options['order'] = array('Module.mod_next_module_id' => 'ASC');
			
			//$this->log("conditions=" . var_export($conditions, true), 'debug');
			$this->Paginator->settings = $find_options;
		
			$this->Module->recursive = 0;
			$this->log("conditions=" . var_export($conditions, true), 'debug');
			$modules = $this->Paginator->paginate('Module', $conditions, array());
			//$this->log("modules=" . var_export($modules, true), 'debug');
			if(!empty($this->request->params['requested'])){
				return $modules;
			}
			$this->set('modules', $modules);
			$this->set('modulesTableURL', $modulesTableURL);
			//render as local table if it is an ajax request
			if($this->request->is('ajax'))
			{
				$this->render('table');
			}
		}
		
		/**
		  //build the following array
			array(
					"identifier" => "id",
					"idAttribute" => "id",
				"label" => "name",
				"items" => array( 
								model => 'module',
								id,
								icon,
								name,
								passed,
								next,
								previous
						
				)
			)
		 */
		public function index($curriculum_id = 0, $q = "", $l = ""){
			$this->log("<index curriculum_id=${curriculum_id} q=$q l=$l>", 'debug');
			$this->viewClass = "Json";
			try{
				if(isset($this->request->query['q']))
					$q = $this->request->query['q'];
				if(isset($this->request->query['l']))
					$l = $this->request->query['l'];
				$this->log("q=$q, l=$l", 'debug');
				
				$curriculum = $this->requestAction("/curriculums/cur_view/${curriculum_id}");
				$items = array();
				$item = array();
				$item["model"] = "curriculum";
				$item["id"] = "cur-" . $curriculum['Curriculum']['id'];
				$item["uniqueid"] =  $curriculum['Curriculum']['id'];
				$item["name"] = $curriculum["Curriculum"]["cur_name"];
				$item["short_name"] = $curriculum["Curriculum"]["cur_short_name"];
				$item["icon"] = "/images/image?id=" .  $curriculum["Curriculum"]["cur_img_id"] . "&width=32&height=32";
				//$item["icon"] = "/img/mod_icon.gif";
				$item["code"] =  $curriculum["Curriculum"]["cur_code"];
				$item["lang"] = $curriculum["CurLang"]["lan_code"];
				$item["created"] = $this->formatTime($curriculum["Curriculum"]["cur_created"]);
				$item["module_count"] =  $curriculum["Curriculum"]["cur_module_count"];
				//payment
				$item["enroll_fees"] =  $curriculum['CurFeesCurrency']['cur_html'].
						' ' . $curriculum['Curriculum']['cur_enroll_fees'];
				$item["payment_dest"] = $this->Config->read('cfg_fees_telephone_number');
				
				$this->loadModel('PaymentType');
				$payment = $this->PaymentType->find('first', array());
				
				$item["payment_message"] = $this->Language->translate($payment['PaymentType']['pat_message']);
				$item["payment_input"] = $this->Language->translate($payment['PaymentType']['pat_input_desc']);
				
				$item["course_id"]  = "";
				$item["course_created"]  = "";
				$item["course_graduated"]  = "";
				$item["course_module_count"]  = "";
			
				if($this->isAuthorized()){
					$this->loadModel('Course');
					$this->log("user is authorised", 'debug');
					$user_id = $this->user_id();
					$this->loadModel('Student');
					$student = $this->Student->find('first', array(
							'conditions' => array('StuPerson.per_user_id' => $user_id)
							)
					);
					if($student){
						$this->log('corresponding student object found', 'debug');
						$course = $this->Course->findByCouStudentId($student['Student']['id']);
						
						$item["course_id"]  = $course['Course']['id'];
						$item["course_created"]  = $course['Course']['cou_created'];
						if(is_int($course['Course']['cou_created']))
							$item["course_graduated"]  = $this->formatTime($course['Course']['cou_created']);
						$item["course_module_count"]  = $course['Course']['cou_module_count'];
					}else
						$this->log("user is not a student", 'debug');
				}else
					$this->log("user is not authorized", 'debug');
				
				$modules = $this->requestAction("/modules/mod_index/${curriculum_id}?q=$q&l=$l");
				$this->log("modules=" . var_export($modules, true), 'debug');
				$children = array();
			
				foreach($modules as $module){
					$child = array();
					$child["model"] = "module";
					$child["id"] = "mod-" . $module['Module']['id'];
					$child["name"] = $module["Module"]["mod_name"];
					$child["previous"] = "";
					$child['previous_passed'] = 0;
					
					$this->log("checking previous module for module:" . 
						$module["Module"]["mod_name"], 'debug');
					if(isset($module['PrevModule']) && isset($module["PrevModule"]["mod_name"])){
						$child["previous"] = $module["PrevModule"]["mod_name"];
						$child['previous_passed'] = 
							$this->requestAction("/modules/mod_passed/". $module['PrevModule']['id']);
					}
					$this->log(sprintf("previous module is [%s] and is passed [%d]:" ,
						$child["previous"], $child['previous_passed']), 'debug');
					
					$child["icon"] = "/images/image?id=" .  $module["Module"]["mod_img_id"] . "&width=32&height=32";
					$child["passed"] =  "";
					
					$course_module = null; 
					
					if($this->isAuthorized()){
						$this->log("user is authorised", 'debug');
						$user_id = $this->user_id();
						$this->loadModel('Student');
						$student = $this->Student->find('first', array(
								'conditions' => array('StuPerson.per_user_id' => $user_id)
								)
						);
						if($student){
							$this->loadModel('Course');
							$course = $this->Course->find('first', array(
										'conditions' => array('Course.cou_student_id' => $student['Student']['id'])
										)
							);
							if($course){
								$this->loadModel('CourseModule');
								$course_module = 
									$this->CourseModule->find('first',  array('conditions' =>
											array(
												'CourseModule.com_module_id' => $module['Module']['id'],
												'CourseModule.com_course_id' => $course['Course']['id']
											)
										)
									);
								if($course_module){
									//find last exam
									$this->loadModel('Exam');
									$exam = $this->Exam->find('first', array(
													'conditions' => array(
														'Exam.exa_course_module_id' => $course_module['CourseModule']['id']
													),
													'order' => array('Exam.exa_created' => 'DESC')
												)
											);
									if($exam && $exam['Exam']['exa_passed'])
										$child["passed"] =  'true';
								}
							}
						}
					}else{
						$this->log("user is not authorised", 'debug');
					}
					
					$child["uniqueid"] =  $module['Module']['id'];
					$children[] = $child;
				}
				
				$item['children'] = $children;
				$items[] = $item;
				
				$identifier = "id";
				$idAttribute = "id";
				$label = 'name';
				$data = compact("identifier", "idAttribute", "label", "items");
				$this->log("</index items=" . var_export($items, true) . ">", 'debug');
				if(!empty($this->request->params['requested'])){
					return $data;
				}
				$this->disableCache();
				$this->set($data);
				$this->set("_serialize", array("identifier", "idAttribute", "label", "items"));
			}catch(Exception $e){
				$this->log($e->getMessage());
			}
		}
		
		public function mod_view($id = null) {
			$this->Module->id = $id;
			$error = "";
			try{
				if (!$this->Module->exists()) {
					$error = __('Invalid Module');
					throw new NotFoundException($error);
				}
				$mod = $this->Module->read(null, $id);
			
				if(!empty($this->request->params['requested'])){
					return $mod;
				}
				$this->set('module', $mod);
			}catch(Exception $e){
				$this->log($e->getMessage());
				if(!$error)
					$error = AppModel::$DEFAULT_ERROR_MESSAGE;
				$this->Session->setFlash($error, 
							'default', array(), 'bad');
			}
		}
		
		/**
		 //build the following array
			array(
					"identifier" => "id",
					"idAttribute" => "id",
				"label" => "name",
				"items" => array( {
							model => 'module' ,
							id,
							icon,
							code,
							name,
							short_name
						 }
				)
		 */
		public function view($id = null){
			$this->viewClass = "Json";
			try{
				$mod = $this->requestAction("/modules/mod_view/$id");
				$items = array();
				$item = array();
				$item["model"] = "module";
				$item["id"] = "mod-" . $mod['Module']['id'];
				$item["name"] =  $mod['Module']["mod_name"];
				$item["short_name"] =  $mod['Module']["mod_name"];
				//$item["icon"] = "/images/image?id=" .   $mod['Module']["mod_img_id"] . "&width=32&height=32";
				$item["icon"] = "/img/mod_icon.gif";
				$item["code"] =   $mod['Module']["mod_code"];
				$item["uniqueid"] =   $mod['Module']["id"];
				$items[] = $item;
			
				$identifier = "id";
				$idAttribute = "id";
				$label = 'name';
				$data = compact("identifier", "idAttribute", "label", "items");
				if(!empty($this->request->params['requested'])){
					return $data;
				}
				$this->set($data);
				$this->set("_serialize", array("identifier", "idAttribute", "label", "items"));
			}catch(Exception $e){
				$this->log($e->getMessage());
			}
		}
		
		/**
		 //build the following array
			array(
					"identifier" => "id",
					"idAttribute" => "id",
				"label" => "name",
				"items" => array( {
							model => 'module',
							id,
							icon,
							name,
							short_name,
							children => [
								{
									model => 'attribute',
									id,
									name,
									value
								 }
							]
						 }
				)
		 */
		public function info($id = null){
			$this->log("<info id=$id>", 'debug');
			$this->viewClass = "Json";
			try{
				$this->log("loading module info ...", 'debug');
				$module = $this->requestAction("/modules/mod_view/$id");
				$items = array();
				$item = array();
				$item["model"] = "module";
				$item["id"] = "mod-" . $module['Module']['id'];
				$item["uniqueid"] =   $module['Module']["id"];
				$item["name"] = $module['Module']["mod_name"];
				$item["short_name"] = $module['Module']["mod_name"];
				//$item["icon"] = "/images/image?id=" .  $module['Module']["mod_img_id"] . "&width=32&height=32";
				$item["icon"] = "/img/mod_icon.gif";
				$item["code"] =  $module['Module']["mod_code"];
				$children = array();
			
				$this->log("loading attributes ...", 'debug');
				$attributes = $this->requestAction("/attributes/table/Module/$id");
				
				$this->log("attributes=" . var_export($attributes, true), 'debug');
				
				foreach($attributes as $attribute){
					$child = array();
					$child['model'] = 'attribute';
					$child['id'] = 'att-' . $attribute['Attribute']['id'];
					$child['uniqueid'] = $attribute['Attribute']['id'];
					$child['name'] = __(trim($attribute['Definition']['atd_display_name']));
					$value = '';
					$is_image = $attribute['Definition']['atd_is_image'];
					$is_data = $attribute['Definition']['atd_is_data'];
					$is_date = $attribute['Definition']['atd_is_date'];
					$is_number = $attribute['Definition']['atd_is_number'];
					$is_currency = $attribute['Definition']['atd_is_currency'];
					$is_editable = $attribute['Definition']['atd_is_editable'];
					if($is_image){
						$value .=  $this->_image($attribute['Attribute']['att_content'], 42, 38);
					}else
					if($is_data){
						$model = $attribute['Definition']['atd_data_model'];
						$data_key = $attribute['Definition']['atd_data_key'];
						$value .= $this->_data($model, $data_key, $attribute['Attribute']['att_content']);
					}else
					if($is_date){
						//debug("date");
						$value .= $this->_date($attribute['Attribute']['att_content']);
					}else
					if($is_currency)
						$value .= CakeNumber::currency($attribute['Attribute']['att_content']);
					else
						$value .= $attribute['Attribute']['att_content']; 
					$child['value'] = $value;
					$children[] = $child;
				}
				$item['children'] = $children;
				$items[] = $item;
			
				$identifier = "id";
				$idAttribute = "id";
				$label = 'name';
				$data = compact("identifier", "idAttribute", "label", "items");
			
				$this->log("items=" . var_export($items, true), 'debug');
				$this->log("</info id=$id>", 'debug');
				if(!empty($this->request->params['requested'])){
					return $data;
				}
				$this->set($data);
				$this->set("_serialize", array("identifier", "idAttribute", "label", "items"));
			}catch(Exception $e){
				$this->log($e->getMessage());
			}
		}
	
		private function _date($datetime){
			return date("F j, Y", $datetime);
		}
		
		private function _image($image_id, $width=50, $height=50, $options = array()){
			$attrs = "";
			foreach($options as $name => $value)
				$attrs .= $name . '="' . $value . '" ';
			if($image_id)
				return sprintf("<img src='/images/image?id=%d&width=%d&height=%d' width='%d' height='%d' %s>", 
						$image_id, $width, $height, $width, $height, $attrs);
			return sprintf("<img src='/img/pixel.gif' width='%d' height='%d'>", $image_id, $width, $height);
		}
	
		private function _data($model, $field = 'id', $value, $conditions = false, $displayField = ''){
			$controller = Inflector::underscore($model);
			$controller = Inflector::pluralize($controller);
			$action = "autocomplete";
			$use_codes = 0;
			if($field !== 'id')
				$use_codes = 1;
			$url = "/$controller/autocomplete/${use_codes}/$displayField";
			$map = $this->requestAction($url);
		
			if(!$map)
				return "";
			foreach($map as $key => $display){
				if($key === $value){
					return $display;
				}
			}
			return "";
		}
		
		/**
		 //build the following array
			array(
					"identifier" => "id",
					"idAttribute" => "id",
				"label" => "name",
				"items" => array( {
							model => 'module',
							id,
							icon,
							name,
							short_name,
							children => [
								{
									model => 'video',
									id,
									mp4,
									ogv,
									webm,
									text
								 }
							]
						 }
				)
		 */
		public function videos($id = null){
			$this->log("<videos id=$id>", 'debug');
			$this->viewClass = "Json";
			try{
				$module = $this->requestAction("/modules/mod_view/$id");
				$items = array();
				$item = array();
				$item["model"] = "module";
				$item["id"] = "mod-" . $module['Module']['id'];
				$item["uniqueid"] =   $module['Module']["id"];
				$item["name"] = $module['Module']["mod_name"];
				$item["short_name"] = $module['Module']["mod_name"];
				//$item["icon"] = "/images/image?id=" .  $module['Module']["mod_img_id"] . "&width=32&height=32";
				$item["icon"] = "/img/mod_icon.gif";
				$item["code"] =  $module['Module']["mod_code"];
				$children = array();
			
				$videos = $this->requestAction("/documents/resources/video/Module/$id");
				$this->log("videos=" . var_export($videos, true), 'debug');
				
				foreach($videos as $video){
					$child = array();
					$child['model'] = 'video';
					$child['id'] = "vid-" . $video['Resource']['id'];
					$child['uniqueid'] = $video['Resource']['id'];
					
					$child['mp4'] = $video["Resource"]["res_mp4"];
					
					$child['ogv'] = $video["Resource"]["res_ogv"];
					
					$child['webm'] = $video["Resource"]["res_webm"];
					
					$child['embed'] = $video["Resource"]["res_embed"];
		
					$children[] = $child;
				}
				$item['children'] = $children;
				$items[] = $item;
			
				$identifier = "id";
				$idAttribute = "id";
				$label = 'name';
				$data = compact("identifier", "idAttribute", "label", "items");
			
				$this->log("items=" . var_export($items, true), 'debug');
				$this->log("</videos id=$id>", 'debug');
				if(!empty($this->request->params['requested'])){
					return $data;
				}
				$this->set($data);
				$this->set("_serialize", array("identifier", "idAttribute", "label", "items"));
			}catch(Exception $e){
				$this->log($e->getMessage());
			}
		}
		
		/**
		 //build the following array
			array(
					"identifier" => "id",
					"idAttribute" => "id",
				"label" => "name",
				"items" => array( {
							model => 'module',
							id,
							icon,
							name,
							short_name,
							children => [
								{
									model => 'audio',
									id,
									mp3,
									ogg,
									wav,
									text
								 }
							]
						 }
				)
		 */
		public function audios($id = null){
			$this->log("<audios id=$id>", 'debug');
			$this->viewClass = "Json";
			try{
				$module = $this->requestAction("/modules/mod_view/$id");
				$items = array();
				$item = array();
				$item["model"] = "module";
				$item["id"] = "mod-" . $module['Module']['id'];
				$item["uniqueid"] =   $module['Module']["id"];
				$item["name"] = $module['Module']["mod_name"];
				$item["short_name"] = $module['Module']["mod_name"];
				//$item["icon"] = "/images/image?id=" .  $module['Module']["mod_img_id"] . "&width=32&height=32";
				$item["icon"] = "/img/mod_icon.gif";
				$item["code"] =  $module['Module']["mod_code"];
				$children = array();
			
				$audios = $this->requestAction("/documents/resources/audio/Module/$id");
				$this->log("audios=" . var_export($audios, true), 'debug');
				
				foreach($audios as $audio){
					$child = array();
					$child['model'] = 'audio';
					$child['id'] = "aud-" . $audio['Resource']['id'];
					$child['uniqueid'] = $audio['Resource']['id'];
					
					$child['mp3'] = $audio["Resource"]["res_mp3"];
					
					$child['ogg'] = $audio["Resource"]["res_ogg"];
						
					$child['wav'] = $audio["Resource"]["res_wav"];
					
					$child['embed'] = $audio["Resource"]["res_embed"];
				
					$children[] = $child;
				}
				$item['children'] = $children;
				$items[] = $item;
			
				$identifier = "id";
				$idAttribute = "id";
				$label = 'name';
				$data = compact("identifier", "idAttribute", "label", "items");
			
				$this->log("items=" . var_export($items, true), 'debug');
				$this->log("</audios id=$id>", 'debug');
				if(!empty($this->request->params['requested'])){
					return $data;
				}
				$this->set($data);
				$this->set("_serialize", array("identifier", "idAttribute", "label", "items"));
			}catch(Exception $e){
				$this->log($e->getMessage());
			}
		}
		
		
		/**
		 //build the following array
			array(
					"identifier" => "id",
					"idAttribute" => "id",
				"label" => "name",
				"items" => array( {
							model => 'module',
							id,
							icon,
							name,
							short_name,
							children => [
								{
									model => 'document',
									source
								 }
							]
						 }
				)
		 */
		public function documents($id = null){
			$this->log("<documents id=$id>", 'debug');
			$this->viewClass = "Json";
			try{
				$module = $this->requestAction("/modules/mod_view/$id");
				$items = array();
				$item = array();
				$item["model"] = "module";
				$item["id"] = "mod-" . $module['Module']['id'];
				$item["uniqueid"] =   $module['Module']["id"];
				$item["name"] = $module['Module']["mod_name"];
				$item["short_name"] = $module['Module']["mod_name"];
				//$item["icon"] = "/images/image?id=" .  $module['Module']["mod_img_id"] . "&width=32&height=32";
				$item["icon"] = "/img/mod_icon.gif";
				$item["code"] =  $module['Module']["mod_code"];
				$children = array();
			
				$child = array();
				$documents = $this->requestAction("/documents/resources/document/Module/$id");
				$this->log("documents=" . var_export($documents, true), 'debug');
				
				foreach($documents as $document){
					$child = array();
					$child['model'] = 'document';
					$child['id'] = "doc-" . $document['Resource']['id'];
					$child['uniqueid'] = $document['Resource']['id'];
					
					$child['pdf'] = $document["Resource"]["res_pdf"];
					
					$child['embed'] = $document["Resource"]["res_embed"];
						
					$children[] = $child;
				}
				
					
				$item['children'] = $children;
				$items[] = $item;
			
				$identifier = "id";
				$idAttribute = "id";
				$label = 'name';
				$data = compact("identifier", "idAttribute", "label", "items");
			
				$this->log("items=" . var_export($items, true), 'debug');
				$this->log("</documents id=$id>", 'debug');
				if(!empty($this->request->params['requested'])){
					return $data;
				}
				$this->set($data);
				$this->set("_serialize", array("identifier", "idAttribute", "label", "items"));
			}catch(Exception $e){
				$this->log($e->getMessage());
			}
		}
		
		/**
		 //build the following array
			array(
					"identifier" => "id",
					"idAttribute" => "id",
					"label" => "name",
					"items" => array( {
								model => 'module',
								id,
								icon,
								name,
								short_name,
								children => [
									{
										model => 'exam',
										id,
										score,
										passed,
										completed,
										children => [
											{
												model => 'question',
												id,
												text,
												children => [
													{
														model => 'option',
														id,
														text
													 }
												]
											 }
										]
									 }
								]
							 }
					)
		 */
		
		public function exam($module_id = 0, $exam_session_id = 0, $force_new = 0){
			$this->log("<exam module_id=${module_id} exam_session_id=${exam_session_id} force_new=${force_new}>", 'debug');
			$this->viewClass = "Json";
			try{
				$q = 0;
				$a = 0;
				if(isset($this->request->query['q']))
					$q = $this->request->query['q'];
				if(isset($this->request->query['a']))
					$a = $this->request->query['a'];
				$this->log("q=$q, a=$a", 'debug');
				
				$module = $this->requestAction("/modules/mod_view/${module_id}");
				$items = array();
				$item = array();
				$item["model"] = "module";
				$item["id"] = "mod-" . $module['Module']['id'];
				$item["uniqueid"] =   $module['Module']["id"];
				$item["name"] = $module['Module']["mod_name"];
				$item["short_name"] = $module['Module']["mod_name"];
				//$item["icon"] = "/images/image?id=" .  $module['Module']["mod_img_id"] . "&width=32&height=32";
				$item["icon"] = "/img/mod_icon.gif";
				$item["code"] =  $module['Module']["mod_code"];
				$children = array();
				
				if($exam_session_id && $q && $a){
					$this->requestAction("/modules/mod_check/${exam_session_id}/$q/$a");
				}
			
				$exam = $this->requestAction("/modules/mod_exam/${module_id}/${exam_session_id}/${force_new}");
				$this->log("exam=" . var_export($exam, true), 'debug');
				
				
				$child = array();
				$child['model'] = 'exam';
				$child['id'] = "exa-" . $exam['Exam']['id'];
				$child['uniqueid'] = $exam['Exam']['id'];
				$child['score'] = $exam['Exam']['exa_score'];
				$child['passed'] = $exam['Exam']['exa_passed'];
				$child['completed'] = $exam['Exam']['exa_completed'];
				$child['question_count'] = $exam['Exam']['exa_question_count'];
				
				if(!$exam['Exam']['exa_completed']){
					$next_assessment = $this->requestAction("/modules/mod_next_assessment/".  
						$exam['Exam']['id']);
					$grand_child = array();
					$grand_child['model'] = 'question';
					$grand_child['id'] = "que-" . $next_assessment['Assessment']['id'];
					$grand_child['uniqueid'] = $next_assessment['Assessment']['id'];
					$grand_child['text']  = $next_assessment['Assessment']['ass_description'];
					
					$answers = $this->requestAction("/modules/mod_answers/".  
						$next_assessment['Assessment']['id']);
					
					$ancestors = array();
					if($answers){
						foreach($answers as $answer){
							$ancestor = array();
							$ancestor['model'] = 'option';
							$ancestor['id'] = "opt-" . $answer['Option']['id'];
							$ancestor['uniqueid'] = $answer['Option']['id'];
							$ancestor['text'] = $answer['Option']['opt_display_text'];
							$ancestors[] = $ancestor;
						}
					}
					
					$grand_child['children'] = $ancestors;
					$child['children'] = array($grand_child);
				}
				
				$children[] = $child;
				
				$item['children'] = $children;
				$items[] = $item;
			
				$identifier = "id";
				$idAttribute = "id";
				$label = 'name';
				$data = compact("identifier", "idAttribute", "label", "items");
			
				$this->log("items=" . var_export($items, true), 'debug');
				$this->log("</exam>", 'debug');
				if(!empty($this->request->params['requested'])){
					return $data;
				}
				$this->set($data);
				$this->set("_serialize", array("identifier", "idAttribute", "label", "items"));
			}catch(Exception $e){
				$this->log($e->getMessage());
			}
		}
		
		public function mod_exam($module_id = 0, $exam_session_id = 0, $force_new = 0){
			$this->log("<mod_exam module_id=${module_id} exam_session_id=${exam_session_id} force_new=${force_new}>", 'debug');
			
			$this->Module->id = $module_id;
			$error = "";
			try{
				if (!$this->Module->exists()) {
					$error = __('Invalid Module');
					throw new NotFoundException($error);
				}
				$mod = $this->Module->read(null, $module_id);
				
				$this->loadModel('CourseModule');
				
				if(!$this->isAuthorized()){
					$error = __('User is not Authorised for This Action');
					throw new NotFoundException($error);
				}
				$this->log("user is authorised", 'debug');
				$user_id = $this->user_id();
				$this->loadModel('Student');
				$student = $this->Student->find('first', array(
														'conditions' => array('StuPerson.per_user_id' => $user_id)
													)
												);
				if(!$student){
					$error = __('User is not a Student');
					throw new NotFoundException($error);
				}
				$this->log('corresponding student object found', 'debug');
				
				$this->loadModel('Course');
				$course = $this->Course->find('first',  array(
					'conditions' => array(
							'Course.cou_student_id' => $student['Student']['id'],
							'Course.cou_curriculum_id' => $this->Module->data['Module']['mod_curriculum_id']
					)
				));
				if(!$course){
					$error = __('User is not Enrolled in this Curriculum');
					throw new NotFoundException($error);
				}
				
				$course_module = $this->CourseModule->find('first',  array(
					'conditions' => array(
							'CourseModule.com_course_id' => $course['Course']['id'],
							'CourseModule.com_module_id' => $module_id
					)
				));
				
				if(!$course_module){
					$error = __('User is not Registered for this Module');
					throw new NotFoundException($error);
				}
				$this->log("course module loaded", 'debug');
			
				$exam = null;
				$this->loadModel('Exam');
				$exam = $this->Exam->find('first',  array(
					'conditions' => array(
						'Exam.exa_course_module_id' => $course_module['CourseModule']['id']),
					'order' => array('Exam.exa_created' => 'DESC')
							  )	
							);
				if(!$exam)
					$this->log("no open exam found", 'debug');
				else
					$this->log("exam=" . var_export($exam, true), 'debug');
					
				if(!$exam || $force_new){
					$this->log("creating new exam session", 'debug');
					$this->Exam->create();
					$this->Exam->data['Exam']['exa_score'] = 0;
					$this->Exam->data['Exam']['exa_passed'] = 0;
					$d = new DateTime();
					$this->Exam->data['Exam']['exa_created'] = $d->getTimestamp();
					$this->Exam->data['Exam']['exa_completed'] = 0;
					$this->Exam->data['Exam']['exa_course_module_id'] = 
						$course_module['CourseModule']['id'];
					$this->Exam->data['Exam']['exa_question_count'] = 10; //TODO;
					$this->Exam->data['Exam']['exa_auto_pass_count'] = 5; //TODO
					
					if(!$this->Exam->save($this->Exam->data)){
						$error = __('Exam cannot be Saved');
						throw new NotFoundException($error);
					}
					$this->log("new exam session is created", 'debug');
					$exam = $this->Exam->read();
				}
				
				$this->log("</mod_exam>", 'debug');
			
				return $exam;
			}catch(Exception $e){
				$this->log($e->getMessage());
				if(!$error)
					$error = AppModel::$DEFAULT_ERROR_MESSAGE;
				$this->Session->setFlash($error, 
							'default', array(), 'bad');
			}
		}
		
		
		public function mod_next_assessment($exam_id = 0){
			$this->log("<mod_next_assessment  exam_id=${exam_id}>", 'debug');
			
			$this->loadModel('Exam');
			$this->Exam->id = $exam_id;
			$error = "";
			try{
				if (!$this->Exam->exists()) {
					$error = __('Invalid Exam');
					throw new NotFoundException($error);
				}
				$this->log("exam exists", 'debug');
				$exam = $this->Exam->read(null, $exam_id);
				
				if(!$this->isAuthorized()){
					$error = __('User is not Authorised for This Action');
					throw new NotFoundException($error);
				}
				$this->log("user is authorized", 'debug');
				
				
				$this->loadModel('ExamQuestion');
				$exam_questions = 
					$this->ExamQuestion->findAllByExqExamId($exam_id);
				$this->log("exam_questions:=" . var_export($exam_questions, true), 'debug');
				
				$done_list = array();
				if($exam_questions){
					foreach($exam_questions as $exam_question){
					  $done_list[] = $exam_question['ExamQuestion']['exq_assessment_id'];
					}
				}
				$this->log("done_list:=" . var_export($done_list, true), 'debug');
				
				$max_question_count = $exam['Exam']['exa_question_count'];
				if(count($done_list) >= $max_question_count ){
					$error = __('Exam is already Completed');
					throw new NotFoundException($error);
				}
				$this->log("exam is ongoing", 'debug');
				
				$this->loadModel('Assessment');
				$next_ass = $this->Assessment->find('first', array(
					'conditions' => 
						array('Assessment.ass_module_id' => 
							       $exam['ExaCourseModule']['com_module_id'],
							   'NOT' => array('Assessment.id' => $done_list) )
				));
				
				$this->log("</mod_next_assessment next_ass=" . var_export($next_ass, true) . ">", 'debug');
				return $next_ass;
			}catch(Exception $e){
				$this->log($e->getMessage());
				if(!$error)
					$error = AppModel::$DEFAULT_ERROR_MESSAGE;
				$this->Session->setFlash($error, 
							'default', array(), 'bad');
			}
		}
		
		public function mod_check($exam_id = 0, $q = 0, $a = 0){
			$this->log("<mod_check  exam_id=${exam_id} q=$q a=$a>", 'debug');
			
			$this->loadModel('Exam');
			$this->Exam->id = $exam_id;
			$error = "";
			try{
				$this->log("checking if exam exists ...", 'debug');
				if (!$this->Exam->exists()) {
					$error = __('Invalid Exam');
					throw new NotFoundException($error);
				}
				$exam = $this->Exam->read(null, $exam_id);
				
				$this->log("checking if user is authorised ...", 'debug');
				if(!$this->isAuthorized()){
					$error = __('User is not Authorised for This Action');
					throw new NotFoundException($error);
				}
				
				$this->log("checking if exam is completed ...", 'debug');
				if($exam['Exam']['exa_completed']){
					$error = __('Exam is already Completed');
					throw new NotFoundException($error);
				}
				
				$this->log("checking if assessment exists ...", 'debug');
				$this->loadModel('Assessment');
				$this->Assessment->id = $q;
				if (!$this->Assessment->read()) {
					$error = __('Invalid Assessment');
					throw new NotFoundException($error);
				}
				
				$this->log("checking if question exists ...", 'debug');
				$this->loadModel('Option');
				$this->Option->id = $a;
				if (!$this->Option->read()) {
					$error = __('Invalid Assessment Option');
					throw new NotFoundException($error);
				}
				$exq_is_approved = $this->Option->data['Option']['opt_is_ok'];
				$exq_answer_code = $this->Option->data['Option']['opt_code'];
				
				$this->log("creating new exam question ...", 'debug');
				$this->loadModel('ExamQuestion');
				$this->ExamQuestion->create();
				$this->ExamQuestion->data['ExamQuestion']['exq_assessment_id'] = $q;
				$this->ExamQuestion->data['ExamQuestion']['exq_answer_code'] = $exq_answer_code;
				$this->ExamQuestion->data['ExamQuestion']['exq_is_approved'] = $exq_is_approved ;
				$this->ExamQuestion->data['ExamQuestion']['exq_exam_id'] = $exam_id;
				
				if(!$this->ExamQuestion->save($this->ExamQuestion->data)){
					$error = __('Exam Question cannot be Saved');
					throw new NotFoundException($error);
				}
				
				$this->log("checking if exam is completed ...", 'debug');
				$this->Exam->read();
				$this->Exam->data['Exam']['exa_score'] = 
					$this->Exam->data['Exam']['exa_good_answer_count'];
					
				if($this->Exam->data['Exam']['exa_answer_count'] >=
				$this->Exam->data['Exam']['exa_question_count']){
					$this->log("exam is completed. Updating exam passed state ...", 'debug');
					$this->Exam->data['Exam']['exa_passed'] =  
						$this->Exam->data['Exam']['exa_good_answer_count'] >= 
						$this->Exam->data['Exam']['exa_auto_pass_count'];
					$d = new DateTime();
					$this->Exam->data['Exam']['exa_completed'] = $d->getTimestamp();
				}
				
				$this->log("saving exam ...", 'debug');
				if(!$this->Exam->save($this->Exam->data)){
					$error = __('Exam cannot be Saved');
					throw new NotFoundException($error);
				}
				
				$this->log("</mod_check return=true>", 'debug');
				
				return true;
			}catch(Exception $e){
				$this->log($e->getMessage());
				if(!$error)
					$error = AppModel::$DEFAULT_ERROR_MESSAGE;
				$this->Session->setFlash($error, 
							'default', array(), 'bad');
			}
			$this->log("</mod_check return=false>", 'debug');
			return false;
		}
		
		public function mod_answers($ass_id = 0){
			$this->log("<mod_answers  ass_id=${ass_id}>", 'debug');
			
			$this->loadModel('Assessment');
			$this->Assessment->id = $ass_id;
			$error = "";
			try{
				$this->log("loading assessment ...", 'debug');
				if (!$this->Assessment->exists()) {
					$error = __('Invalid Assessment');
					throw new NotFoundException($error);
				}
				$ass = $this->Assessment->read(null, $ass_id);
				
				$this->log("checking if user is authorised ...", 'debug');
				if(!$this->isAuthorized()){
					$error = __('User is not Authorised for This Action');
					throw new NotFoundException($error);
				}
			
				$this->log("loading options ...", 'debug');
				$this->loadModel('Option');
				$options = 
					$this->Option->findAllByOptAssessmentId($ass_id);
				
				if(!$options){
					$error = __('Assessment has no option list defined');
					throw new NotFoundException($error);
				}
				$this->log("</mod_answers>", 'debug');
			
				return $options;
			}catch(Exception $e){
				$this->log($e->getMessage());
				if(!$error)
					$error = AppModel::$DEFAULT_ERROR_MESSAGE;
				$this->Session->setFlash($error, 
							'default', array(), 'bad');
			}
		}
		
		public function mod_passed($module_id = 0, $user_id = 0){
			$this->log("<mod_passed module_id=${module_id} user_id=${user_id}>", 'debug');
			$this->Module->id = $module_id;
			$error = "";
			$passed = 0;
			try{
				if (!$this->Module->exists()) {
					$error = __('Invalid Module');
					throw new NotFoundException($error);
				}
				$mod = $this->Module->read(null, $module_id);
				
				$this->loadModel('CourseModule');
				
				if(!$user_id && !$this->isAuthorized()){
					$error = __('User is not Authorised for This Action');
					throw new NotFoundException($error);
				}
				$this->log("user is authorised", 'debug');
				if(!$user_id)
					$user_id = $this->user_id();
				$this->loadModel('Student');
				$student = $this->Student->find('first', array(
														'conditions' => array('StuPerson.per_user_id' => $user_id)
													)
												);
				if(!$student){
					$error = __('User is not a Student');
					throw new NotFoundException($error);
				}
				$this->log('corresponding student object found', 'debug');
				
				$this->loadModel('Course');
				$course = $this->Course->find('first',  array(
					'conditions' => array(
							'Course.cou_student_id' => $student['Student']['id'],
							'Course.cou_curriculum_id' => $this->Module->data['Module']['mod_curriculum_id']
					)
				));
				if(!$course){
					$error = __('User is not Enrolled in this Curriculum');
					throw new NotFoundException($error);
				}
				
				$course_module = $this->CourseModule->find('first',  array(
					'conditions' => array(
							'CourseModule.com_course_id' => $course['Course']['id'],
							'CourseModule.com_module_id' => $module_id
					)
				));
				
				if(!$course_module){
					$error = __('User is not Registered for this Module');
					throw new NotFoundException($error);
				}
				$this->log("course module loaded", 'debug');
			
				$exam = null;
				$this->loadModel('Exam');
				$exam = $this->Exam->find('first',  array(
					'conditions' => array(
						'Exam.exa_course_module_id' => $course_module['CourseModule']['id']),
					'order' => array('Exam.exa_created' => 'DESC')
							  )	
				);
				if($exam){
					$this->log("course exam loaded", 'debug');
					$this->log("</mod_passed>", 'debug');
					if(!empty($this->request->params['requested']))
						return $exam['Exam']['exa_passed'];
					$passed = $exam['Exam']['exa_passed'];
				}else
					$this->log("course exam not found for cource module:".  
						$course_module['CourseModule']['id'], 'debug');
				
			}catch(Exception $e){
				$this->log($e->getMessage());
				if(!$error)
					$error = AppModel::$DEFAULT_ERROR_MESSAGE;
			}
			$this->log("</mod_passed>", 'debug');
			if(!empty($this->request->params['requested']))
				return 0;
			$this->set('passed', $passed);
		}
	}
?>