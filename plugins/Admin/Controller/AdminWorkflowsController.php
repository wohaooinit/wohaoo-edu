<?php
App::uses('AdminAppController', 'Admin.Controller');
/**
 * AdminWorkflowsController
 *
 * @property Workflow $Workflow
 */
class AdminWorkflowsController extends AdminAppController {

	public $uses = array('Workflow');

    public function index() {
	    $conditions = array();
        $workflowsTableURL = array('controller' => 'admin_workflows', 'action' => 'index');

        //join get query & named params
        $params = array_merge($this->request->params['named']);
        foreach($this->request->query as $key => $value) $params[$key] = $value;

        foreach($params as $key => $value) {
            $split = explode('-', $key);
            $modelName = (sizeof($split) > 1) ? $split[0] : 'Workflow';
            $property = (sizeof($split) > 1) ? $split[1] : $key;
            if($modelName == 'Workflow' || !empty($this->Workflow->belongsTo[$modelName])) {
                $this->loadModel($modelName);
                $modelObj = new $modelName();
                if(!empty($modelObj)) {
                    $columnType = $modelObj->getColumnType($property);
                    if(!empty($columnType)){
                        //add it to url
                        $workflowsTableURL[$key] = $value;
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

		$this->Workflow->recursive = 0;
		$this->set('workflows', $this->Paginator->paginate('Workflow', $conditions, array()));
		$this->set('workflowsTableURL', $workflowsTableURL);
		//render as local table if it is an ajax request
        if($this->request->is('ajax'))
        {
            $this->render('table');
        }
	}
    
	public function view($id = null) {
		$this->Workflow->id = $id;
		if (!$this->Workflow->exists()) {
			throw new NotFoundException(__('Invalid workflow'));
		}
        $workflow = $this->Workflow->read(null, $id);
		$this->set('workflow', $workflow);
		//related workflowActivities
		$this->Workflow->WorkflowActivity->recursive = 0;
		$this->paginate = array('conditions' => array('Workflow.id' => $id), 'limit' => 15);
		$this->set('workflowActivities', $this->Paginator->paginate('WorkflowActivity'));
		$this->set('workflowActivitiesTableURL', array('controller' => 'admin_workflow_activities', 'action' => 'index', 'Workflow-id' => $id));
	}
	
	public function add() {
		if ($this->request->is('post')) {
			$this->Workflow->create();
			if ($this->Workflow->save($this->request->data)) {
				$this->Session->setFlash(__('The workflow has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->Workflow->id));
			} else {
				$this->Session->setFlash(__('The workflow could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            //add the named params as data
            foreach($this->request->params['named'] as $param => $value) {
                $columnType = $this->Workflow->getColumnType($param);
                if(!empty($columnType)) {
                    if(empty($this->request->data['Workflow'])) $this->request->data['Workflow'] = array();
                    $this->request->data['Workflow'][$param] = $value;
                }
            }
        }
	}
	
	public function edit($id = null) {
		$this->Workflow->id = $id;
		if (!$this->Workflow->exists()) {
			throw new NotFoundException(__('Invalid workflow'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Workflow->save($this->request->data)) {
				$this->Session->setFlash(__('The workflow has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->Workflow->id));
			} else {
				$this->Session->setFlash(__('The workflow could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            $workflow = $this->Workflow->read(null, $id);
			$this->request->data = $workflow;
		}
	}
	
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Workflow->id = $id;
		if (!$this->Workflow->exists()) {
			throw new NotFoundException(__('Invalid workflow'));
		}
		if ($this->Workflow->delete()) {
			$this->Session->setFlash(__('Workflow deleted'), 'default', array(), 'good');
            $this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Workflow was not deleted'), 'default', array(), 'bad');
		$this->redirect(array('action' => 'index'));
	}

}
