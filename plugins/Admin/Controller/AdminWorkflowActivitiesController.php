<?php
App::uses('AdminAppController', 'Admin.Controller');
/**
 * AdminWorkflowActivitiesController
 *
 * @property WorkflowActivity $WorkflowActivity
 */
class AdminWorkflowActivitiesController extends AdminAppController {

	public $uses = array('WorkflowActivity');

    public function index() {
	    $conditions = array();
        $workflowActivitiesTableURL = array('controller' => 'admin_workflow_activities', 'action' => 'index');

        //join get query & named params
        $params = array_merge($this->request->params['named']);
        foreach($this->request->query as $key => $value) $params[$key] = $value;

        foreach($params as $key => $value) {
            $split = explode('-', $key);
            $modelName = (sizeof($split) > 1) ? $split[0] : 'WorkflowActivity';
            $property = (sizeof($split) > 1) ? $split[1] : $key;
            if($modelName == 'WorkflowActivity' || !empty($this->WorkflowActivity->belongsTo[$modelName])) {
                $this->loadModel($modelName);
                $modelObj = new $modelName();
                if(!empty($modelObj)) {
                    $columnType = $modelObj->getColumnType($property);
                    if(!empty($columnType)){
                        //add it to url
                        $workflowActivitiesTableURL[$key] = $value;
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

		$this->WorkflowActivity->recursive = 0;
		$this->set('workflowActivities', $this->Paginator->paginate('WorkflowActivity', $conditions, array()));
		$this->set('workflowActivitiesTableURL', $workflowActivitiesTableURL);
		//render as local table if it is an ajax request
        if($this->request->is('ajax'))
        {
            $this->render('table');
        }
	}
    
	public function view($id = null) {
		$this->WorkflowActivity->id = $id;
		if (!$this->WorkflowActivity->exists()) {
			throw new NotFoundException(__('Invalid workflow activity'));
		}
        $workflowActivity = $this->WorkflowActivity->read(null, $id);
		$this->set('workflowActivity', $workflowActivity);
		//related workflowActions
		$this->WorkflowActivity->WorkflowAction->recursive = 0;
		$this->paginate = array('conditions' => array('WorkflowActivity.id' => $id), 'limit' => 15);
		$this->set('workflowActions', $this->Paginator->paginate('WorkflowAction'));
		$this->set('workflowActionsTableURL', array('controller' => 'admin_workflow_actions', 'action' => 'index', 'WorkflowActivity-id' => $id));
	}
	
	public function add() {
		if ($this->request->is('post')) {
			$this->WorkflowActivity->create();
			if ($this->WorkflowActivity->save($this->request->data)) {
				$this->Session->setFlash(__('The workflow activity has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->WorkflowActivity->id));
			} else {
				$this->Session->setFlash(__('The workflow activity could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            //add the named params as data
            foreach($this->request->params['named'] as $param => $value) {
                $columnType = $this->WorkflowActivity->getColumnType($param);
                if(!empty($columnType)) {
                    if(empty($this->request->data['WorkflowActivity'])) $this->request->data['WorkflowActivity'] = array();
                    $this->request->data['WorkflowActivity'][$param] = $value;
                }
            }
        }
		$workflows = $this->WorkflowActivity->Workflow->find('list', array('order' => $this->WorkflowActivity->Workflow->displayField));
		$this->set(compact('workflows'));
	}
	
	public function edit($id = null) {
		$this->WorkflowActivity->id = $id;
		if (!$this->WorkflowActivity->exists()) {
			throw new NotFoundException(__('Invalid workflow activity'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->WorkflowActivity->save($this->request->data)) {
				$this->Session->setFlash(__('The workflow activity has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->WorkflowActivity->id));
			} else {
				$this->Session->setFlash(__('The workflow activity could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            $workflowActivity = $this->WorkflowActivity->read(null, $id);
			$this->request->data = $workflowActivity;
		}
		$workflows = $this->WorkflowActivity->Workflow->find('list', array('order' => $this->WorkflowActivity->Workflow->displayField));
		$this->set(compact('workflows'));
	}
	
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->WorkflowActivity->id = $id;
		if (!$this->WorkflowActivity->exists()) {
			throw new NotFoundException(__('Invalid workflow activity'));
		}
		if ($this->WorkflowActivity->delete()) {
			$this->Session->setFlash(__('Workflow activity deleted'), 'default', array(), 'good');
            $this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Workflow activity was not deleted'), 'default', array(), 'bad');
		$this->redirect(array('action' => 'index'));
	}

}
