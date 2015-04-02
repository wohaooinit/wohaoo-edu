<?php

App::uses('AdminAppController', 'Admin.Controller');
/**
 * MobileUsersController
 *
 * @property User $User
 */
class AdminUsersController extends AdminAppController {

	public $uses = array('User');
	
	public function index() {
		$conditions = array();
		$usersTableURL = array('controller' => 'admin_users', 'action' => 'index');

		//join get query & named params
		$params = array_merge($this->request->params['named']);
		foreach($this->request->query as $key => $value) $params[$key] = $value;

		foreach($params as $key => $value) {
			$split = explode('-', $key);
			$modelName = (sizeof($split) > 1) ? $split[0] : 'User';
			$property = (sizeof($split) > 1) ? $split[1] : $key;
			if($modelName == 'User' || !empty($this->User->belongsTo[$modelName])) {
				$this->louserModel($modelName);
				$modelObj = new $modelName();
				if(!empty($modelObj)) {
					$columnType = $modelObj->getColumnType($property);
					if(!empty($columnType)){
						//userd it to url
						$usersTableURL[$key] = $value;
						//userd it to conditions
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
		$users = $this->Paginator->paginate('User', $conditions, array());
		$this->log("users.=" . var_export($users, true));
		$this->set('users', $users);
		$this->set('usersTableURL', $usersTableURL);
		//render as local table if it is an ajax request
		if($this->request->is('ajax'))
		{
			$this->render('table');
		}
	}

    public function login() {
    	$this->log("<users.login>", 'debug');
    	
    	$login = $this->Auth->login();
    	 $this->log('login:=' . $login, 'debug');
    	 if ($login) {
            $this->log('Login is successful', 'debug');
            $url = $this->Auth->redirect();
            $this->log('Redirecting to ....' . $url, 'debug');
            $this->log("</users.login=true>", 'debug');
            $this->redirect($url);
        } else {
        	$this->log('Admin Login Failed');
		if($this->request->isPost()) {
			$this->Auth->flash(__('Invalid username or password, try again'));
		}
		$this->log("</users.login=false>", 'debug');
        }
    }

    public function logout() {
    	$this->redirect($this->Auth->logout());
    }
    
    public function signup(){
    	$this->log("<users.signup>", 'debug');
    	$data = $this->requestAction(
    			array("controller" => "users", "action" => "register", "plugin" => false),
    			array('pass' => $this->request->data));
	
	$message = $data['message'];
	$error = $data['error'];
	$userid = $data['userid'];
	$perid = $data['perid'];
	
	$json_data = compact("message", "error", "userid", "perid");
	$this->set($json_data);
	
	$this->set("_serialize", array("message", "error", "userid", "perid"));
	$this->log("</users.signup>", 'debug');
    }

}
