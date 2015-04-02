<?php
App::uses('AdminAppController', 'Admin.Controller');
/**
 * AdminConfigsController
 *
 * @property Config $Config
 */
class AdminConfigsController extends AdminAppController {

	public $uses = array('Config');

    public function index() {
	    $conditions = array();
        $configsTableURL = array('controller' => 'admin_configs', 'action' => 'index');

        //join get query & named params
        $params = array_merge($this->request->params['named']);
        foreach($this->request->query as $key => $value) $params[$key] = $value;

        foreach($params as $key => $value) {
            $split = explode('-', $key);
            $modelName = (sizeof($split) > 1) ? $split[0] : 'Config';
            $property = (sizeof($split) > 1) ? $split[1] : $key;
            if($modelName == 'Config' || !empty($this->Config->belongsTo[$modelName])) {
                $this->loadModel($modelName);
                $modelObj = new $modelName();
                if(!empty($modelObj)) {
                    $columnType = $modelObj->getColumnType($property);
                    if(!empty($columnType)){
                        //add it to url
                        $configsTableURL[$key] = $value;
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

		$this->Config->recursive = 0;
		$this->set('configs', $this->Paginator->paginate('Config', $conditions, array()));
		$this->set('configsTableURL', $configsTableURL);
		//render as local table if it is an ajax request
        if($this->request->is('ajax'))
        {
            $this->render('table');
        }
	}
    
	public function view($id = null) {
		$this->Config->id = $id;
		if (!$this->Config->exists()) {
			throw new NotFoundException(__('Invalid config'));
		}
        $config = $this->Config->read(null, $id);
		$this->set('config', $config);
	}
	
	public function add() {
		if ($this->request->is('post')) {
			$this->Config->create();
			if ($this->Config->save($this->request->data)) {
				$this->Session->setFlash(__('The config has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->Config->id));
			} else {
				$this->Session->setFlash(__('The config could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            //add the named params as data
            foreach($this->request->params['named'] as $param => $value) {
                $columnType = $this->Config->getColumnType($param);
                if(!empty($columnType)) {
                    if(empty($this->request->data['Config'])) $this->request->data['Config'] = array();
                    $this->request->data['Config'][$param] = $value;
                }
            }
        }
	}
	
	public function edit($id = null) {
		$this->Config->id = $id;
		if (!$this->Config->exists()) {
			throw new NotFoundException(__('Invalid config'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Config->save($this->request->data)) {
				$this->Session->setFlash(__('The config has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->Config->id));
			} else {
				$this->Session->setFlash(__('The config could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            $config = $this->Config->read(null, $id);
			$this->request->data = $config;
		}
	}
	
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Config->id = $id;
		if (!$this->Config->exists()) {
			throw new NotFoundException(__('Invalid config'));
		}
		if ($this->Config->delete()) {
			$this->Session->setFlash(__('Config deleted'), 'default', array(), 'good');
            $this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Config was not deleted'), 'default', array(), 'bad');
		$this->redirect(array('action' => 'index'));
	}

}
