<?php
App::uses('AdminAppController', 'Admin.Controller');
/**
 * AdminProjectMonitorsController
 *
 * @property ProjectMonitor $ProjectMonitor
 */
class AdminProjectMonitorsController extends AdminAppController {

	public $uses = array('ProjectMonitor');

    public function index() {
	    $conditions = array();
        $projectMonitorsTableURL = array('controller' => 'admin_project_monitors', 'action' => 'index');

        //join get query & named params
        $params = array_merge($this->request->params['named']);
        foreach($this->request->query as $key => $value) $params[$key] = $value;

        foreach($params as $key => $value) {
            $split = explode('-', $key);
            $modelName = (sizeof($split) > 1) ? $split[0] : 'ProjectMonitor';
            $property = (sizeof($split) > 1) ? $split[1] : $key;
            if($modelName == 'ProjectMonitor' || !empty($this->ProjectMonitor->belongsTo[$modelName])) {
                $this->loadModel($modelName);
                $modelObj = new $modelName();
                if(!empty($modelObj)) {
                    $columnType = $modelObj->getColumnType($property);
                    if(!empty($columnType)){
                        //add it to url
                        $projectMonitorsTableURL[$key] = $value;
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

		$this->ProjectMonitor->recursive = 0;
		$this->set('projectMonitors', $this->Paginator->paginate('ProjectMonitor', $conditions, array()));
		$this->set('projectMonitorsTableURL', $projectMonitorsTableURL);
		//render as local table if it is an ajax request
        if($this->request->is('ajax'))
        {
            $this->render('table');
        }
	}
    
	public function view($id = null) {
		$this->ProjectMonitor->id = $id;
		if (!$this->ProjectMonitor->exists()) {
			throw new NotFoundException(__('Invalid project monitor'));
		}
        $projectMonitor = $this->ProjectMonitor->read(null, $id);
		$this->set('projectMonitor', $projectMonitor);
	}
	
	public function add() {
		if ($this->request->is('post')) {
			$this->ProjectMonitor->create();
			if ($this->ProjectMonitor->save($this->request->data)) {
				$this->Session->setFlash(__('The project monitor has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->ProjectMonitor->id));
			} else {
				$this->Session->setFlash(__('The project monitor could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            //add the named params as data
            foreach($this->request->params['named'] as $param => $value) {
                $columnType = $this->ProjectMonitor->getColumnType($param);
                if(!empty($columnType)) {
                    if(empty($this->request->data['ProjectMonitor'])) $this->request->data['ProjectMonitor'] = array();
                    $this->request->data['ProjectMonitor'][$param] = $value;
                }
            }
        }
		$monitors = $this->ProjectMonitor->Monitor->find('list', array('order' => $this->ProjectMonitor->Monitor->displayField));
		$this->set(compact('monitors'));
	}
	
	public function edit($id = null) {
		$this->ProjectMonitor->id = $id;
		if (!$this->ProjectMonitor->exists()) {
			throw new NotFoundException(__('Invalid project monitor'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->ProjectMonitor->save($this->request->data)) {
				$this->Session->setFlash(__('The project monitor has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->ProjectMonitor->id));
			} else {
				$this->Session->setFlash(__('The project monitor could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            $projectMonitor = $this->ProjectMonitor->read(null, $id);
			$this->request->data = $projectMonitor;
		}
		$monitors = $this->ProjectMonitor->Monitor->find('list', array('order' => $this->ProjectMonitor->Monitor->displayField));
		$this->set(compact('monitors'));
	}
	
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->ProjectMonitor->id = $id;
		if (!$this->ProjectMonitor->exists()) {
			throw new NotFoundException(__('Invalid project monitor'));
		}
		if ($this->ProjectMonitor->delete()) {
			$this->Session->setFlash(__('Project monitor deleted'), 'default', array(), 'good');
            $this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Project monitor was not deleted'), 'default', array(), 'bad');
		$this->redirect(array('action' => 'index'));
	}

}
