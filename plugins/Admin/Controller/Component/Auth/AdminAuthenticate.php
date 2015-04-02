<?php

/**
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('FormAuthenticate', 'Controller/Component/Auth');
App::uses('CakeSession', 'Model/Datasource');

/**
 * An authentication adapter for AuthComponent. Provides the ability to authenticate using POST
 * data. Can be used by configuring AuthComponent to use it via the AuthComponent::$authenticate setting.
 *
 * {{{
 *	$this->Auth->authenticate = array(
 *		'Form' => array(
 *			'scope' => array('User.active' => 1)
 *		)
 *	)
 * }}}
 *
 * When configuring MobileAuthenticate you can pass in settings to which fields, model and additional conditions
 * are used. See FormAuthenticate::$settings for more information.
 *
 * @see AuthComponent::$authenticate
 */
class AdminAuthenticate extends FormAuthenticate {

/**
 * Checks the fields to ensure they are supplied.
 *
 * @param CakeRequest $request The request that contains login information.
 * @param string $model The model used for login verification.
 * @param array $fields The fields to be checked.
 * @return boolean False if the fields have not been supplied. True if they exist.
 */
	protected function _checkFields(CakeRequest $request, $model, $fields) {
		if (empty($request->data[$model])) {
			return false;
		}
		foreach (array($fields['username'], $fields['password']) as $field) {
			$value = $request->data($model . '.' . $field);
			if (empty($value) || !is_string($value)) {
				return false;
			}
		}
		return true;
	}

/**
 * Authenticates the identity contained in a request. Will use the `settings.userModel`, and `settings.fields`
 * to find POST data that is used to find a matching record in the `settings.userModel`. Will return false if
 * there is no post data, either username or password is missing, of if the scope conditions have not been met.
 *
 * @param CakeRequest $request The request that contains login information.
 * @param CakeResponse $response Unused response object.
 * @return mixed False on login failure. An array of User data on success.
 */
	public function authenticate(CakeRequest $request, CakeResponse $response) {
		//indicated name for the user model, can contain a plugin prefix
		$userModel = $this->settings['userModel'];
		//instantiate user model,: instance will be used for loging and database operations
		$userModelObj = ClassRegistry::init($userModel);
		$userModelObj->log('<Admin.authenticate>', 'debug');
		list(, $model) = pluginSplit($userModel);
		
		$result = array();
		$user = array();
		
		try{
			if(!$request->isPost()){
				$userModelObj->log("request is not post, returning false", 'debug');
				return false;
			}
			$userModelObj->log("request:=" . var_export($request->data, true), 'debug');
			$fields = $this->settings['fields'];
			$userModelObj->log('usernameField=>' . $fields['username'] . ';passwordField=>' . $fields['password'], 'debug');
			$userModelObj->log('username=>' . $request->data($model . '.' . $fields['username'] ) 
								. ';password=>' . $request->data($model . '.' . $fields['password']), 'debug');
			//check that all necessary data are present in the request
			if (!$this->_checkFields($request, $model, $fields)) {
				$error = "Check fields failed";
				throw new Exception($error);
			}
			$userModelObj->log('checkField successfull', 'debug');
			//get the login data from the request
			$email = $request->data($model . '.' . $fields['username'] ) ;
			$passwd = $request->data($model . '.' . $fields['password']);
			
			$userModelObj->log("email=$email,passwd=$passwd", 'debug');
		
			//check of a users exists with the specified email
			//NOTE: emails are case insensitive
			$fieldName = $fields['username'];
			$userModelObj->log("fieldName=${fieldName}", 'debug');
			$conditions = array("LOWER(User.{$fieldName})"=> strtolower($email));
			
			$result = ClassRegistry::init($userModel)->find('first', array(
						'conditions' => $conditions));
			if(!$result){
				$error = __('Incorrect user name or password');
				throw new Exception($error);
			}
			//a user exists
			$userModelObj->log("result=" . var_export($result, true), 'debug');
			$user = $result[$model];
			
			$dependents = $userModelObj->hasOne;
			$userModelObj->log("dependents=" . var_export($dependents, true), 'debug');
			foreach($dependents as $dependentAlias => $dependent){
				$dependentObj = $result[$dependentAlias];
				if($dependentObj['id']){
					if(isset($dependentObj['id'])) 
						unset($dependentObj['id']);
					$user = array_merge($user, $dependentObj);
				}
			}
			$userModelObj->log("user=" . var_export($user, true), 'debug');
			
			//$userModelObj->log('found user:' . $user[$fields['username']], 'debug');
			//$userModelObj->log('found passwd hash:' . $user[$fields['password']], 'debug');
			
			//check that password digests match
			if(md5($passwd) !== $user[$fields['password']]){
				$error = __('Incorrect user name or password');
				throw new Exception($error);
			}
		
			//make sure that the user has been approved by admins
			if(!isset($user['usr_is_active']) || !$user['usr_is_active']){
				$error = __('Your account has not yet been approved');
				throw new Exception($error);
			}
		
			//user is valid, update last login date
			$userModelObj->log('user logged in sucessfully', 'debug');
			$userModelObj->log('Updating user last login date ...', 'debug');
			$date = new DateTime();
			$now = $date->getTimestamp();
			$user['usr_last_connect_date'] = $now;
			$userModelObj->id = $user['id'];
			if(!$userModelObj->read()){
				$error = __('Unable to read user data');
				throw new Exception($error);
			}
			$userModelObj->data['User']['usr_last_connect_date'] = $now;
			if(!$userModelObj->save($userModelObj->data['User'])){
				$error = __('Unable to update user last login date');
				throw new Exception($error);
			}
			$userModelObj->log('User last login date is updated', 'debug');
			$error = '';
			//reset Session object
			CakeSession::destroy();
			CakeSession::write("User.id", $user['id']);
			$userModelObj->log('User session data are updated', 'debug');
			
			unset($result[$model]);
		}catch(Exception $e){
			$userModelObj->log('validationErrors=' . var_export($userModelObj->validationErrors, true), 'error');
			$userModelObj->log($e->getMessage());
			throw $e;
			//return false;
		}
		$userModelObj->log('</Admin.authenticate>', 'debug');
		return array_merge($user, $result);
	}

}
