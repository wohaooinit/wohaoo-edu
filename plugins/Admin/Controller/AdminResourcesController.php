<?php

App::uses('AdminAppController', 'Admin.Controller');
/**
 * AdminResourcesController
 *
 * @property Resource $Resource
 */
class AdminResourcesController extends AdminAppController {

	public $uses = array('Resource');

        public function index() {
	   	 $conditions = array();
		$resourcesTableURL = array('controller' => 'admin_resources', 'action' => 'index');

		//join get query & named params
		$params = array_merge($this->request->params['named']);
		foreach($this->request->query as $key => $value) $params[$key] = $value;

		foreach($params as $key => $value) {
			$split = explode('-', $key);
			$modelName = (sizeof($split) > 1) ? $split[0] : 'Resource';
			$property = (sizeof($split) > 1) ? $split[1] : $key;
			if($modelName == 'Resource' || !empty($this->Resource->belongsTo[$modelName])) {
				$this->loadModel($modelName);
				$modelObj = new $modelName();
				if(!empty($modelObj)) {
					$columnType = $modelObj->getColumnType($property);
					if(!empty($columnType)){
						//add it to url
						$resourcesTableURL[$key] = $value;
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

		$this->Resource->recursive = 0;
		$this->set('resources', $this->Paginator->paginate('Resource', $conditions, array()));
		$this->set('resourcesTableURL', $resourcesTableURL);
		//render as local table if it is an ajax request
		if($this->request->is('ajax'))
		{
			$this->render('table');
		}
	}
    
	public function view($id = null) {
		$this->log("<resources.view id=$id>", 'debug');
		$this->Resource->id = $id;
		if (!$this->Resource->exists()) {
			throw new NotFoundException(__('Invalid resource'));
		}
      		$resource = $this->Resource->read(null, $id);
		$this->set('resource', $resource);
		$this->Session->write('Url.referer', $this->referer());
		
		$this->log('</resources.view>', 'debug');
		
	}
	
	public function add($model = "", $model_id = 0) {
		if ($this->request->is('post')) {
			$this->Resource->create();
			
			$date = new DateTime();
			if(empty($this->request->data['Resource']['res_created']))
				$this->request->data['Resource']['res_created'] = $date->getTimestamp();
				
			if ($this->Resource->save($this->request->data)) {
				$this->Session->setFlash(__('The resource has been saved'), 'default', array(), 'good');
                		
                		if($this->Session->check('Url.referer'))
					$this->redirect($this->Session->read('Url.referer'));
                		$this->redirect($this->referer());
                		
			} else {
				$this->Session->setFlash(__('The resource could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
			//add the named params as data
			foreach($this->request->params['named'] as $param => $value) {
				$columnType = $this->Resource->getColumnType($param);
				if(!empty($columnType)) {
					if(empty($this->request->data['Resource'])) $this->request->data['Resource'] = array();
					$this->request->data['Resource'][$param] = $value;
				}
			}
			if(empty($this->request->data['Resource'])) $this->request->data['Resource'] = array();
			$this->request->data['Resource']['res_model_id'] = $model_id;
			$this->request->data['Resource']['res_model'] = $model;
			
			$this->Session->write('Url.referer', $this->referer());
		}
	}
	
	public function edit($id = null) {
		$this->Resource->id = $id;
		if (!$this->Resource->exists()) {
			throw new NotFoundException(__('Invalid resource'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Resource->save($this->request->data)) {
				$this->Session->setFlash(__('The resource has been saved'), 'default', array(), 'good');
               			 
               			 if($this->Session->check('Url.referer'))
					$this->redirect($this->Session->read('Url.referer'));
                		$this->redirect($this->referer());
                		
			} else {
				$this->Session->setFlash(__('The resource could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
           		 $resource = $this->Resource->read(null, $id);
			$this->request->data = $resource;
			
			$this->Session->write('Url.referer', $this->referer());
		}
	}
	
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Resource->id = $id;
		if (!$this->Resource->exists()) {
			throw new NotFoundException(__('Invalid resource'));
		}
		if ($this->Resource->delete()) {
			$this->Session->setFlash(__('Resource deleted'), 'default', array(), 'good');
            		$this->redirect($this->referer());
		}
		$this->Session->setFlash(__('Resource was not deleted'), 'default', array(), 'bad');
		$this->redirect($this->referer());
	}

}
