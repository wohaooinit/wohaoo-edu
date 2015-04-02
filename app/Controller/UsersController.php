<?php

App::uses('EduController', 'Controller');
App::uses('HttpSocket', 'Network/Http');

/**
 * UsersController
 *
 * @property User $User
 */
class UsersController extends EduController {

	public $uses = array('User');
	
	public function beforeFilter(){
		parent::beforeFilter();
		$this->Auth->allow('login', 'register');
	}
	
	public function user($id = 0){
		if(empty($this->request->params['requested']))
			return false;
		$this->User->id = $id;
		if(!$this->User->exists())
			return false;
		$user = $this->User->read();
		$user_attributes = $user['User'];
		$person_attributes = $user['UsrPerson'];
		$user = array_merge($person_attributes, $user_attributes);
		return $user;
	}
	
	public function profile(){
		$user_id = $this->Security->user_id();
		$this->User->id = $user_id;
		$user = false;
		if(!($user = $this->User->read())){
			$error = __("Invalid User");
			throw new Exception($error);
		}
		$this->set('user', $user);
		$user_attributes = $this->requestAction("/attributes/table/User/${user_id}");
		$this->loadModel('Person');
		$person = $this->Person->findByPerUserId($user_id);
		$person_attributes = array();
		if($person){
			$person_id = $person['Person']['id'];
			$person_attributes = $this->requestAction("/attributes/table/Person/${person_id}");
		}
		$profile_attributes = array_merge($user_attributes, $person_attributes);
		$this->set('attributes', $profile_attributes);
	}
	
    public function autocomplete($use_codes = true, $displayField = "", $query = ""){
		if(isset($this->request->query['use_codes']) )
			$use_codes = $this->request->query['use_codes'];
		$this->log("<users.autocomplete use-codes=${use_codes} query=$query>", 'debug');
		if(isset($this->request->query['query']) )
			$query = $this->request->query['query'];
		if(!$displayField)
			$displayField = '"User"."usr_first_name" || "User"."usr_last_name"';
		if(!strstr($displayField, "."))
			$displayField = '"User".' . $displayField;
		$conditions = array("LOWER($displayField) LIKE"=> "%" . strtolower($query) . "%",
						"\"User\".\"usr_is_active\"" => 1);
		
		$find_options = array(
			"conditions" => $conditions,
			"limit" => 5
			);
		
		$users = $this->User->find('all', $find_options);
		$user_list = array();
		foreach($users as $user){
			$usr_name = $user['User']['usr_first_name'] . ' ' . $user['User']['usr_last_name'];
			if($use_codes)
				$user_list[$user['User']['usr_guid']] = $usr_name;
			else
				$user_list[$user['User']['id']] = $usr_name;
		}
		//sort users alphabetically by display name
		asort($user_list);//preserve index association
		if(!empty($this->request->params["requested"])){
			$this->log("</users.autocomplete>", 'debug');
			return $user_list;
		}
		//$this->viewClass = 'Json';
		$this->set('user_list', $user_list);
		$this->set('_serialize', array('user_list'));
		$this->log("</users.autocomplete>", 'debug');
	}

		public function login() {
			$this->log("<users.login>", 'debug');
			try{
				$message = "";
				$error = "";
				$userid = "";
				$this->viewClass = "Json";
				
				if ($this->Auth->login()) {
					$this->log('Login is successful', 'debug');
					$message = __("Login is successful");
					$user = $this->Auth->user();
					$userid = $user['id'];
				} else {
					$error = __("Login failed");
					throw new Exception($error);
				}
				$data = compact("message", "error", "userid");
				if(!empty($this->request->params['requested'])){
					return $data;
				}
		
				$this->set($data);
		
				$this->set("_serialize", array("message", "error", "userid"));
				return;
			}catch(Exception $e){
				if(!$error)
					$error = $e->getMessage();
				$this->log($e->getMessage(), 'error');
			}
			$this->set(compact("message", "error", "userid"));
			$this->set("_serialize", array("message", "error", "userid"));
			
			$this->log("</users.login>", 'debug');
		}

