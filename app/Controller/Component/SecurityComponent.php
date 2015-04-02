<?php

App::uses('Component', 'Controller');

class SecurityComponent extends Component
{
	protected $_Controller;
	/**
	 * Called after the Controller::beforeFilter() and before the controller action
	 *
	 * @param Controller $controller Controller with components to startup
	 * @return void
	 * @link http://book.cakephp.org/2.0/en/controllers/components.html#Component::startup
	 */
	public function startup(Controller $controller) {
		$this->_Controller = $controller;
	}
	
	public function user($user_id = 0){
		return $this->_Controller->requestAction("/authorizations/user/${user_id}");
	}
	
	public function is_group($user_id = 0){
		$user = $this->user($user_id);
		if(!$user)
			return false;
		return $user['usr_type'] === 'group';
	}
	
	public function groups($rol_name = "", $user_id = 0){
		return $this->_Controller->requestAction("/authorizations/groups/${rol_name}/${user_id}");
	}
	
	protected static $ROLES = array('admin' => 1);
	
	public function user_id(){
		return $this->_Controller->requestAction("/authorizations/user_id");
	}
	
	public function has($role_name = "", $object_id = 0){
		if(!$role_name || !isset(self::$ROLES[$role_name]))
			return false;
		$function = "has_${role_name}_role";
		return $this->$function($object_id);
	}
	
	public function has_admin_role($object_id = 0){
		return $this->_Controller->requestAction("/authorizations/is_admin/${object_id}");
	}
	
	public function getfileinfo($file_name = ""){
        	$finfo = finfo_open(FILEINFO_MIME);
		list($type, $charset) = explode(';', finfo_file($finfo, $file_name));
		return array('mimetype' => $type);
        }
}
?>