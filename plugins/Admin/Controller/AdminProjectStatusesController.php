<?php
App::uses('AdminAppController', 'Admin.Controller');
/**
 * AdminProjectStatusesController
 *
 * @property ProjectStatus $ProjectStatus
 */
class AdminProjectStatusesController extends AdminAppController {

	public $uses = array('ProjectStatus');

    public function index() {
	    $conditions = array();
        $projectStatusesTableURL = array('controller' => 'admin_project_statuses', 'action' => 'index');

        //join get query & named params
        $params = array_merge($this->request->params['named']);
        foreach($this->request->query as $key => $value) $params[$key] = $value;

        foreach($params as $key => $value) {
            $split = explode('-', $key);
            $modelName = (sizeof($split) > 1) ? $split[0] : 'ProjectStatus';
            $property = (sizeof($split) > 1) ? $split[1] : $key;
            if($modelName == 'ProjectStatus' || !empty($this->ProjectStatus->belongsTo[$modelName])) {
                $this->loadModel($modelName);
                $modelObj = new $modelName();
                if(!empty($modelObj)) {
                    $columnType = $modelObj->getColumnType($property);
                    if(!empty($columnType)){
                        //add it to url
                        $projectStatusesTableURL[$key] = $value;
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

		$this->ProjectStatus->recursive = 0;
		$this->set('projectStatuses', $this->Paginator->paginate('ProjectStatus', $conditions, array()));
		$this->set('projectStatusesTableURL', $projectStatusesTableURL);
		//render as local table if it is an ajax request
        if($this->request->is('ajax'))
        {
            $this->render('table');
        }
	}
    
	public function view($id = null) {
		$this->ProjectStatus->id = $id;
		if (!$this->ProjectStatus->exists()) {
			throw new NotFoundException(__('Invalid project status'));
		}
        $projectStatus = $this->ProjectStatus->read(null, $id);
		$this->set('projectStatus', $projectStatus);
	}
	
	public function add() {
		if ($this->request->is('post')) {
			$this->ProjectStatus->create();
			if ($this->ProjectStatus->save($this->request->data)) {
				$this->Session->setFlash(__('The project status has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->ProjectStatus->id));
			} else {
				$this->Session->setFlash(__('The project status could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            //add the named params as data
            foreach($this->request->params['named'] as $param => $value) {
                $columnType = $this->ProjectStatus->getColumnType($param);
                if(!empty($columnType)) {
                    if(empty($this->request->data['ProjectStatus'])) $this->request->data['ProjectStatus'] = array();
                    $this->request->data['ProjectStatus'][$param] = $value;
                }
            }
        }
	}
	
	public function edit($id = null) {
		$this->ProjectStatus->id = $id;
		if (!$this->ProjectStatus->exists()) {
			throw new NotFoundException(__('Invalid project status'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->ProjectStatus->save($this->request->data)) {
				$this->Session->setFlash(__('The project status has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->ProjectStatus->id));
			} else {
				$this->Session->setFlash(__('The project status could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            $projectStatus = $this->ProjectStatus->read(null, $id);
			$this->request->data = $projectStatus;
		}
	}
	
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->ProjectStatus->id = $id;
		if (!$this->ProjectStatus->exists()) {
			throw new NotFoundException(__('Invalid project status'));
		}
		if ($this->ProjectStatus->delete()) {
			$this->Session->setFlash(__('Project status deleted'), 'default', array(), 'good');
            $this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Project status was not deleted'), 'default', array(), 'bad');
		$this->redirect(array('action' => 'index'));
	}

}
