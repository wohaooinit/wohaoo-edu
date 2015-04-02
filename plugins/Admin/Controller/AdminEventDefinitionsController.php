<?php
App::uses('AdminAppController', 'Admin.Controller');
/**
 * AdminEventDefinitionsController
 *
 * @property EventDefinition $EventDefinition
 */
class AdminEventDefinitionsController extends AdminAppController {

	public $uses = array('EventDefinition');

    public function index() {
	    $conditions = array();
        $eventDefinitionsTableURL = array('controller' => 'admin_event_definitions', 'action' => 'index');

        //join get query & named params
        $params = array_merge($this->request->params['named']);
        foreach($this->request->query as $key => $value) $params[$key] = $value;

        foreach($params as $key => $value) {
            $split = explode('-', $key);
            $modelName = (sizeof($split) > 1) ? $split[0] : 'EventDefinition';
            $property = (sizeof($split) > 1) ? $split[1] : $key;
            if($modelName == 'EventDefinition' || !empty($this->EventDefinition->belongsTo[$modelName])) {
                $this->loadModel($modelName);
                $modelObj = new $modelName();
                if(!empty($modelObj)) {
                    $columnType = $modelObj->getColumnType($property);
                    if(!empty($columnType)){
                        //add it to url
                        $eventDefinitionsTableURL[$key] = $value;
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

		$this->EventDefinition->recursive = 0;
		$this->set('eventDefinitions', $this->Paginator->paginate('EventDefinition', $conditions, array()));
		$this->set('eventDefinitionsTableURL', $eventDefinitionsTableURL);
		//render as local table if it is an ajax request
        if($this->request->is('ajax'))
        {
            $this->render('table');
        }
	}
    
	public function view($id = null) {
		$this->EventDefinition->id = $id;
		if (!$this->EventDefinition->exists()) {
			throw new NotFoundException(__('Invalid event definition'));
		}
        $eventDefinition = $this->EventDefinition->read(null, $id);
		$this->set('eventDefinition', $eventDefinition);
	}
	
	public function add() {
		if ($this->request->is('post')) {
			$this->EventDefinition->create();
			if ($this->EventDefinition->save($this->request->data)) {
				$this->Session->setFlash(__('The event definition has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->EventDefinition->id));
			} else {
				$this->Session->setFlash(__('The event definition could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            //add the named params as data
            foreach($this->request->params['named'] as $param => $value) {
                $columnType = $this->EventDefinition->getColumnType($param);
                if(!empty($columnType)) {
                    if(empty($this->request->data['EventDefinition'])) $this->request->data['EventDefinition'] = array();
                    $this->request->data['EventDefinition'][$param] = $value;
                }
            }
        }
	}
	
	public function edit($id = null) {
		$this->EventDefinition->id = $id;
		if (!$this->EventDefinition->exists()) {
			throw new NotFoundException(__('Invalid event definition'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->EventDefinition->save($this->request->data)) {
				$this->Session->setFlash(__('The event definition has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->EventDefinition->id));
			} else {
				$this->Session->setFlash(__('The event definition could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            $eventDefinition = $this->EventDefinition->read(null, $id);
			$this->request->data = $eventDefinition;
		}
	}
	
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->EventDefinition->id = $id;
		if (!$this->EventDefinition->exists()) {
			throw new NotFoundException(__('Invalid event definition'));
		}
		if ($this->EventDefinition->delete()) {
			$this->Session->setFlash(__('Event definition deleted'), 'default', array(), 'good');
            $this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Event definition was not deleted'), 'default', array(), 'bad');
		$this->redirect(array('action' => 'index'));
	}

}
