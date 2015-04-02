<?php
App::uses('AdminAppController', 'Admin.Controller');
/**
 * AdminEventsController
 *
 * @property Event $Event
 */
class AdminEventsController extends AdminAppController {

	public $uses = array('Event');

    public function index() {
	    $conditions = array();
        $eventsTableURL = array('controller' => 'admin_events', 'action' => 'index');

        //join get query & named params
        $params = array_merge($this->request->params['named']);
        foreach($this->request->query as $key => $value) $params[$key] = $value;

        foreach($params as $key => $value) {
            $split = explode('-', $key);
            $modelName = (sizeof($split) > 1) ? $split[0] : 'Event';
            $property = (sizeof($split) > 1) ? $split[1] : $key;
            if($modelName == 'Event' || !empty($this->Event->belongsTo[$modelName])) {
                $this->loadModel($modelName);
                $modelObj = new $modelName();
                if(!empty($modelObj)) {
                    $columnType = $modelObj->getColumnType($property);
                    if(!empty($columnType)){
                        //add it to url
                        $eventsTableURL[$key] = $value;
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

		$this->Event->recursive = 0;
		$this->set('events', $this->Paginator->paginate('Event', $conditions, array()));
		$this->set('eventsTableURL', $eventsTableURL);
		//render as local table if it is an ajax request
        if($this->request->is('ajax'))
        {
            $this->render('table');
        }
	}
    
	public function view($id = null) {
		$this->Event->id = $id;
		if (!$this->Event->exists()) {
			throw new NotFoundException(__('Invalid event'));
		}
        $event = $this->Event->read(null, $id);
		$this->set('event', $event);
	}
	
	public function add() {
		if ($this->request->is('post')) {
			$this->Event->create();
			if ($this->Event->save($this->request->data)) {
				$this->Session->setFlash(__('The event has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->Event->id));
			} else {
				$this->Session->setFlash(__('The event could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            //add the named params as data
            foreach($this->request->params['named'] as $param => $value) {
                $columnType = $this->Event->getColumnType($param);
                if(!empty($columnType)) {
                    if(empty($this->request->data['Event'])) $this->request->data['Event'] = array();
                    $this->request->data['Event'][$param] = $value;
                }
            }
        }
		$workflowActivities = $this->Event->WorkflowActivity->find('list', array('order' => $this->Event->WorkflowActivity->displayField));
		$this->set(compact('workflowActivities'));
	}
	
	public function edit($id = null) {
		$this->Event->id = $id;
		if (!$this->Event->exists()) {
			throw new NotFoundException(__('Invalid event'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Event->save($this->request->data)) {
				$this->Session->setFlash(__('The event has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->Event->id));
			} else {
				$this->Session->setFlash(__('The event could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            $event = $this->Event->read(null, $id);
			$this->request->data = $event;
		}
		$workflowActivities = $this->Event->WorkflowActivity->find('list', array('order' => $this->Event->WorkflowActivity->displayField));
		$this->set(compact('workflowActivities'));
	}
	
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Event->id = $id;
		if (!$this->Event->exists()) {
			throw new NotFoundException(__('Invalid event'));
		}
		if ($this->Event->delete()) {
			$this->Session->setFlash(__('Event deleted'), 'default', array(), 'good');
            $this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Event was not deleted'), 'default', array(), 'bad');
		$this->redirect(array('action' => 'index'));
	}

}
