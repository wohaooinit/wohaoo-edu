<?php
App::uses('AdminAppController', 'Admin.Controller');
/**
 * AdminMonitorsController
 *
 * @property Monitor $Monitor
 */
class AdminMonitorsController extends AdminAppController {

	public $uses = array('Monitor');

    public function index() {
	    $conditions = array();
        $monitorsTableURL = array('controller' => 'admin_monitors', 'action' => 'index');

        //join get query & named params
        $params = array_merge($this->request->params['named']);
        foreach($this->request->query as $key => $value) $params[$key] = $value;

        foreach($params as $key => $value) {
            $split = explode('-', $key);
            $modelName = (sizeof($split) > 1) ? $split[0] : 'Monitor';
            $property = (sizeof($split) > 1) ? $split[1] : $key;
            if($modelName == 'Monitor' || !empty($this->Monitor->belongsTo[$modelName])) {
                $this->loadModel($modelName);
                $modelObj = new $modelName();
                if(!empty($modelObj)) {
                    $columnType = $modelObj->getColumnType($property);
                    if(!empty($columnType)){
                        //add it to url
                        $monitorsTableURL[$key] = $value;
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

		$this->Monitor->recursive = 0;
		$this->set('monitors', $this->Paginator->paginate('Monitor', $conditions, array()));
		$this->set('monitorsTableURL', $monitorsTableURL);
		//render as local table if it is an ajax request
        if($this->request->is('ajax'))
        {
            $this->render('table');
        }
	}
    
	public function view($id = null) {
		$this->Monitor->id = $id;
		if (!$this->Monitor->exists()) {
			throw new NotFoundException(__('Invalid monitor'));
		}
        $monitor = $this->Monitor->read(null, $id);
		$this->set('monitor', $monitor);
	}
	
	public function add() {
		if ($this->request->is('post')) {
			$this->Monitor->create();
			if ($this->Monitor->save($this->request->data)) {
				$this->Session->setFlash(__('The monitor has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->Monitor->id));
			} else {
				$this->Session->setFlash(__('The monitor could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            //add the named params as data
            foreach($this->request->params['named'] as $param => $value) {
                $columnType = $this->Monitor->getColumnType($param);
                if(!empty($columnType)) {
                    if(empty($this->request->data['Monitor'])) $this->request->data['Monitor'] = array();
                    $this->request->data['Monitor'][$param] = $value;
                }
            }
        }
		$users = $this->Monitor->User->find('list', array('order' => $this->Monitor->User->displayField));
		$countries = $this->Monitor->Country->find('list', array('order' => $this->Monitor->Country->displayField));
		$this->set(compact('users', 'countries'));
	}
	
	public function edit($id = null) {
		$this->Monitor->id = $id;
		if (!$this->Monitor->exists()) {
			throw new NotFoundException(__('Invalid monitor'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Monitor->save($this->request->data)) {
				$this->Session->setFlash(__('The monitor has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->Monitor->id));
			} else {
				$this->Session->setFlash(__('The monitor could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            $monitor = $this->Monitor->read(null, $id);
			$this->request->data = $monitor;
		}
		$users = $this->Monitor->User->find('list', array('order' => $this->Monitor->User->displayField));
		$countries = $this->Monitor->Country->find('list', array('order' => $this->Monitor->Country->displayField));
		$this->set(compact('users', 'countries'));
	}
	
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Monitor->id = $id;
		if (!$this->Monitor->exists()) {
			throw new NotFoundException(__('Invalid monitor'));
		}
		if ($this->Monitor->delete()) {
			$this->Session->setFlash(__('Monitor deleted'), 'default', array(), 'good');
            $this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Monitor was not deleted'), 'default', array(), 'bad');
		$this->redirect(array('action' => 'index'));
	}

}
