<?php

App::uses('AdminAppController', 'Admin.Controller');
/**
 * AdminTranslationsController
 *
 * @property Translation $Translation
 */
class AdminTranslationsController extends AdminAppController {

	public $uses = array('Translation');

    public function index() {
	    $conditions = array();
		$translationsTableURL = array('controller' => 'admin_translations', 'action' => 'index');

		//join get query & named params
		$params = array_merge($this->request->params['named']);
		foreach($this->request->query as $key => $value) $params[$key] = $value;

		foreach($params as $key => $value) {
			$split = explode('-', $key);
			$modelName = (sizeof($split) > 1) ? $split[0] : 'Translation';
			$property = (sizeof($split) > 1) ? $split[1] : $key;
			if($modelName == 'Translation' || !empty($this->Translation->belongsTo[$modelName])) {
				$this->loadModel($modelName);
				$modelObj = new $modelName();
				if(!empty($modelObj)) {
					$columnType = $modelObj->getColumnType($property);
					if(!empty($columnType)){
						//add it to url
						$translationsTableURL[$key] = $value;
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

		$this->Translation->recursive = 0;
		$this->set('translations', $this->Paginator->paginate('Translation', $conditions, array()));
		$this->set('translationsTableURL', $translationsTableURL);
		//render as local table if it is an ajax request
		if($this->request->is('ajax'))
		{
			$this->render('table');
		}
	}
    
	public function view($id = null) {
		$this->Translation->id = $id;
		if (!$this->Translation->exists()) {
			throw new NotFoundException(__('Invalid translation'));
		}
       		$translation = $this->Translation->read(null, $id);
		$this->set('translation', $translation);
	}
	
	public function edit($id = null) {
		$this->Translation->id = $id;
		
		try{
			if (!$this->Translation->exists()) {
				throw new NotFoundException(__('Invalid translation'));
			}
			if(!$this->Auth->isAuthorized()){
				$error = __("User is not Authorised");
				throw new Exception($error);
			}
			$this->loadModel('User');
			$user = $this->Auth->user();
			$this->User->id = $user['id'];
		
			$this->User->read();
		
			$personid = $this->User->data['UsrPerson']['id'];
		
			if ($this->request->is('post') || $this->request->is('put')) {
				if(!isset($this->request->data['Translation'])){
					$error = __("Invalid Request");
					throw new Exception($error);
				}
				$this->request->data['Translation']['t9n_trans_user_id'] = $personid ;
				$date = new DateTime();
				$now = $date->getTimestamp();
				$this->request->data['Translation']['t9n_modified'] = $now ;
			
				if ($this->Translation->save($this->request->data)) {
					$this->Session->setFlash(__('The translation has been saved'), 'default', array(), 'good');
							if($this->Session->check('Url.referer'))
						$this->redirect($this->Session->read('Url.referer'));
							$this->redirect($this->referer());
				} else {
					$this->Session->setFlash(__('The translation could not be saved. Please, try again.'), 'default', array(), 'bad');
				}
			} else {
						$translation = $this->Translation->read(null, $id);
				$this->request->data = $translation;
				$this->Session->write('Url.referer', $this->referer());
			}
		}catch(Exception $e){
			$this->Session->setFlash($e->getMessage(), 'default', array(), 'bad');
		}
	}
	
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Translation->id = $id;
		if (!$this->Translation->exists()) {
			throw new NotFoundException(__('Invalid translation'));
		}
		if ($this->Translation->delete()) {
			$this->Session->setFlash(__('Translation deleted'), 'default', array(), 'good');
           		 $this->redirect($this->referer());
		}
		$this->Session->setFlash(__('Translation was not deleted'), 'default', array(), 'bad');
		$this->redirect($this->referer());
	}

}
