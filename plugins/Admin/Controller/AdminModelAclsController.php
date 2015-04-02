<?php
App::uses('AdminAppController', 'Admin.Controller');
/**
 * AdminModelAclsController
 *
 * @property ModelAcl $ModelAcl
 */
class AdminModelAclsController extends AdminAppController {

	public $uses = array('ModelAcl');

    public function index() {
	    $conditions = array();
        $modelAclsTableURL = array('controller' => 'admin_model_acls', 'action' => 'index');

        //join get query & named params
        $params = array_merge($this->request->params['named']);
        foreach($this->request->query as $key => $value) $params[$key] = $value;

        foreach($params as $key => $value) {
            $split = explode('-', $key);
            $modelName = (sizeof($split) > 1) ? $split[0] : 'ModelAcl';
            $property = (sizeof($split) > 1) ? $split[1] : $key;
            if($modelName == 'ModelAcl' || !empty($this->ModelAcl->belongsTo[$modelName])) {
                $this->loadModel($modelName);
                $modelObj = new $modelName();
                if(!empty($modelObj)) {
                    $columnType = $modelObj->getColumnType($property);
                    if(!empty($columnType)){
                        //add it to url
                        $modelAclsTableURL[$key] = $value;
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

		$this->ModelAcl->recursive = 0;
		$this->set('modelAcls', $this->Paginator->paginate('ModelAcl', $conditions, array()));
		$this->set('modelAclsTableURL', $modelAclsTableURL);
		//render as local table if it is an ajax request
        if($this->request->is('ajax'))
        {
            $this->render('table');
        }
	}
    
	public function view($id = null) {
		$this->ModelAcl->id = $id;
		if (!$this->ModelAcl->exists()) {
			throw new NotFoundException(__('Invalid model acl'));
		}
        $modelAcl = $this->ModelAcl->read(null, $id);
		$this->set('modelAcl', $modelAcl);
	}
	
	public function add() {
		if ($this->request->is('post')) {
			$this->ModelAcl->create();
			if ($this->ModelAcl->save($this->request->data)) {
				$this->Session->setFlash(__('The model acl has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->ModelAcl->id));
			} else {
				$this->Session->setFlash(__('The model acl could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            //add the named params as data
            foreach($this->request->params['named'] as $param => $value) {
                $columnType = $this->ModelAcl->getColumnType($param);
                if(!empty($columnType)) {
                    if(empty($this->request->data['ModelAcl'])) $this->request->data['ModelAcl'] = array();
                    $this->request->data['ModelAcl'][$param] = $value;
                }
            }
        }
	}
	
	public function edit($id = null) {
		$this->ModelAcl->id = $id;
		if (!$this->ModelAcl->exists()) {
			throw new NotFoundException(__('Invalid model acl'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->ModelAcl->save($this->request->data)) {
				$this->Session->setFlash(__('The model acl has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->ModelAcl->id));
			} else {
				$this->Session->setFlash(__('The model acl could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            $modelAcl = $this->ModelAcl->read(null, $id);
			$this->request->data = $modelAcl;
		}
	}
	
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->ModelAcl->id = $id;
		if (!$this->ModelAcl->exists()) {
			throw new NotFoundException(__('Invalid model acl'));
		}
		if ($this->ModelAcl->delete()) {
			$this->Session->setFlash(__('Model acl deleted'), 'default', array(), 'good');
            $this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Model acl was not deleted'), 'default', array(), 'bad');
		$this->redirect(array('action' => 'index'));
	}

}
