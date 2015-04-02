<?php
App::uses('AdminAppController', 'Admin.Controller');
/**
 * AdminProjectCategoriesController
 *
 * @property ProjectCategory $ProjectCategory
 */
class AdminProjectCategoriesController extends AdminAppController {

	public $uses = array('ProjectCategory');

    public function index() {
	    $conditions = array();
        $projectCategoriesTableURL = array('controller' => 'admin_project_categories', 'action' => 'index');

        //join get query & named params
        $params = array_merge($this->request->params['named']);
        foreach($this->request->query as $key => $value) $params[$key] = $value;

        foreach($params as $key => $value) {
            $split = explode('-', $key);
            $modelName = (sizeof($split) > 1) ? $split[0] : 'ProjectCategory';
            $property = (sizeof($split) > 1) ? $split[1] : $key;
            if($modelName == 'ProjectCategory' || !empty($this->ProjectCategory->belongsTo[$modelName])) {
                $this->loadModel($modelName);
                $modelObj = new $modelName();
                if(!empty($modelObj)) {
                    $columnType = $modelObj->getColumnType($property);
                    if(!empty($columnType)){
                        //add it to url
                        $projectCategoriesTableURL[$key] = $value;
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

		$this->ProjectCategory->recursive = 0;
		$this->set('projectCategories', $this->Paginator->paginate('ProjectCategory', $conditions, array()));
		$this->set('projectCategoriesTableURL', $projectCategoriesTableURL);
		//render as local table if it is an ajax request
        if($this->request->is('ajax'))
        {
            $this->render('table');
        }
	}
    
	public function view($id = null) {
		$this->ProjectCategory->id = $id;
		if (!$this->ProjectCategory->exists()) {
			throw new NotFoundException(__('Invalid project category'));
		}
        $projectCategory = $this->ProjectCategory->read(null, $id);
		$this->set('projectCategory', $projectCategory);
	}
	
	public function add() {
		if ($this->request->is('post')) {
			$this->ProjectCategory->create();
			if ($this->ProjectCategory->save($this->request->data)) {
				$this->Session->setFlash(__('The project category has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->ProjectCategory->id));
			} else {
				$this->Session->setFlash(__('The project category could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            //add the named params as data
            foreach($this->request->params['named'] as $param => $value) {
                $columnType = $this->ProjectCategory->getColumnType($param);
                if(!empty($columnType)) {
                    if(empty($this->request->data['ProjectCategory'])) $this->request->data['ProjectCategory'] = array();
                    $this->request->data['ProjectCategory'][$param] = $value;
                }
            }
        }
	}
	
	public function edit($id = null) {
		$this->ProjectCategory->id = $id;
		if (!$this->ProjectCategory->exists()) {
			throw new NotFoundException(__('Invalid project category'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->ProjectCategory->save($this->request->data)) {
				$this->Session->setFlash(__('The project category has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->ProjectCategory->id));
			} else {
				$this->Session->setFlash(__('The project category could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            $projectCategory = $this->ProjectCategory->read(null, $id);
			$this->request->data = $projectCategory;
		}
	}
	
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->ProjectCategory->id = $id;
		if (!$this->ProjectCategory->exists()) {
			throw new NotFoundException(__('Invalid project category'));
		}
		if ($this->ProjectCategory->delete()) {
			$this->Session->setFlash(__('Project category deleted'), 'default', array(), 'good');
            $this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Project category was not deleted'), 'default', array(), 'bad');
		$this->redirect(array('action' => 'index'));
	}

}