		public function logout() {
			$this->log("<users.logout>", 'debug');
			$error = "";
			$message = "";
			
			$this->viewClass = "Json";
			
			try{
			$this->Auth->logout();
			$message = __("Logout is successful");
			}catch(Exception $e){
				$this->log($e->getMessage());
				$error = __("Logout failed");
			}
			$data = compact("message", "error");
			if(!empty($this->request->params['requested'])){
				return $data;
			}
	
			$this->set($data);
	
			$this->set("_serialize", array("message", "error"));
			$this->log("</users.logout>", 'debug');
		}
	
		public function signup(){
			$this->log("<users.signup>", 'debug');
			$data = $this->requestAction(
					array("controller" => "users", "action" => "register", "plugin" => false),
					array('pass' => $this->request->data));
			$this->viewClass = "Json";
	
			$message = $data['message'];
			$error = $data['error'];
			$userid = $data['userid'];
			$perid = $data['perid'];
	
			$json_data = compact("message", "error", "userid", "perid");
			$this->set($json_data);
	
			$this->set("_serialize", array("message", "error", "userid", "perid"));
			$this->log("</users.signup>", 'debug');
		}
	public function index() {
		$conditions = array();
		$usersTableURL = array('controller' => 'users', 'action' => 'index');

		//join get query & named params
		$params = array_merge($this->request->params['named']);
		foreach($this->request->query as $key => $value) $params[$key] = $value;

		foreach($params as $key => $value) {
			$split = explode('-', $key);
			$modelName = (sizeof($split) > 1) ? $split[0] : 'User';
			$property = (sizeof($split) > 1) ? $split[1] : $key;
			if($modelName == 'User' || !empty($this->User->belongsTo[$modelName])) {
				$this->loadModel($modelName);
				$modelObj = new $modelName();
				if(!empty($modelObj)) {
					$columnType = $modelObj->getColumnType($property);
					if(!empty($columnType)){
						//add it to url
						$usersTableURL[$key] = $value;
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

		$this->User->recursive = 0;
		$this->set('users', $this->Paginator->paginate('User', $conditions, array()));
		$this->set('usersTableURL', $usersTableURL);
		//render as local table if it is an ajax request
		if($this->request->is('ajax'))
		{
			$this->render('table');
		}
	}
    
	public function view($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		$user = $this->User->read(null, $id);
		$this->set('user', $user);
	}
	
	public function register() {
		$this->log("<users.register>", 'debug');
		if ($this->request->is('post')) {
			$this->request->data  = array_merge($this->request->data, $this->request->params['pass']);
			
			$this->User->create();
			$error = '';
			$message = '';
			$userid = '';
			$perid = '';
			$db = $this->User->getDataSource();
			$db->begin();
			try{
				$this->log("checking user password ...", 'debug');
				$usr_password = "";
				if(isset($this->request->data['User']['usr_password']))
					$usr_password = $this->request->data['User']['usr_password'];
				if(!$usr_password){
					$error = __("User password cannot be empty!");
					throw new Exception($error);
				}
				
				if(strlen($usr_password) < 4){
					$error = __("User password must have at least 4 characters!");
					throw new Exception($error);
				}
				
				$safe_data = array();
				
				$usr_telephone= isset($this->request->data['User']['usr_telephone'])?$this->request->data['User']['usr_telephone']:"";
				$this->log("checking user password ...", 'debug');
				if(!$usr_telephone){
					$error = __("User telephone cannot be empty!");
					throw new Exception($error);
				}
				$user = $this->User->findByUsrTelephone($usr_telephone);
				if($user){
					$error = __("User telephone is already in use!");
					throw new Exception($error);
				}
				$cfg_telephone_format = $this->Config->read('cfg_telephone_format');
				if(!preg_match($cfg_telephone_format, $usr_telephone)){
					$error = __("User telephone is invalid!");
					throw new Exception($error);
				}
				$safe_data['usr_telephone'] = $usr_telephone;
				
				$usr_pict_id = isset($this->request->data['User']['usr_pict_id'])?$this->request->data['User']['usr_pict_id']:"";
				
				$safe_data['usr_pict_id'] = $usr_pict_id;
				
				$safe_data['usr_password'] = md5($usr_password);
				$safe_data['usr_hash_algo']  = 'md5';
				$date = new DateTime();
				$now = $date->getTimestamp();
				$safe_data['usr_created']  = $now;
				$safe_data['usr_modified']  = $now;
				
				$safe_data['usr_type'] = 'user';
				
				$this->log("saving user data ...", 'debug');
				
				if (!$this->User->save($safe_data)) {
					$error = __('The user could not be created. Please, try again.');
					throw new Exception($error);
				}
				$this->User->read();
				
				$this->log("updating user guid and active state ...", 'debug');
				$this->User->data['User']['usr_guid']  = $this->User->id;
				$this->User->data['User']['usr_is_active'] = 1;//TODO: user validation workflow
				//$this->log("NewUser.data=" . var_export($safe_data, true), 'debug');
				
				if (!$this->User->save($this->User->data)) {
					$error = __('The user could not be saved. Please, try again.');
					throw new Exception($error);
				}
				
				$this->log("building user personnal data ...", 'debug');
				
				$userid = $this->User->id;
				$safe_data = array();
				$safe_data['per_user_id'] = $userid;
				$safe_data['per_first_name'] = isset($this->request->data['Person']['per_first_name'])?$this->request->data['Person']['per_first_name']:"";
				$safe_data['per_last_name'] = isset($this->request->data['Person']['per_last_name'])?$this->request->data['Person']['per_last_name']:"";
				$safe_data['per_mobile_tel'] = isset($this->request->data['Person']['per_mobile_tel'])?$this->request->data['Person']['per_mobile_tel']:"";
				$safe_data['per_town'] = isset($this->request->data['Person']['per_town'])?$this->request->data['Person']['per_town']:"";
				$safe_data['per_street1'] = isset($this->request->data['Person']['per_street1'])?$this->request->data['Person']['per_street1']:"";
				$safe_data['per_street2'] = isset($this->request->data['Person']['per_street2'])?$this->request->data['Person']['per_street2']:"";
				$safe_data['per_created']  = $now;
				$safe_data['per_modified']  = $now;
				$safe_data['per_img_id'] = isset($this->request->data['User']['usr_pict_id'])?$this->request->data['User']['usr_pict_id']:"";
				$safe_data['per_email'] = isset($this->request->data['User']['usr_email'])?$this->request->data['User']['usr_email']:"";
				$this->log("checking person's birth date ...", 'debug');
				$per_birth_date = isset($this->request->data['Person']['per_birth_date'])?$this->request->data['Person']['per_birth_date']:"";
				if($per_birth_date){
					if(!is_array($per_birth_date)){
						$error = __("Bad birth date");
						throw new Exception($error);
					}
					if($per_birth_date['year'] && $per_birth_date['month'] && $per_birth_date['day']){
						//$this->log("per_birth_date=" . var_export($per_birth_date, true), 'debug');
						$birth_date = new DateTime();
						$birth_date->setDate($per_birth_date['year'], $per_birth_date['month'], $per_birth_date['day']);
						$safe_data['per_birth_date'] = $birth_date->getTimestamp();
					}
				}
				
				//check that country exists
				$this->log("checking person's country and region ...", 'debug');
				$per_country_id = isset($this->request->data['Person']['per_country_id'])?$this->request->data['Person']['per_country_id']:"";
				if($per_country_id){
					$this->loadModel('Country');
					$country = $this->Country->find('first', array('conditions' => array('Country.id' => $per_country_id)));
					if(!$country){
						$error = __('The selected country does not exist');
						throw new Exception($error);
					}
					
					//check that region exists
					$per_region_id = isset($this->request->data['Person']['per_region_id'])?$this->request->data['Person']['per_region_id']:"";
					if($per_region_id){
						$this->loadModel('Region');
						$region = $this->Region->find('first', array('conditions' => array('Region.reg_country_id' => $per_country_id,
																				   'Region.id' => $per_region_id)));
						if(!$region){
							$error = __('The selected region does not exist');
							throw new Exception($error);
						}
						$safe_data['per_region_id'] = $this->request->data['Person']['per_region_id'];
					}
					$safe_data['per_country_id'] = $this->request->data['Person']['per_country_id'];
				}
				$this->loadModel('Person');
				$this->Person->create();
				$this->log("saving person ...", 'debug');
				$this->Person->data = array('Person' => $safe_data);
				if (!$this->Person->save($this->Person->data)) {
					$error = __('The personnal data of the user could not be saved. Please, try again.');
					throw new Exception($error);
				}
				$perid = $this->Person->id;
				$this->log("The personnal data of the user are saved userid=$userid, perid=$perid.", 'debug');
											
				$message = __('The user has been saved');			
				
				$db->commit();
				$this->log("returning to calling client", 'debug');
				$data = compact("message", "error", "userid", "perid");
				if(!empty($this->request->params["requested"])){
					$this->log("</users.register>", 'debug');
					return $data;
				}else{
					if($this->request->isAjax()){
						$this->viewClass = "Json";
						$this->set($data);
						$this->set("_serialize", array("message", "error", "userid", "perid"));
					}else{	
						$this->Session->setFlash($message, 'default', array(), 'good');
						$this->redirect(array('action' => 'login'));
					}
				}
			}catch(Exception $e){
				$db->rollback();
				$validationErrors = var_export($this->User->validationErrors, true);
				if($this->Person)
					$validationErrors .= var_export($this->Person->validationErrors, true);
				$this->log('validationErrors=' . $validationErrors, 'error');
				$this->log($e->getMessage());
				if(!$error)
					$error = AppModel::$DEFAULT_ERROR_MESSAGE;
			}
			$data = compact("message", "error", "userid", "perid");
			if(!empty($this->request->params["requested"])){
				$this->log("</users.register>", 'debug');
				return $data;
			}else{
				if($this->request->isAjax()){
					$this->viewClass = "Json";
					$this->set($data);
					$this->set("_serialize", array("message", "error", "userid", "perid"));
				}else{
					$this->redirect("/");
				}
			}
		} else {
			//add the named params as data
			foreach($this->request->params['named'] as $param => $value) {
				$columnType = $this->User->getColumnType($param);
				if(!empty($columnType)) {
					if(empty($this->request->data['User'])) $this->request->data['User'] = array();
					$this->request->data['User'][$param] = $value;
				}
			}
		}
		$this->log("<users.register>", 'debug');
	}
	
	public function settings($user_id = 0){
		$this->log("<users.settings user_id=${user_id}>", 'debug');
		$message = "";
		$error = "";
		$personid = 0;
		$this->viewClass = "Json";
		
		$db = $this->User->getDataSource();
		$db->begin();
		try{	
			if(!$this->isAuthorized()){
				$error = __("User is not Authorised");
				throw new Exception($error);
			}
			$this->User->id = $this->user_id();
		
			$user = $this->User->read();
		
			if ($this->request->is('post')) {
				$safe_data = array();
				
				$usr_lang_code= isset($this->request->data['User']['usr_lang_code'])?
							$this->request->data['User']['usr_lang_code']:"";
				$this->log("checking lang code ...", 'debug');
				if($usr_lang_code){
					$this->loadModel('Lang');
					$lang = $this->Lang->findByLanCode($usr_lang_code);
					if(!$lang){
						$error = __("Unkown language code!");
						throw new Exception($error);
					}
					$safe_data['usr_lang_code']  = $usr_lang_code;
				}
				$date = new DateTime();
				$now = $date->getTimestamp();
				$safe_data['usr_modified']  = $now;
				
				$this->log("saving user data ...", 'debug');
				
				$this->log("saving data for user " . $this->User->id, 'debug');
				$this->log("safe_data=" . var_export($safe_data, true), 'debug');
				
				if (!$this->User->save($safe_data)) {
					$error = __('The user could not be created. Please, try again.');
					throw new Exception($error);
				}
				$this->User->read();
				
				$this->loadModel('Person');
				$this->Person->id = $this->User->data['UsrPerson']['id'];
				$this->Person->read();
				
				$safe_data = $this->Person->data['Person'];
			
				$safe_data['per_first_name'] = isset($this->request->data['Person']['per_first_name'])?
					$this->request->data['Person']['per_first_name']:"";
				$safe_data['per_last_name'] = isset($this->request->data['Person']['per_last_name'])?
					$this->request->data['Person']['per_last_name']:"";
				$safe_data['per_modified']  = $now;
				$this->log("checking person's birth date ...", 'debug');
				$per_birth_date = isset($this->request->data['Person']['per_birth_date'])?
					$this->request->data['Person']['per_birth_date']:"";
				if($per_birth_date){
					if(!is_array($per_birth_date)){
						$per_birth_date = explode("-", $per_birth_date);
					}
					
					if(!is_array($per_birth_date)){
						$error = __("Bad birth date");
						throw new Exception($error);
					}
					if(!isset($per_birth_date['year'])){
						$per_birth_date['year'] = $per_birth_date[0];
						$per_birth_date['month'] = $per_birth_date[1];
						$per_birth_date['day'] = $per_birth_date[2];
					}
					if($per_birth_date['year'] && $per_birth_date['month'] && $per_birth_date['day']){
						$this->log("per_birth_date=" . var_export($per_birth_date, true), 'debug');
						$birth_date = new DateTime();
						$birth_date->setDate($per_birth_date['year'], $per_birth_date['month'], $per_birth_date['day']);
						$safe_data['per_birth_date'] = $birth_date->getTimestamp();
					}
				}
				
				

				$this->log("saving person ...", 'debug');
				$this->log("safe_data=" . var_export($safe_data, true), 'debug');
				$this->Person->data = array('Person' => $safe_data);
				if (!$this->Person->save($this->Person->data)) {
					$error = __('The personnal data of the user could not be saved. Please, try again.');
					throw new Exception($error);
				}
				$personid = $this->Person->id;
				
				$db->commit();
				$this->log("The personnal data of the user are saved", 'debug');
											
				$message = __('The user has been saved');	
		
				$this->set(compact("message", "error", "personid"));
				$this->set("_serialize", array("message", "error", "personid"));
			}else{
				$items = array();
				$item = array();
				$item["model"] = "user";
				$item["id"] = $user['User']['id'];
				$item["first_name"] = $user['UsrPerson']['per_first_name'];
				$item["last_name"] = $user['UsrPerson']['per_last_name'];
				$item["name"] = $user['UsrPerson']['per_last_name'] . ', ' . $user['UsrPerson']['per_first_name'];
			
				$bdate = $user['UsrPerson']['per_birth_date'];
				if($bdate){
					$birth_date = new DateTime();
					$birth_date->setTimestamp($bdate);
					$bdate = $birth_date->format('Y-m-d');
				}
				$item["birth_date"] = $bdate;
				$item["language_code"] = $user['User']['usr_lang_code'];
			
				$items[] = $item;
				$identifier = "id";
				$idAttribute = "id";
				$label = 'name';
				$this->set(compact("identifier", "idAttribute", "label", "items"));
				$this->set("_serialize", array("identifier", "idAttribute", "label", "items"));
			}
		}catch(Exception $e){
			$db->rollback();
			$this->log($e->getMessage());
			$this->set(compact("message", "error", "personid"));
			$this->set("_serialize", array("message", "error", "personid"));
		}
	}
	
	public function enroll() {
		$this->log("<users.enroll>", 'debug');
		if ($this->request->is('post')) {
			$this->request->data  = array_merge($this->request->data, $this->request->params['pass']);
			
			$error = '';
			$message = '';
			$studentid = '';
			$courseid = '';
			$db = $this->User->getDataSource();
			$db->begin();
			try{
				$this->loadModel('Student');
				$this->Student->create();
			
				if(!$this->isAuthorized()){
					$error = __("User is not Authorised for This Action");
					throw new Exception($error);
				}
				$this->User->id = $this->user_id();
				$this->User->read();
				
				$this->log("checking curriculum id ...", 'debug');
				$curriculum_id = "";
				if(isset($this->request->data['Curriculum']['id']))
					$curriculum_id = $this->request->data['Curriculum']['id'];
				if(!$curriculum_id){
					$error = __("Curriculum id cannot be empty!");
					throw new Exception($error);
				}
				
				$pay_transaction_id= isset($this->request->data['Payment']['pay_transaction_id'])?$this->request->data['Payment']['pay_transaction_id']:"";
				$this->log("checking transaction id ...", 'debug');
				if(!$pay_transaction_id){
					$error = __("Transaction id cannot be empty!");
					throw new Exception($error);
				}
				$this->loadModel('Payment');
				$payment = $this->Payment->findByPayTransactionId($pay_transaction_id);
				if(!$payment){
					$error = __("Transaction Id is invalid!");
					throw new Exception($error);
				}
				if($payment['Payment']['pay_ended']){
					$error = __("Transaction Id has already been used!");
					throw new Exception($error);
				}
				
				$this->Payment->id = $payment['Payment']['id'];
				$this->Payment->read();
				
				$d = new DateTime();
				$now = $d->getTimestamp();
				$this->Payment->data['Payment']['pay_ended'] = $now;
				if(!$this->Payment->save($this->Payment->data)){
					$error = __("Transaction cannot be completed!");
					throw new Exception($error);
				}
				$this->log("saving student data ...", 'debug');
				$this->Student->data['Student']['stu_person_id'] = $this->User->data['UsrPerson']['id'];
				$this->Student->data['Student']['stu_created'] = $now;
				$this->Student->data['Student']['stu_modifed'] = $now;
				$this->Student->data['Student']['stu_code'] = $now;
				
				if (!$this->Student->save($this->Student->data)) {
					$error = __('The student could not be created. Please, try again.');
					throw new Exception($error);
				}
				$this->Student->read();
				$studentid = $this->Student->id;
				
				$this->log("creating course data ...", 'debug');
				$this->loadModel('Course');
				
				$this->Course->create();
				$this->Course->data['Course']['cou_curriculum_id'] = $curriculum_id;
				$this->Course->data['Course']['cou_student_id'] = $this->Student->id;
				$this->Course->data['Course']['cou_payment_id'] = $this->Payment->id;
				$this->Course->data['Course']['cou_created'] = $now;
				$this->Course->data['Course']['cou_modified'] = $now;
				
				if (!$this->Course->save($this->Course->data)) {
					$error = __('The new course could not be created. Please, try again.');
					throw new Exception($error);
				}
				$this->Course->read();
				$courseid = $this->Course->id;
				
				$this->loadModel('Module');
				$modules = $this->Module->findAllByModCurriculumId($curriculum_id);
				if(!$modules || count($modules) == 0){
					$error = __('The curriculum has no module.');
					throw new Exception($error);
				}
				
				$this->loadModel('CourseModule');
				
				foreach($modules as $module){
					$this->CourseModule->create();
					$this->CourseModule->data['CourseModule']['com_module_id'] = $module['Module']['id'];
					$this->CourseModule->data['CourseModule']['com_created'] = $now;
					$this->CourseModule->data['CourseModule']['com_course_id'] = $courseid;
				
					if (!$this->CourseModule->save($this->CourseModule->data)) {
						$error = __('The new course module could not be created. Please, try again.');
						throw new Exception($error);
					}
				}
											
				$message = __('The student has been saved');			
				
				$db->commit();
				$this->log("returning to calling client", 'debug');
				$data = compact("message", "error", "studentid", "courseid");
				if(!empty($this->request->params["requested"])){
					$this->log("</users.enroll>", 'debug');
					return $data;
				}else{
					if($this->request->isAjax()){
						$this->viewClass = "Json";
						$this->set($data);
						$this->set("_serialize", array("message", "error",  "studentid", "courseid"));
					}else{	
						$this->Session->setFlash($message, 'default', array(), 'good');
						$this->redirect(array('action' => 'login'));
					}
				}
			}catch(Exception $e){
				$db->rollback();
				$validationErrors = var_export($this->User->validationErrors, true);
				if($this->Person)
					$validationErrors .= var_export($this->Person->validationErrors, true);
				$this->log('validationErrors=' . $validationErrors, 'error');
				$this->log($e->getMessage());
				if(!$error)
					$error = AppModel::$DEFAULT_ERROR_MESSAGE;
			}
			$data = compact("message", "error",  "studentid", "courseid");
			if(!empty($this->request->params["requested"])){
				$this->log("</users.enroll>", 'debug');
				return $data;
			}else{
				if($this->request->isAjax()){
					$this->viewClass = "Json";
					$this->set($data);
					$this->set("_serialize", array("message", "error",  "studentid", "courseid"));
				}else{
					$this->redirect("/");
				}
			}
		} 
		$this->log("<users.enroll>", 'debug');
	}
	
	public function edit($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$error = '';
			try{
				$this->User->read();
				$old_usr_password = $this->User->data['User']['usr_password'];
				
				$usr_password = "";
				$usr_password_confirm = "";
				if(isset($this->request->data['User']['usr_password']))
					$usr_password = $this->request->data['User']['usr_password'];
				if(isset($this->request->data['User']['usr_password_confirm']))
					$usr_password_confirm = $this->request->data['User']['usr_password_confirm'];
				if($usr_password != $usr_password_confirm){
					$error = __("User passwords don't match!");
					throw new Exception($error);
				}
				
				//check that country exists
				$usr_country = $this->request->data['User']['usr_country'];
				$this->loadModel('Country');
				$country = $this->Country->find('first', array('conditions' => array('Country.con_code' => $usr_country)));
				if(!$country){
					$error = __('The selected country does not exist');
					throw new Exception($error);
				}
				//check that region exists
				$usr_region = 	$this->request->data['User']['usr_region'];
				$this->loadModel('Region');
				$region = $this->Region->find('first', array('conditions' => array('Region.reg_country' => $usr_country,
																	       'Region.reg_code' => $usr_region)));
				if(!$region){
					$error = __('The selected region does not exist');
					throw new Exception($error);
				}
		
				$safe_data = array();
				$safe_data['usr_first_name'] = $this->request->data['User']['usr_first_name'];
				$safe_data['usr_last_name'] = $this->request->data['User']['usr_last_name'];
				$safe_data['usr_email'] = $this->request->data['User']['usr_email'];
				$usr_pict_id = $this->request->data['User']['usr_pict_id'];
				if(!$usr_pict_id){
					$error = __('User picture is not defined');
					throw new Exception($error);
				}
				$safe_data['usr_pict_id'] = $usr_pict_id;
				$usr_birth_date = $this->request->data['User']['usr_birth_date'];
				if(!$usr_birth_date || !is_array($usr_birth_date)){
					$error = __("User birth date is not defined");
					throw new Exception($error);
				}
				$birth_date = new DateTime();
				$birth_date->setDate($usr_birth_date['year'], $usr_birth_date['month'], $usr_birth_date['day']);
				$safe_data['usr_birth_date'] = $birth_date->getTimestamp();
				
				$safe_data['usr_country'] = $this->request->data['User']['usr_country'];
				$safe_data['usr_region'] = $this->request->data['User']['usr_region'];
				
				if($old_usr_password  !== $usr_password){
					$safe_data['usr_password'] = md5($usr_password);
					$safe_data['usr_password_confirm'] = md5($usr_password_confirm);
					$safe_data['usr_hash_algo']  = 'md5';
				}
				$date = new DateTime();
				$now = $date->getTimestamp();
				$safe_data['usr_modified']  = $now;
				
				if (!$this->User->save($safe_data)) {
					$error = __('The user could not be saved. Please, try again.');
					throw new Exception($error);
				}
				$this->Session->setFlash(__('The user has been saved'), 'default', array(), 'good');
				$this->redirect(array('action' => 'view', $this->User->id));
			}catch(Exception $e){
				$this->log($e->getMessage());
				if(!$error)
					$error = AppModel::$DEFAULT_ERROR_MESSAGE;
				$this->Session->setFlash($error, 'default', array(), 'bad');
			}
		} else {
            		$user = $this->User->read(null, $id);
			$this->request->data = $user;
		}
	}
	
	public function password_change($id = 0){
		if ($this->request->is('post') || $this->request->is('put')) {
			$error = "";
			$redirect = "";
			try{
				$this->User->id = $id;
				if (!$this->User->exists()) {
					throw new NotFoundException(__('Invalid user'));
				}
				$this->User->read();
				$old_usr_password = $this->User->data['User']['usr_password'];
				
				$usr_password = "";
				$usr_password_confirm = "";
				if(isset($this->request->data['User']['usr_password']))
					$usr_password = $this->request->data['User']['usr_password'];
				if(isset($this->request->data['User']['usr_password_confirm']))
					$usr_password_confirm = $this->request->data['User']['usr_password_confirm'];
				if($usr_password != $usr_password_confirm){
					$error = __("User passwords don't match!");
					throw new Exception($error);
				}
				if(md5($usr_password) === $old_usr_password){
					$error = __("User password is not new");
					throw new Exception($error);
				}
				$safe_data['usr_password'] = md5($usr_password);
				$safe_data['usr_password_confirm'] = md5($usr_password_confirm);
				$safe_data['usr_hash_algo']  = 'md5';
				if (!$this->User->save($safe_data)) {
					$error = __("The user %s could not be saved. Please, try again.", __("Password"));
					throw new Exception($error);
				}
				$this->Session->setFlash(__('The user %s has been saved', __("Password")), 'default', array(), 'good');
				$redirect = Router::url(array('action' => 'profile', $this->User->id));
				if($this->Session->check("Url.referer"))
					$redirect = $this->Session->read("Url.referer");
				$this->redirect($redirect);
			}catch(Exception $e){
				$this->log($e->getMessage());
				if(!$error)
					$error = AppModel::$DEFAULT_ERROR_MESSAGE;
				$this->Session->setFlash($error, 'default', array(), 'bad');
			}
		}else{
			$this->Session->write("Url.referer", $this->request->referer());
			$user = $this->User->read(null, $id);
			$this->request->data = $user;
		}
	}
	
	public function username_change($id = 0){
		if ($this->request->is('post') || $this->request->is('put')) {
			$error = "";
			$redirect = "";
			try{
				$this->User->id = $id;
				if (!$this->User->exists()) {
					throw new NotFoundException(__('Invalid user'));
				}
				$this->User->read();
				$old_usr_username = $this->User->data['User']['usr_username'];
				
				$usr_username = "";
				if(isset($this->request->data['User']['usr_username']))
					$usr_username = $this->request->data['User']['usr_username'];
				if(empty($usr_username)){
					$error = __("User username cannot be empty");
					throw new Exception($error);
				}
				if($old_usr_username === $usr_username){
					$error = __("User username is not new");
					throw new Exception($error);
				}
				$u = $this->User->findByUsrUsername($usr_username);
				if($u){
					$error = __("The selected username is already in use");
					throw new Exception($error);
				}
				$safe_data['usr_username'] = $usr_username;
				if (!$this->User->save($safe_data)) {
					$error = __("The user %s could not be saved. Please, try again.", __("username"));
					throw new Exception($error);
				}
				$this->Session->setFlash(__('The user %s has been saved', __("username")), 'default', array(), 'good');
				$redirect = Router::url(array('action' => 'profile', $this->User->id));
				if($this->Session->check("Url.referer"))
					$redirect = $this->Session->read("Url.referer");
				$this->redirect($redirect);
			}catch(Exception $e){
				$this->log($e->getMessage());
				if(!$error)
					$error = AppModel::$DEFAULT_ERROR_MESSAGE;
				$this->Session->setFlash($error, 'default', array(), 'bad');
			}
		}else{
			$this->Session->write("Url.referer", $this->request->referer());
			$user = $this->User->read(null, $id);
			$this->request->data = $user;
		}
	}
	
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->User->delete()) {
			$this->Session->setFlash(__('User deleted'), 'default', array(), 'good');
            		$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('User was not deleted'), 'default', array(), 'bad');
		$this->redirect(array('action' => 'index'));
	}
	
	

	
}
