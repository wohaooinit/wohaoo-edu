<?php
App::uses('AdminAppController', 'Admin.Controller');
/**
 * AdminProjectContributionsController
 *
 * @property ProjectContribution $ProjectContribution
 */
class AdminProjectContributionsController extends AdminAppController {

	public $uses = array('ProjectContribution');

    public function index() {
	    $conditions = array();
        $projectContributionsTableURL = array('controller' => 'admin_project_contributions', 'action' => 'index');

        //join get query & named params
        $params = array_merge($this->request->params['named']);
        foreach($this->request->query as $key => $value) $params[$key] = $value;

        foreach($params as $key => $value) {
            $split = explode('-', $key);
            $modelName = (sizeof($split) > 1) ? $split[0] : 'ProjectContribution';
            $property = (sizeof($split) > 1) ? $split[1] : $key;
            if($modelName == 'ProjectContribution' || !empty($this->ProjectContribution->belongsTo[$modelName])) {
                $this->loadModel($modelName);
                $modelObj = new $modelName();
                if(!empty($modelObj)) {
                    $columnType = $modelObj->getColumnType($property);
                    if(!empty($columnType)){
                        //add it to url
                        $projectContributionsTableURL[$key] = $value;
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

		$this->ProjectContribution->recursive = 0;
		$this->set('projectContributions', $this->Paginator->paginate('ProjectContribution', $conditions, array()));
		$this->set('projectContributionsTableURL', $projectContributionsTableURL);
		//render as local table if it is an ajax request
        if($this->request->is('ajax'))
        {
            $this->render('table');
        }
	}
    
	public function view($id = null) {
		$this->ProjectContribution->id = $id;
		if (!$this->ProjectContribution->exists()) {
			throw new NotFoundException(__('Invalid project contribution'));
		}
        $projectContribution = $this->ProjectContribution->read(null, $id);
		$this->set('projectContribution', $projectContribution);
	}
	
	public function add() {
		if ($this->request->is('post')) {
			$this->ProjectContribution->create();
			if ($this->ProjectContribution->save($this->request->data)) {
				$this->Session->setFlash(__('The project contribution has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->ProjectContribution->id));
			} else {
				$this->Session->setFlash(__('The project contribution could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            //add the named params as data
            foreach($this->request->params['named'] as $param => $value) {
                $columnType = $this->ProjectContribution->getColumnType($param);
                if(!empty($columnType)) {
                    if(empty($this->request->data['ProjectContribution'])) $this->request->data['ProjectContribution'] = array();
                    $this->request->data['ProjectContribution'][$param] = $value;
                }
            }
        }
	}
	
	public function edit($id = null) {
		$this->ProjectContribution->id = $id;
		if (!$this->ProjectContribution->exists()) {
			throw new NotFoundException(__('Invalid project contribution'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->ProjectContribution->save($this->request->data)) {
				$this->Session->setFlash(__('The project contribution has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->ProjectContribution->id));
			} else {
				$this->Session->setFlash(__('The project contribution could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            $projectContribution = $this->ProjectContribution->read(null, $id);
			$this->request->data = $projectContribution;
		}
	}
	
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->ProjectContribution->id = $id;
		if (!$this->ProjectContribution->exists()) {
			throw new NotFoundException(__('Invalid project contribution'));
		}
		if ($this->ProjectContribution->delete()) {
			$this->Session->setFlash(__('Project contribution deleted'), 'default', array(), 'good');
            $this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Project contribution was not deleted'), 'default', array(), 'bad');
		$this->redirect(array('action' => 'index'));
	}

}
