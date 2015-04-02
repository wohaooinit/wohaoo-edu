<?php
App::uses('AdminAppController', 'Admin.Controller');
/**
 * AdminWorkflowActionsController
 *
 * @property WorkflowAction $WorkflowAction
 */
class AdminWorkflowActionsController extends AdminAppController {

	public $uses = array('WorkflowAction');

    public function index() {
	    $conditions = array();
        $workflowActionsTableURL = array('controller' => 'admin_workflow_actions', 'action' => 'index');

        //join get query & named params
        $params = array_merge($this->request->params['named']);
        foreach($this->request->query as $key => $value) $params[$key] = $value;

        foreach($params as $key => $value) {
            $split = explode('-', $key);
            $modelName = (sizeof($split) > 1) ? $split[0] : 'WorkflowAction';
            $property = (sizeof($split) > 1) ? $split[1] : $key;
            if($modelName == 'WorkflowAction' || !empty($this->WorkflowAction->belongsTo[$modelName])) {
                $this->loadModel($modelName);
                $modelObj = new $modelName();
                if(!empty($modelObj)) {
                    $columnType = $modelObj->getColumnType($property);
                    if(!empty($columnType)){
                        //add it to url
                        $workflowActionsTableURL[$key] = $value;
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

		$this->WorkflowAction->recursive = 0;
		$this->set('workflowActions', $this->Paginator->paginate('WorkflowAction', $conditions, array()));
		$this->set('workflowActionsTableURL', $workflowActionsTableURL);
		//render as local table if it is an ajax request
        if($this->request->is('ajax'))
        {
            $this->render('table');
        }
	}
    
	public function view($id = null) {
		$this->WorkflowAction->id = $id;
		if (!$this->WorkflowAction->exists()) {
			throw new NotFoundException(__('Invalid workflow action'));
		}
        $workflowAction = $this->WorkflowAction->read(null, $id);
		$this->set('workflowAction', $workflowAction);
	}
	
	public function add() {
		if ($this->request->is('post')) {
			$this->WorkflowAction->create();
			if ($this->WorkflowAction->save($this->request->data)) {
				$this->Session->setFlash(__('The workflow action has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->WorkflowAction->id));
			} else {
				$this->Session->setFlash(__('The workflow action could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            //add the named params as data
            foreach($this->request->params['named'] as $param => $value) {
                $columnType = $this->WorkflowAction->getColumnType($param);
                if(!empty($columnType)) {
                    if(empty($this->request->data['WorkflowAction'])) $this->request->data['WorkflowAction'] = array();
                    $this->request->data['WorkflowAction'][$param] = $value;
                }
            }
        }
		$workflowActivities = $this->WorkflowAction->WorkflowActivity->find('list', array('order' => $this->WorkflowAction->WorkflowActivity->displayField));
		$this->set(compact('workflowActivities'));
	}
	
	public function edit($id = null) {
		$this->WorkflowAction->id = $id;
		if (!$this->WorkflowAction->exists()) {
			throw new NotFoundException(__('Invalid workflow action'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->WorkflowAction->save($this->request->data)) {
				$this->Session->setFlash(__('The workflow action has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->WorkflowAction->id));
			} else {
				$this->Session->setFlash(__('The workflow action could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            $workflowAction = $this->WorkflowAction->read(null, $id);
			$this->request->data = $workflowAction;
		}
		$workflowActivities = $this->WorkflowAction->WorkflowActivity->find('list', array('order' => $this->WorkflowAction->WorkflowActivity->displayField));
		$this->set(compact('workflowActivities'));
	}
	
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->WorkflowAction->id = $id;
		if (!$this->WorkflowAction->exists()) {
			throw new NotFoundException(__('Invalid workflow action'));
		}
		if ($this->WorkflowAction->delete()) {
			$this->Session->setFlash(__('Workflow action deleted'), 'default', array(), 'good');
            $this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Workflow action was not deleted'), 'default', array(), 'bad');
		$this->redirect(array('action' => 'index'));
	}

}
