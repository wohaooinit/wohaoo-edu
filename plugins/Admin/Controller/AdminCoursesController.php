<?php

App::uses('AdminAppController', 'Admin.Controller');
/**
 * AdminCoursesController
 *
 * @property Course $Course
 */
class AdminCoursesController extends AdminAppController {

	public $uses = array('Course');

    public function index() {
	    $conditions = array();
        $coursesTableURL = array('controller' => 'admin_courses', 'action' => 'index');

        //join get query & named params
        $params = array_merge($this->request->params['named']);
        foreach($this->request->query as $key => $value) $params[$key] = $value;

        foreach($params as $key => $value) {
            $split = explode('-', $key);
            $modelName = (sizeof($split) > 1) ? $split[0] : 'Course';
            $property = (sizeof($split) > 1) ? $split[1] : $key;
            if($modelName == 'Course' || !empty($this->Course->belongsTo[$modelName])) {
                $this->locourseModel($modelName);
                $modelObj = new $modelName();
                if(!empty($modelObj)) {
                    $columnType = $modelObj->getColumnType($property);
                    if(!empty($columnType)){
                        //coursed it to url
                        $coursesTableURL[$key] = $value;
                        //coursed it to conditions
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

		$this->Course->recursive = 0;
		$this->set('courses', $this->Paginator->paginate('Course', $conditions, array()));
		$this->set('coursesTableURL', $coursesTableURL);
		//render as local table if it is an ajax request
        if($this->request->is('ajax'))
        {
            $this->render('table');
        }
	}
    
	public function view($id = null) {
		$this->Course->id = $id;
		if (!$this->Course->exists()) {
			throw new NotFoundException(__('Invalid course'));
		}
        $course = $this->Course->read(null, $id);
		$this->set('course', $course);
	}
	
	public function add() {
		if ($this->request->is('post')) {
			$this->Course->create();
			if ($this->Course->save($this->request->data)) {
				$this->Session->setFlash(__('The course has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->Course->id));
			} else {
				$this->Session->setFlash(__('The course could not be saved. Please, try again.'), 'default', array(), 'bcourse');
			}
		} else {
            //coursed the named params as data
            foreach($this->request->params['named'] as $param => $value) {
                $columnType = $this->Course->getColumnType($param);
                if(!empty($columnType)) {
                    if(empty($this->request->data['Course'])) $this->request->data['Course'] = array();
                    $this->request->data['Course'][$param] = $value;
                }
            }
        }
	}
	
	public function edit($id = null) {
		$this->Course->id = $id;
		if (!$this->Course->exists()) {
			throw new NotFoundException(__('Invalid course'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Course->save($this->request->data)) {
				$this->Session->setFlash(__('The course has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->Course->id));
			} else {
				$this->Session->setFlash(__('The course could not be saved. Please, try again.'), 'default', array(), 'bcourse');
			}
		} else {
            $course = $this->Course->read(null, $id);
			$this->request->data = $course;
		}
	}
	
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Course->id = $id;
		if (!$this->Course->exists()) {
			throw new NotFoundException(__('Invalid course'));
		}
		if ($this->Course->delete()) {
			$this->Session->setFlash(__('Course deleted'), 'default', array(), 'good');
            $this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Course was not deleted'), 'default', array(), 'bcourse');
		$this->redirect(array('action' => 'index'));
	}

}
