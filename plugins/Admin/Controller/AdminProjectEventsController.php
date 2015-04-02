<?php
App::uses('AdminAppController', 'Admin.Controller');
/**
 * AdminProjectEventsController
 *
 * @property ProjectEvent $ProjectEvent
 */
class AdminProjectEventsController extends AdminAppController {

	public $uses = array('ProjectEvent');

    public function index() {
	    $conditions = array();
        $projectEventsTableURL = array('controller' => 'admin_project_events', 'action' => 'index');

        //join get query & named params
        $params = array_merge($this->request->params['named']);
        foreach($this->request->query as $key => $value) $params[$key] = $value;

        foreach($params as $key => $value) {
            $split = explode('-', $key);
            $modelName = (sizeof($split) > 1) ? $split[0] : 'ProjectEvent';
            $property = (sizeof($split) > 1) ? $split[1] : $key;
            if($modelName == 'ProjectEvent' || !empty($this->ProjectEvent->belongsTo[$modelName])) {
                $this->loadModel($modelName);
                $modelObj = new $modelName();
                if(!empty($modelObj)) {
                    $columnType = $modelObj->getColumnType($property);
                    if(!empty($columnType)){
                        //add it to url
                        $projectEventsTableURL[$key] = $value;
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

		$this->ProjectEvent->recursive = 0;
		$this->set('projectEvents', $this->Paginator->paginate('ProjectEvent', $conditions, array()));
		$this->set('projectEventsTableURL', $projectEventsTableURL);
		//render as local table if it is an ajax request
        if($this->request->is('ajax'))
        {
            $this->render('table');
        }
	}
    
	public function view($id = null) {
		$this->ProjectEvent->id = $id;
		if (!$this->ProjectEvent->exists()) {
			throw new NotFoundException(__('Invalid project event'));
		}
        $projectEvent = $this->ProjectEvent->read(null, $id);
		$this->set('projectEvent', $projectEvent);
	}
	
	public function add() {
		if ($this->request->is('post')) {
			$this->ProjectEvent->create();
			if ($this->ProjectEvent->save($this->request->data)) {
				$this->Session->setFlash(__('The project event has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->ProjectEvent->id));
			} else {
				$this->Session->setFlash(__('The project event could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            //add the named params as data
            foreach($this->request->params['named'] as $param => $value) {
                $columnType = $this->ProjectEvent->getColumnType($param);
                if(!empty($columnType)) {
                    if(empty($this->request->data['ProjectEvent'])) $this->request->data['ProjectEvent'] = array();
                    $this->request->data['ProjectEvent'][$param] = $value;
                }
            }
        }
	}
	
	public function edit($id = null) {
		$this->ProjectEvent->id = $id;
		if (!$this->ProjectEvent->exists()) {
			throw new NotFoundException(__('Invalid project event'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->ProjectEvent->save($this->request->data)) {
				$this->Session->setFlash(__('The project event has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->ProjectEvent->id));
			} else {
				$this->Session->setFlash(__('The project event could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            $projectEvent = $this->ProjectEvent->read(null, $id);
			$this->request->data = $projectEvent;
		}
	}
	
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->ProjectEvent->id = $id;
		if (!$this->ProjectEvent->exists()) {
			throw new NotFoundException(__('Invalid project event'));
		}
		if ($this->ProjectEvent->delete()) {
			$this->Session->setFlash(__('Project event deleted'), 'default', array(), 'good');
            $this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Project event was not deleted'), 'default', array(), 'bad');
		$this->redirect(array('action' => 'index'));
	}

}
