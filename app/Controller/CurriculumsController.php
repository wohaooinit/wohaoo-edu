<?php

	App::uses('EduController', 'Controller');
	
	class CurriculumsController extends EduController {
		public function beforeFilter(){
			parent::beforeFilter();
			$this->Auth->allow('map', 'autocomplete');
		}
		
		public function autocomplete($use_codes = false, $displayField = "", $query = false, $field = "id", $value = ""){
			$this->log("<curriculums.autocomplete use-codes=${use_codes} displayField=$displayField query=$query field=$field value=$value> ", 'debug');
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
				$displayField  = 'cur_name';
			$conditions = array("LOWER(\"Curriculum\".\"$displayField\") LIKE " => '%' . strtolower($query) . '%');
			if($value){
				$conditions[] = "\"Curriculum\".\"$field\"=$value" ;
			}
			$params = array_merge($this->request->params['named']);
			foreach($this->request->query as $key => $value) $params[$key] = $value;
			foreach($params as $param => $value) {
				$columnType = $this->Curriculum->getColumnType($param);
				if(!empty($columnType)) {
					$conditions["\"Curriculum\".\"$param\""] = $value;
				}
			}
			//$conditions = array();
			$find_options = array(
				"conditions" => $conditions
				);
			
			//set locale
			//NOTE: this is no more necessary because it is handled by AppModel::find
			$this->Curriculum->locale = Configure::read('Config.language');
			
			$this->log("find_options:=" . var_export($find_options, true), 'debug');
		
			$curriculums = $this->Curriculum->find('all', $find_options);
			$curriculum_list = array();
			foreach($curriculums as $curriculum){
				if($use_codes)
					$curriculum_list[$curriculum['Curriculum']['cur_code']] = $curriculum['Curriculum'][$displayField ];
				else
					$curriculum_list[$curriculum['Curriculum']['id']] = $curriculum['Curriculum'][$displayField ];
			}
			//sort curriculums alphabetically by display name
			asort($curriculum_list);//preserve index association
			$this->log("curriculum_list:=" . var_export($curriculum_list, true), 'debug');
			
			if(!empty($this->request->params["requested"])){
				$this->log("</curriculums.autocomplete>", 'debug');
				return $curriculum_list;
			}
			//$this->viewClass = 'Json';
			$this->set('curriculum_list', $curriculum_list);
			$this->set('_serialize', array('curriculum_list'));
			$this->log("</curriculums.autocomplete>", 'debug');
		}
		
		public function cur_index($query = "", $max_id = 0, $min_id = 0, $list = "") {
			$conditions = array();
			$joins = array();
			$group = array();
			$curriculumsTableURL = array('controller' => 'curriculums', 'action' => 'index');

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
		
			$this->log("list=$list", 'debug');
		
			$favoritesPos = strpos($query, ':favorites');
			if($favoritesPos !== false){
				//activate favorites search
				/*$this->log("checking user ...", 'debug');
				$user_id = $this->Security->user_id();
				if($user_id){
					$this->log("activating favorites search ...", 'debug');
					$joins[] = array('table' => 'favorites',
									'alias' => 'CurriculumFavorite',
									'type' => 'INNER',
									'conditions' => array(	'CurriculumFavorite.fav_object_id = Curriculum.id', 
														"CurriculumFavorite.fav_model = 'Curriculum'",
														"CurriculumFavorite.fav_user_id = ${user_id}")
							);
					$group = array("Curriculum.id");
					$this->Curriculum->virtualFields['cur_is_favorite'] = "count(CurriculumFavorite)";
				}*/
				//clear all references to the favorites keyword in query
				$query = str_replace(':favorites', '', $query);
			}
			
			if($list !== ""){
				$list = trim($list);
				$curriculumIds = explode(",", $list);
				$this->log("final list=/$list/", 'debug');
				$conditions["Curriculum.id"] = $curriculumIds;
			}
			
			if($query){
				$query = trim($query);
				$this->log("final query=/$query/", 'debug');
				$conditions["LOWER(\"Curriculum\".\"cur_name\" ||" .
						     " \"Curriculum\".\"cur_short_name\" ||" .
						     " \"Curriculum\".\"cur_code\" ||".
						     " \"Curriculum\".\"cur_description\") LIKE"] = 
									"%" . strtolower($query) . "%";
			}
		
			//join get query & named params
			$params = array_merge($this->request->params['named']);
			foreach($this->request->query as $key => $value) $params[$key] = $value;

			foreach($params as $key => $value) {
				$split = explode('-', $key);
				$modelName = (sizeof($split) > 1) ? $split[0] : 'Curriculum';
				$property = (sizeof($split) > 1) ? $split[1] : $key;
				if($modelName == 'Curriculum' || !empty($this->Curriculum->belongsTo[$modelName])) {
					$this->loadModel($modelName);
					$modelObj = new $modelName();
					if(!empty($modelObj)) {
						$columnType = $modelObj->getColumnType($property);
						if(!empty($columnType)){
							//add it to url
							$curriculumsTableURL[$key] = $value;
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
			
			//$this->log("conditions=" . var_export($conditions, true), 'debug');
			$this->Paginator->settings = $find_options;
		
			$this->Curriculum->recursive = 0;
			$curriculums = null;
			$this->log("find curriculums", 'debug');
			
		
			$curriculums = $this->Paginator->paginate('Curriculum', $conditions, array());
				
			//$this->log("curriculums=" . var_export($curriculums, true), 'debug');
			if(!empty($this->request->params['requested'])){
				return $curriculums;
			}
			$this->set('curriculums', $curriculums);
			$this->set('curriculumsTableURL', $curriculumsTableURL);
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
								model => 'curriculum',
								id,
								icon,
								code,
								lang,
								desc,
								name,
								created,
								module_count,
								course_id,
								course_created,
								course_graduated,
								course_module_count
						
				)
			)
		 */
		public function index($q = "", $l = ""){
			$this->log("<index q=$q l=$l>", 'debug');
			$this->viewClass = "Json";
			try{
				if(isset($this->request->query['q']))
					$q = $this->request->query['q'];
				if(isset($this->request->query['l']))
					$l = $this->request->query['l'];
				$this->log("q=$q, l=$l", 'debug');
				$curriculums = $this->requestAction("/curriculums/cur_index?q=$q&l=$l");
				$this->log("curriculums=" . var_export($curriculums, true), 'debug');
				$items = array();
			
				if($curriculums != null){
					foreach($curriculums as $curriculum){
						$item = array();
						$item["model"] = "curriculum";
						$item["id"] = "cur-" . $curriculum['Curriculum']['id'];
						$item["uniqueid"] = $curriculum['Curriculum']['id'];
						$item["name"] = $curriculum["Curriculum"]["cur_name"];
						$item["icon"] = "/images/image?id=" .  $curriculum["Curriculum"]["cur_img_id"] . "&width=65&height=65";
						$item["code"] =  $curriculum["Curriculum"]["cur_code"];
						$item["lang"] = $curriculum["CurLang"]["lan_code"];
						$item["created"] = $this->formatTime($curriculum["Curriculum"]["cur_created"]);
						$item["module_count"] =  $curriculum["Curriculum"]["cur_module_count"];
					
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
						$items[] = $item;
					}
				}
			
				$identifier = "id";
				$idAttribute = "id";
				$label = 'name';
				$data = compact("identifier", "idAttribute", "label", "items");
				$this->log("</index q=$q>", 'debug');
				if(!empty($this->request->params['requested'])){
					return $data;
				}
				$this->set($data);
				$this->set("_serialize", array("identifier", "idAttribute", "label", "items"));
			}catch(Exception $e){
				$this->log($e->getMessage());
			}
		}
		
		public function cur_view($id = null) {
			$this->Curriculum->id = $id;
			$error = "";
			try{
				if (!$this->Curriculum->exists()) {
					$error = __('Invalid Curriculum');
					throw new NotFoundException($error);
				}
				$cur = $this->Curriculum->read(null, $id);
			
				if(!empty($this->request->params['requested'])){
					return $cur;
				}
				$this->set('curriculum', $cur);
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
							model => 'curriculum' ,
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
				$curriculum = $this->requestAction("/curriculums/cur_view/$id");
				$items = array();
				$item = array();
				$item["model"] = "curriculum";
				$item["id"] = "cur-" . $curriculum['Curriculum']['id'];
				$item["uniqueid"] =  $curriculum['Curriculum']['id'];
				$item["name"] = $curriculum["Curriculum"]["cur_name"];
				$item["short_name"] = $curriculum["Curriculum"]["cur_short_name"];
				$item["icon"] = "/images/image?id=" .  $curriculum["Curriculum"]["cur_img_id"] . "&width=32&height=32";
				$item["code"] =  $curriculum["Curriculum"]["cur_code"];
				$item["lang"] = $curriculum["CurLang"]["lan_code"];
				$item["created"] = $this->formatTime($curriculum["Curriculum"]["cur_created"]);
				$item["module_count"] =  $curriculum["Curriculum"]["cur_module_count"];
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
							model => 'curriculum',
							id,
							icon,
							name,
							value,
							ranking,
							club,
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
				$curriculum = $this->requestAction("/curriculums/cur_view/$id");
				$items = array();
				$item = array();
				$item["model"] = "curriculum";
				$item["id"] = "cur-" . $curriculum['Curriculum']['id'];
				$item["uniqueid"] =  $curriculum['Curriculum']['id'];
				$item["name"] = $curriculum["Curriculum"]["cur_name"];
				$item["short_name"] = $curriculum["Curriculum"]["cur_short_name"];
				$item["icon"] = "/images/image?id=" .  $curriculum["Curriculum"]["cur_img_id"] . "&width=32&height=32";
				$item["code"] =  $curriculum["Curriculum"]["cur_code"];
				$item["lang"] = $curriculum["CurLang"]["lan_code"];
				$item["created"] = $this->formatTime($curriculum["Curriculum"]["cur_created"]);
				$item["module_count"] =  $curriculum["Curriculum"]["cur_module_count"];
				$children = array();
			
				$attributes = $this->requestAction("/attributes/table/Curriculum/$id");
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
							model => 'curriculum',
							id,
							icon,
							name,
							value,
							ranking,
							club,
							children => [
								{
									model => 'video',
									id,
									mp4,
									ogv,
									webm,
									embed,
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
				$curriculum = $this->requestAction("/curriculums/cur_view/$id");
				$items = array();
				$item = array();
				$item["model"] = "curriculum";
				$item["id"] = "cur-" . $curriculum['Curriculum']['id'];
				$item["uniqueid"] =  $curriculum['Curriculum']['id'];
				$item["name"] = $curriculum["Curriculum"]["cur_name"];
				$item["short_name"] = $curriculum["Curriculum"]["cur_short_name"];
				$item["icon"] = "/images/image?id=" .  $curriculum["Curriculum"]["cur_img_id"] . "&width=32&height=32";
				$item["code"] =  $curriculum["Curriculum"]["cur_code"];
				$item["lang"] = $curriculum["CurLang"]["lan_code"];
				$item["created"] = $this->formatTime($curriculum["Curriculum"]["cur_created"]);
				$item["module_count"] =  $curriculum["Curriculum"]["cur_module_count"];
				$children = array();
			
				$videos = $this->requestAction("/documents/resources/video/Curriculum/$id");
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
							model => 'curriculum',
							id,
							icon,
							name,
							value,
							ranking,
							club,
							children => [
								{
									model => 'audio',
									id,
									mp3,
									ogg,
									wav,
									embed,
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
				$curriculum = $this->requestAction("/curriculums/cur_view/$id");
				$items = array();
				$item = array();
				$item["model"] = "curriculum";
				$item["id"] = "cur-" . $curriculum['Curriculum']['id'];
				$item["uniqueid"] =  $curriculum['Curriculum']['id'];
				$item["name"] = $curriculum["Curriculum"]["cur_name"];
				$item["short_name"] = $curriculum["Curriculum"]["cur_short_name"];
				$item["icon"] = "/images/image?id=" .  $curriculum["Curriculum"]["cur_img_id"] . "&width=32&height=32";
				$item["code"] =  $curriculum["Curriculum"]["cur_code"];
				$item["lang"] = $curriculum["CurLang"]["lan_code"];
				$item["created"] = $this->formatTime($curriculum["Curriculum"]["cur_created"]);
				$item["module_count"] =  $curriculum["Curriculum"]["cur_module_count"];
				$children = array();
			
				$audios = $this->requestAction("/documents/resources/audio/Curriculum/$id");
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
							model => 'curriculum',
							id,
							icon,
							name,
							value,
							ranking,
							club,
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
				$curriculum = $this->requestAction("/curriculums/cur_view/$id");
				$items = array();
				$item = array();
				$item["model"] = "curriculum";
				$item["id"] = "cur-" . $curriculum['Curriculum']['id'];
				$item["uniqueid"] =  $curriculum['Curriculum']['id'];
				$item["name"] = $curriculum["Curriculum"]["cur_name"];
				$item["short_name"] = $curriculum["Curriculum"]["cur_short_name"];
				$item["icon"] = "/images/image?id=" .  $curriculum["Curriculum"]["cur_img_id"] . "&width=32&height=32";
				$item["code"] =  $curriculum["Curriculum"]["cur_code"];
				$item["lang"] = $curriculum["CurLang"]["lan_code"];
				$item["created"] = $this->formatTime($curriculum["Curriculum"]["cur_created"]);
				$item["module_count"] =  $curriculum["Curriculum"]["cur_module_count"];
				$children = array();
			
				$child = array();
				$documents = $this->requestAction("/documents/resources/document/Curriculum/$id");
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
	}
?>