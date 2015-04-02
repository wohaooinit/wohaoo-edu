<?php
App::uses('FootNationController', 'Controller');
App::uses('String', 'Utility');
/**
 * AuthorizationsController
 *
 * @property Authorization $Authorization
 */
class  AuthorizationsController extends FootNationController {
	public function beforeFilter(){
		parent::beforeFilter();
		$this->Auth->allow('user_id', 'user', 'groups', 'is_admin');
	}
	public function user_id(){
		$user = $this->Auth->user();
		if(!$user)
			return false;
		return $user['id'];
	}
	
	public function user($user_id = 0){
		$this->log("<authorizations.user user_id=${user_id}>", 'debug');
		$user = null;
		if(!$user_id){
			$this->log("user id is NOT defined, getting the default user", 'debug');
			$user = $this->Auth->user();
		}else{
			$this->log("user id IS defined, getting the specified user", 'debug');
			$this->loadModel('User');
			$this->User->id = $user_id;
			$u = $this->User->read();
			$user_attributes = $u['User'];
			$person_attributes = $u['UsrPerson'];
			$user = array_merge($person_attributes, $user_attributes);
		}
		$this->log("authorizations.user=" . var_export($user, true), 'debug');
		
		return $user;
	}
	
	public function groups($rol_name = "", $user_id = 0){
		$this->log("<authorizations.groups rol_name=${rol_name} user_id=${user_id}>", 'debug');
		if(!$user_id)
			$user_id = $this->user_id();
		$groups = array();
		$this->loadModel('UserRole');
		$conditions = array('UserRole.rol_user_id' => $user_id);
		if($rol_name){
			$this->log("role name IS defined, getting ONLY the groups of this user role name", 'debug');
			$conditions['UserRole.rol_name'] = $rol_name;
		}
		$user_roles = $this->UserRole->find('all', array('conditions' => $conditions));
		foreach($user_roles as $user_role){
			$groups[] = $user_role['UserRole']['rol_group_id'];
		}
		return $groups;
	}
	
	public function is_admin($group_id = 0){
		$user_id = $this->user_id();
		if(!$user_id)
			return false;
		/*if($user_id == $group_id)
			return true;*/
		$this->loadModel('User');
		$this->User->id = $user_id;
		if(!$this->User->read())
			return false;
		if($this->User->data['User']['usr_is_admin'])
			return true;
		//TODO: check whether the User is member of the sys_admin role of the SYSTEM group
		$this->loadModel('UserRole');
		$conditions = array('UserRole.rol_name' => 'admin', 'UserRole.rol_user_id' => $user_id,
							'UserRole.rol_group_id' => $group_id);
		$user_role = $this->UserRole->find('first', array('conditions' => $conditions));
		if($user_role)
			return true;
		return false;
	}
}
?>