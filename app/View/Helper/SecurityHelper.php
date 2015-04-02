<?php
App::uses('AppHelper', 'Helper');

/**
 * Security helper
 *
 * @package       app.View.Helper
 */
class SecurityHelper extends AppHelper {
	protected static $ROLES = array('admin' => 1, 'point_of_sale_agent' => 1, 'seller' => 1, 'buyer' => 1, 'transporter' => 1);
	
	public function user_id(){
		return $this->_View->requestAction("/authorizations/user_id");
	}
	
	public function user($user_id = 0){
		return $this->_View->requestAction("/authorizations/user/${user_id}");
	}
	
	public function is_group($user_id = 0){
		$user = $this->user($user_id);
		if(!$user)
			return false;
		return $user['usr_type'] === 'group';
	}
	
	public function groups($rol_name = "", $user_id = 0){
		return $this->_View->requestAction("/authorizations/groups/${rol_name}/${user_id}");
	}
	
	public function has($role_name = "", $object_id = 0){
		if(!$role_name || !isset(self::$ROLES[$role_name]))
			return false;
		$function = "has_${role_name}_role";
		return $this->$function($object_id);
	}
	
	public function has_admin_role($object_id = 0){
		return $this->_View->requestAction("/authorizations/is_admin/${object_id}");
	}
	
	public function has_point_of_sale_agent_role($object_id = 0){
		return $this->_View->requestAction("/authorizations/is_point_of_sale_agent/${object_id}");
	}
	
	public function has_buyer_role($object_id = 0){
		return $this->_View->requestAction("/authorizations/is_buyer/${object_id}");
	}
	
	public function has_seller_role($object_id = 0){
		return $this->_View->requestAction("/authorizations/is_seller/${object_id}");
	}
	
	public function has_transporter_role($object_id = 0){
		return $this->_View->requestAction("/authorizations/is_transporter/${object_id}");
	}
}

?>