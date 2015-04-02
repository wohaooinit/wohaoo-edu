<?php
App::uses('AdminAppController', 'Admin.Controller');
/**
 * AdminWorkflowJobsController
 *
 * @property WorkflowJob $WorkflowJob
 */
class AdminWorkflowJobsController extends AdminAppController {

	public $uses = array('WorkflowJob');

    public function index() {
	    $conditions = array();
        $workflowJobsTableURL = array('controller' => 'admin_workflow_jobs', 'action' => 'index');

        //join get query & named params
        $params = array_merge($this->request->params['named']);
        foreach($this->request->query as $key => $value) $params[$key] = $value;

        foreach($params as $key => $value) {
            $split = explode('-', $key);
            $modelName = (sizeof($split) > 1) ? $split[0] : 'WorkflowJob';
            $property = (sizeof($split) > 1) ? $split[1] : $key;
            if($modelName == 'WorkflowJob' || !empty($this->WorkflowJob->belongsTo[$modelName])) {
                $this->loadModel($modelName);
                $modelObj = new $modelName();
                if(!empty($modelObj)) {
                    $columnType = $modelObj->getColumnType($property);
                    if(!empty($columnType)){
                        //add it to url
                        $workflowJobsTableURL[$key] = $value;
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

		$this->WorkflowJob->recursive = 0;
		$this->set('workflowJobs', $this->Paginator->paginate('WorkflowJob', $conditions, array()));
		$this->set('workflowJobsTableURL', $workflowJobsTableURL);
		//render as local table if it is an ajax request
        if($this->request->is('ajax'))
        {
            $this->render('table');
        }
	}
    
	public function view($id = null) {
		$this->WorkflowJob->id = $id;
		if (!$this->WorkflowJob->exists()) {
			throw new NotFoundException(__('Invalid workflow job'));
		}
        $workflowJob = $this->WorkflowJob->read(null, $id);
		$this->set('workflowJob', $workflowJob);
	}
	
	public function add() {
		if ($this->request->is('post')) {
			$this->WorkflowJob->create();
			if ($this->WorkflowJob->save($this->request->data)) {
				$this->Session->setFlash(__('The workflow job has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->WorkflowJob->id));
			} else {
				$this->Session->setFlash(__('The workflow job could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            //add the named params as data
            foreach($this->request->params['named'] as $param => $value) {
                $columnType = $this->WorkflowJob->getColumnType($param);
                if(!empty($columnType)) {
                    if(empty($this->request->data['WorkflowJob'])) $this->request->data['WorkflowJob'] = array();
                    $this->request->data['WorkflowJob'][$param] = $value;
                }
            }
        }
	}
	
	public function edit($id = null) {
		$this->WorkflowJob->id = $id;
		if (!$this->WorkflowJob->exists()) {
			throw new NotFoundException(__('Invalid workflow job'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->WorkflowJob->save($this->request->data)) {
				$this->Session->setFlash(__('The workflow job has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->WorkflowJob->id));
			} else {
				$this->Session->setFlash(__('The workflow job could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            $workflowJob = $this->WorkflowJob->read(null, $id);
			$this->request->data = $workflowJob;
		}
	}
	
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->WorkflowJob->id = $id;
		if (!$this->WorkflowJob->exists()) {
			throw new NotFoundException(__('Invalid workflow job'));
		}
		if ($this->WorkflowJob->delete()) {
			$this->Session->setFlash(__('Workflow job deleted'), 'default', array(), 'good');
            $this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Workflow job was not deleted'), 'default', array(), 'bad');
		$this->redirect(array('action' => 'index'));
	}

}
