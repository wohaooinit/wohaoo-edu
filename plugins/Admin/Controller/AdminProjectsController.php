<?php
App::uses('AdminAppController', 'Admin.Controller');
/**
 * AdminProjectsController
 *
 * @property Project $Project
 */
class AdminProjectsController extends AdminAppController {

	public $uses = array('Project');

    public function index() {
	    $conditions = array();
        $projectsTableURL = array('controller' => 'admin_projects', 'action' => 'index');

        //join get query & named params
        $params = array_merge($this->request->params['named']);
        foreach($this->request->query as $key => $value) $params[$key] = $value;

        foreach($params as $key => $value) {
            $split = explode('-', $key);
            $modelName = (sizeof($split) > 1) ? $split[0] : 'Project';
            $property = (sizeof($split) > 1) ? $split[1] : $key;
            if($modelName == 'Project' || !empty($this->Project->belongsTo[$modelName])) {
                $this->loadModel($modelName);
                $modelObj = new $modelName();
                if(!empty($modelObj)) {
                    $columnType = $modelObj->getColumnType($property);
                    if(!empty($columnType)){
                        //add it to url
                        $projectsTableURL[$key] = $value;
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

		$this->Project->recursive = 0;
		$this->set('projects', $this->Paginator->paginate('Project', $conditions, array()));
		$this->set('projectsTableURL', $projectsTableURL);
		//render as local table if it is an ajax request
        if($this->request->is('ajax'))
        {
            $this->render('table');
        }
	}
    
	public function view($id = null) {
		$this->Project->id = $id;
		if (!$this->Project->exists()) {
			throw new NotFoundException(__('Invalid project'));
		}
        $project = $this->Project->read(null, $id);
		$this->set('project', $project);
		//related projectEvents
		$this->Project->ProjectEvent->recursive = 0;
		$this->paginate = array('conditions' => array('Project.id' => $id), 'limit' => 15);
		$this->set('projectEvents', $this->Paginator->paginate('ProjectEvent'));
		$this->set('projectEventsTableURL', array('controller' => 'admin_project_events', 'action' => 'index', 'Project-id' => $id));
		//related projectStatuses
		$this->Project->ProjectStatus->recursive = 0;
		$this->paginate = array('conditions' => array('Project.id' => $id), 'limit' => 15);
		$this->set('projectStatuses', $this->Paginator->paginate('ProjectStatus'));
		$this->set('projectStatusesTableURL', array('controller' => 'admin_project_statuses', 'action' => 'index', 'Project-id' => $id));
	}
	
	public function add() {
		if ($this->request->is('post')) {
			$this->Project->create();
			if ($this->Project->save($this->request->data)) {
				$this->Session->setFlash(__('The project has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->Project->id));
			} else {
				$this->Session->setFlash(__('The project could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            //add the named params as data
            foreach($this->request->params['named'] as $param => $value) {
                $columnType = $this->Project->getColumnType($param);
                if(!empty($columnType)) {
                    if(empty($this->request->data['Project'])) $this->request->data['Project'] = array();
                    $this->request->data['Project'][$param] = $value;
                }
            }
        }
		$creators = $this->Project->Creator->find('list', array('order' => $this->Project->Creator->displayField));
		$categories = $this->Project->Category->find('list', array('order' => $this->Project->Category->displayField));
		$resources = $this->Project->Resource->find('list', array('order' => $this->Project->Resource->displayField));
		$countries = $this->Project->Country->find('list', array('order' => $this->Project->Country->displayField));
		$this->set(compact('creators', 'categories', 'resources', 'countries'));
	}
	
	public function edit($id = null) {
		$this->Project->id = $id;
		if (!$this->Project->exists()) {
			throw new NotFoundException(__('Invalid project'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Project->save($this->request->data)) {
				$this->Session->setFlash(__('The project has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->Project->id));
			} else {
				$this->Session->setFlash(__('The project could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            $project = $this->Project->read(null, $id);
			$this->request->data = $project;
		}
		$creators = $this->Project->Creator->find('list', array('order' => $this->Project->Creator->displayField));
		$categories = $this->Project->Category->find('list', array('order' => $this->Project->Category->displayField));
		$resources = $this->Project->Resource->find('list', array('order' => $this->Project->Resource->displayField));
		$countries = $this->Project->Country->find('list', array('order' => $this->Project->Country->displayField));
		$this->set(compact('creators', 'categories', 'resources', 'countries'));
	}
	
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Project->id = $id;
		if (!$this->Project->exists()) {
			throw new NotFoundException(__('Invalid project'));
		}
		if ($this->Project->delete()) {
			$this->Session->setFlash(__('Project deleted'), 'default', array(), 'good');
            $this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Project was not deleted'), 'default', array(), 'bad');
		$this->redirect(array('action' => 'index'));
	}

}
