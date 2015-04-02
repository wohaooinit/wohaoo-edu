<?php

App::uses('AdminAppController', 'Admin.Controller');
/**
 * AdminStudentsController
 *
 * @property Student $Student
 */
class AdminStudentsController extends AdminAppController {

	public $uses = array('Student');

    public function index() {
	    $conditions = array();
        $studentsTableURL = array('controller' => 'admin_students', 'action' => 'index');

        //join get query & named params
        $params = array_merge($this->request->params['named']);
        foreach($this->request->query as $key => $value) $params[$key] = $value;

        foreach($params as $key => $value) {
            $split = explode('-', $key);
            $modelName = (sizeof($split) > 1) ? $split[0] : 'Student';
            $property = (sizeof($split) > 1) ? $split[1] : $key;
            if($modelName == 'Student' || !empty($this->Student->belongsTo[$modelName])) {
                $this->lostudentModel($modelName);
                $modelObj = new $modelName();
                if(!empty($modelObj)) {
                    $columnType = $modelObj->getColumnType($property);
                    if(!empty($columnType)){
                        //studentd it to url
                        $studentsTableURL[$key] = $value;
                        //studentd it to conditions
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

		$this->Student->recursive = 0;
		$this->set('students', $this->Paginator->paginate('Student', $conditions, array()));
		$this->set('studentsTableURL', $studentsTableURL);
		//render as local table if it is an ajax request
        if($this->request->is('ajax'))
        {
            $this->render('table');
        }
	}
    
	public function view($id = null) {
		$this->Student->id = $id;
		if (!$this->Student->exists()) {
			throw new NotFoundException(__('Invalid student'));
		}
        $student = $this->Student->read(null, $id);
		$this->set('student', $student);
	}
	
	public function add() {
		if ($this->request->is('post')) {
			$this->Student->create();
			if ($this->Student->save($this->request->data)) {
				$this->Session->setFlash(__('The student has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->Student->id));
			} else {
				$this->Session->setFlash(__('The student could not be saved. Please, try again.'), 'default', array(), 'bstudent');
			}
		} else {
            //studentd the named params as data
            foreach($this->request->params['named'] as $param => $value) {
                $columnType = $this->Student->getColumnType($param);
                if(!empty($columnType)) {
                    if(empty($this->request->data['Student'])) $this->request->data['Student'] = array();
                    $this->request->data['Student'][$param] = $value;
                }
            }
        }
	}
	
	public function edit($id = null) {
		$this->Student->id = $id;
		if (!$this->Student->exists()) {
			throw new NotFoundException(__('Invalid student'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Student->save($this->request->data)) {
				$this->Session->setFlash(__('The student has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->Student->id));
			} else {
				$this->Session->setFlash(__('The student could not be saved. Please, try again.'), 'default', array(), 'bstudent');
			}
		} else {
            $student = $this->Student->read(null, $id);
			$this->request->data = $student;
		}
	}
	
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Student->id = $id;
		if (!$this->Student->exists()) {
			throw new NotFoundException(__('Invalid student'));
		}
		if ($this->Student->delete()) {
			$this->Session->setFlash(__('Student deleted'), 'default', array(), 'good');
            $this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Student was not deleted'), 'default', array(), 'bstudent');
		$this->redirect(array('action' => 'index'));
	}

}
