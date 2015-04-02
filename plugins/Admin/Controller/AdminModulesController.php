<?php

App::uses('AdminAppController', 'Admin.Controller');
/**
 * AdminModulesController
 *
 * @property Module $Module
 */
class AdminModulesController extends AdminAppController {

	public $uses = array('Module');

    public function index() {
	    $conditions = array();
        $modulesTableURL = array('controller' => 'admin_modules', 'action' => 'index');

        //join get query & named params
        $params = array_merge($this->request->params['named']);
        foreach($this->request->query as $key => $value) $params[$key] = $value;

        foreach($params as $key => $value) {
            $split = explode('-', $key);
            $modelName = (sizeof($split) > 1) ? $split[0] : 'Module';
            $property = (sizeof($split) > 1) ? $split[1] : $key;
            if($modelName == 'Module' || !empty($this->Module->belongsTo[$modelName])) {
                $this->lomoduleModel($modelName);
                $modelObj = new $modelName();
                if(!empty($modelObj)) {
                    $columnType = $modelObj->getColumnType($property);
                    if(!empty($columnType)){
                        //moduled it to url
                        $modulesTableURL[$key] = $value;
                        //moduled it to conditions
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

		$this->Module->recursive = 0;
		$this->Paginator->settings = array('order' => array('Module.mod_index' => 'ASC'));
		$modules = $this->Paginator->paginate('Module', $conditions, array());
		$this->set('modules', $modules);
		$this->log("modules=" . var_export($modules, true), 'debug');
		$this->set('modulesTableURL', $modulesTableURL);
		//render as local table if it is an ajax request
		if($this->request->is('ajax'))
		{
			$this->render('table');
		}
	}
    
	public function view($id = null) {
		$this->Module->id = $id;
		if (!$this->Module->exists()) {
			throw new NotFoundException(__('Invalid module'));
		}
        	$module = $this->Module->read(null, $id);
		$this->set('module', $module);
		
		$this->loadModel('Assessment');
		$this->Assessment->recursive = 0;
		$conditions = array("Assessment.ass_module_id" => $id);
		//http://localhost/admin/modules/view/2/page:2#tabs-3
        	$assessmentsTableURL = array('controller' => 'admin_modules', 'action' => 'view_assessments', $id);
        
		$this->set('assessments', $this->Paginator->paginate('Assessment', $conditions, array()));
		$this->set('assessmentsTableURL', $assessmentsTableURL);
		
		$this->loadModel('Resource');
		$this->Resource->recursive = 0;
		$conditions = array("Resource.res_model_id" => $id, "Resource.res_model" => 'Module');
        	$resourcesTableURL = array('controller' => 'admin_modules', 'action' => 'view_resources', $id);
        
        	$this->Paginator->settings = array('order' => array('Resource.res_created' => 'DESC'));
		$this->set('resources', $this->Paginator->paginate('Resource', $conditions, array()));
		$this->set('resourcesTableURL', $resourcesTableURL);
	}
	
	public function view_assessments($id = null) {
		$this->loadModel('Assessment');
		$this->Assessment->recursive = 0;
		$conditions = array("Assessment.ass_module_id" => $id);
		//http://localhost/admin/modules/view/2/page:2#tabs-3
        	$assessmentsTableURL = array('controller' => 'admin_modules', 'action' => 'view_assessments', $id);
        
		$this->set('assessments', $this->Paginator->paginate('Assessment', $conditions, array()));
		$this->set('assessmentsTableURL', $assessmentsTableURL);
		
		$this->log("loading assessment table ...", 'debug');
		$this->render('../AdminAssessments/table');
	}
	
	public function view_resources($id = null) {
		$this->loadModel('Resource');
		$this->Resource->recursive = 0;
		$conditions = array("Resource.res_model_id" => $id, "Resource.res_model" => 'Module');
        	$resourcesTableURL = array('controller' => 'admin_modules', 'action' => 'view_resources', $id);
        
        	$this->Paginator->settings = array('order' => array('Resource.res_created' => 'DESC'));
		$this->set('resources', $this->Paginator->paginate('Resource', $conditions, array()));
		$this->set('resourcesTableURL', $resourcesTableURL);
		
		$this->log("loading resources table ...", 'debug');
		$this->render('../AdminResourcestable');
	}
	
	public function add($curriculum_id = 0) {
		$this->_loadParent("Curriculum", $curriculum_id);
		if ($this->request->is('post')) {
			$this->Module->create();
			$date = new DateTime();
			if(empty($this->request->data['Module']['mod_created']))
				$this->request->data['Module']['mod_created'] = $date->getTimestamp();
			
			if ($this->Module->save($this->request->data)) {
				$this->Session->setFlash(__('The module has been saved'), 'default', array(), 'good');
				if($this->Session->check('Url.referer'))
					$this->redirect($this->Session->read('Url.referer'));
                		$this->redirect($this->referer());
			} else {
				$this->Session->setFlash(__('The module could not be saved. Please, try again.'), 'default', array(), 'bmodule');
			}
			
		} else {
			//moduled the named params as data
			foreach($this->request->params['named'] as $param => $value) {
				$columnType = $this->Module->getColumnType($param);
				if(!empty($columnType)) {
					if(empty($this->request->data['Module'])) $this->request->data['Module'] = array();
					$this->request->data['Module'][$param] = $value;
				}
			}
			$this->Session->write('Url.referer', $this->referer());
       		 }
	}
	
	public function edit($id = null, $curriculum_id = 0) {
		$this->log("<edit id=$id curriculum_id=${curriculum_id}>", 'debug');
		if($curriculum_id){
			$this->_loadParent("Curriculum", $curriculum_id);
		}
		$this->Module->id = $id;
		if (!$this->Module->exists()) {
			throw new NotFoundException(__('Invalid module'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$date = new DateTime();
			if(empty($this->request->data['Module']['mod_created']))
				$this->request->data['Module']['mod_created'] = $date->getTimestamp();
			$this->request->data['Module']['mod_modified'] = $date->getTimestamp();
			
			if ($this->Module->save($this->request->data)) {
				$this->Session->setFlash(__('The module has been saved'), 'default', array(), 'good');
                		if($this->Session->check('Url.referer'))
					$this->redirect($this->Session->read('Url.referer'));
                		$this->redirect($this->referer());
			} else {
				$this->Session->setFlash(__('The module could not be saved. Please, try again.'), 'default', array(), 'bmodule');
			}
		} else {
           		 $module = $this->Module->read(null, $id);
			$this->request->data = $module;
			$this->Session->write('Url.referer', $this->referer());
		}
		$this->log("</edit>", 'debug');
	}
	
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Module->id = $id;
		if (!$this->Module->exists()) {
			throw new NotFoundException(__('Invalid module'));
		}
		if ($this->Module->delete()) {
			$this->Session->setFlash(__('Module deleted'), 'default', array(), 'good');
            $this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Module was not deleted'), 'default', array(), 'bmodule');
		$this->redirect(array('action' => 'index'));
	}

}
