<?php

App::uses('AdminAppController', 'Admin.Controller');
/**
 * AdminOptionsController
 *
 * @property Option $Option
 */
class AdminOptionsController extends AdminAppController {
	public $uses = array('Option');

    public function index() {
	    $conditions = array();
		$optionsTableURL = array('controller' => 'admin_options', 'action' => 'index');

		//join get query & named params
		$params = array_merge($this->request->params['named']);
		foreach($this->request->query as $key => $value) $params[$key] = $value;

		foreach($params as $key => $value) {
			$split = explode('-', $key);
			$modelName = (sizeof($split) > 1) ? $split[0] : 'Option';
			$property = (sizeof($split) > 1) ? $split[1] : $key;
			if($modelName == 'Option' || !empty($this->Option->belongsTo[$modelName])) {
				$this->loadModel($modelName);
				$modelObj = new $modelName();
				if(!empty($modelObj)) {
					$columnType = $modelObj->getColumnType($property);
					if(!empty($columnType)){
						//add it to url
						$optionsTableURL[$key] = $value;
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

		$this->Option->recursive = 0;
		$this->set('options', $this->Paginator->paginate('Option', $conditions, array()));
		$this->set('optionsTableURL', $optionsTableURL);
		//render as local table if it is an ajax request
		if($this->request->is('ajax'))
		{
			$this->render('table');
		}
	}
    
	public function view($id = null) {
		$this->Option->id = $id;
		if (!$this->Option->exists()) {
			throw new NotFoundException(__('Invalid option'));
		}
        	$option = $this->Option->read(null, $id);
		$this->set('option', $option);
	}
	
	public function add($assessment_id = 0) {
		$this->_loadParent("Assessment", $assessment_id);
		if ($this->request->is('post')) {
			$this->Option->create();
			if ($this->Option->save($this->request->data)) {
				$this->Session->setFlash(__('The option has been saved'), 'default', array(), 'good');
                		if($this->Session->check('Url.referer'))
					$this->redirect($this->Session->read('Url.referer'));
                		$this->redirect($this->referer());
			} else {
				$this->Session->setFlash(__('The option could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
			//add the named params as data
			foreach($this->request->params['named'] as $param => $value) {
				$columnType = $this->Option->getColumnType($param);
				if(!empty($columnType)) {
					if(empty($this->request->data['Option'])) $this->request->data['Option'] = array();
					$this->request->data['Option'][$param] = $value;
				}
			}
			$this->Session->write('Url.referer', $this->referer());
		}
	}
	
	public function edit($id = null) {
		$this->Option->id = $id;
		if (!$this->Option->exists()) {
			throw new NotFoundException(__('Invalid option'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Option->save($this->request->data)) {
				$this->Session->setFlash(__('The option has been saved'), 'default', array(), 'good');
                	
                	if($this->Session->check('Url.referer'))
				$this->redirect($this->Session->read('Url.referer'));
                	$this->redirect($this->referer());
		} else {
			$this->Session->setFlash(__('The option could not be saved. Please, try again.'), 'default', array(), 'bad');
		}
		} else {
            		$option = $this->Option->read(null, $id);
			$this->request->data = $option;
			$this->Session->write('Url.referer', $this->referer());
		}
	}
	
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Option->id = $id;
		if (!$this->Option->exists()) {
			throw new NotFoundException(__('Invalid option'));
		}
		if ($this->Option->delete()) {
			$this->Session->setFlash(__('Option deleted'), 'default', array(), 'good');
            		$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Option was not deleted'), 'default', array(), 'bad');
		$this->redirect(array('action' => 'index'));
	}

}
