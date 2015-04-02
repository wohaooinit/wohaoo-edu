<?php

App::uses('AdminAppController', 'Admin.Controller');
/**
 * AdminCurriculumsController
 *
 * @property Curriculum $Curriculum
 */
class AdminCurriculumsController extends AdminAppController {

	public $uses = array('Curriculum');

    public function index() {
	    $conditions = array();
        $curriculumsTableURL = array('controller' => 'admin_curriculums', 'action' => 'index');

        //join get query & named params
        $params = array_merge($this->request->params['named']);
        foreach($this->request->query as $key => $value) $params[$key] = $value;

        foreach($params as $key => $value) {
            $split = explode('-', $key);
            $modelName = (sizeof($split) > 1) ? $split[0] : 'Curriculum';
            $property = (sizeof($split) > 1) ? $split[1] : $key;
            if($modelName == 'Curriculum' || !empty($this->Curriculum->belongsTo[$modelName])) {
                $this->locurriculumModel($modelName);
                $modelObj = new $modelName();
                if(!empty($modelObj)) {
                    $columnType = $modelObj->getColumnType($property);
                    if(!empty($columnType)){
                        //curriculumd it to url
                        $curriculumsTableURL[$key] = $value;
                        //curriculumd it to conditions
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

		$this->Curriculum->recursive = 0;
		$this->set('curriculums', $this->Paginator->paginate('Curriculum', $conditions, array()));
		$this->set('curriculumsTableURL', $curriculumsTableURL);
		//render as local table if it is an ajax request
        if($this->request->is('ajax'))
        {
            $this->render('table');
        }
	}
    
	public function view($id = null) {
		$this->Curriculum->id = $id;
		if (!$this->Curriculum->exists()) {
			throw new NotFoundException(__('Invalid curriculum'));
		}
       		 $curriculum = $this->Curriculum->read(null, $id);
		$this->set('curriculum', $curriculum);
		
		$this->loadModel('Module');
		$this->Module->recursive = 0;
		$conditions = array("Module.mod_curriculum_id" => $id);
        	$modulesTableURL = array('controller' => 'admin_modules', 'action' => 'index');
        
        	$this->Paginator->settings = array('order' => array('Module.mod_index' => 'ASC'));
		$this->set('modules', $this->Paginator->paginate('Module', $conditions, array()));
		$this->set('modulesTableURL', $modulesTableURL);
		
		
		$this->loadModel('Resource');
		$this->Resource->recursive = 0;
		$conditions = array("Resource.res_model_id" => $id, "Resource.res_model" => 'Curriculum');
        	$resourcesTableURL = array('controller' => 'admin_resources', 'action' => 'index');
        
        	$this->Paginator->settings = array('order' => array('Resource.res_created' => 'DESC'));
		$this->set('resources', $this->Paginator->paginate('Resource', $conditions, array()));
		$this->set('resourcesTableURL', $resourcesTableURL);
       }
	
	public function add() {
		if ($this->request->is('post')) {
			$this->Curriculum->create();
			$date = new DateTime();
			if(empty($this->request->data['Curriculum']['cur_created']))
				$this->request->data['Curriculum']['cur_created'] = $date->getTimestamp();
				
			if ($this->Curriculum->save($this->request->data)) {
				$this->Session->setFlash(__('The curriculum has been saved'), 'default', array(), 'good');
               			 $this->redirect(array('action' => 'view', $this->Curriculum->id));
			} else {
				$this->Session->setFlash(__('The curriculum could not be saved. Please, try again.'), 'default', array(), 'bcurriculum');
			}
		} else {
            //curriculumd the named params as data
            foreach($this->request->params['named'] as $param => $value) {
                $columnType = $this->Curriculum->getColumnType($param);
                if(!empty($columnType)) {
                    if(empty($this->request->data['Curriculum'])) $this->request->data['Curriculum'] = array();
                    $this->request->data['Curriculum'][$param] = $value;
                }
            }
        }
	}
	
	public function edit($id = null) {
		$this->Curriculum->id = $id;
		if (!$this->Curriculum->exists()) {
			throw new NotFoundException(__('Invalid curriculum'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$date = new DateTime();
			if(empty($this->request->data['Curriculum']['cur_created']))
				$this->request->data['Curriculum']['cur_created'] = $date->getTimestamp();
			$this->request->data['Curriculum']['cur_modified'] = $date->getTimestamp();
			if ($this->Curriculum->save($this->request->data)) {
				$this->Session->setFlash(__('The curriculum has been saved'), 'default', array(), 'good');
                	$this->redirect(array('action' => 'view', $this->Curriculum->id));
			} else {
				$this->Session->setFlash(__('The curriculum could not be saved. Please, try again.'), 'default', array(), 'bcurriculum');
			}
		} else {
          		  $curriculum = $this->Curriculum->read(null, $id);
			$this->request->data = $curriculum;
		}
	}
	
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Curriculum->id = $id;
		if (!$this->Curriculum->exists()) {
			throw new NotFoundException(__('Invalid curriculum'));
		}
		if ($this->Curriculum->delete()) {
			$this->Session->setFlash(__('Curriculum deleted'), 'default', array(), 'good');
            $this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Curriculum was not deleted'), 'default', array(), 'bcurriculum');
		$this->redirect(array('action' => 'index'));
	}

}
