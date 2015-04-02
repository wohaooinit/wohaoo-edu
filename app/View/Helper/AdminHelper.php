<?php
App::uses('AppHelper', 'Helper');

/**
 * Admin helper
 *
 * @package       app.View.Helper
 */
class AdminHelper extends AppHelper {
	protected static $IS_STATES = array('approved' => 1);
	protected static $CAN_STATES = array();
	
	public function user_id(){
		return $this->_View->requestAction("/authorizations/user_id");
	}
	
	public function is($state = "", $Model = "", $object_id){
		if(!$state || !isset(self::$IS_STATES[$state]))
			return false;
		if(!$Model || !$object_id)
			return false;
		$function = "is_${state}";
		return $this->_View->requestAction("/admins/$function/${Model}/${object_id}");;
	}
}

?>