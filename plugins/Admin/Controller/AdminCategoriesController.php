<?php
App::uses('AdminAppController', 'Admin.Controller');
/**
 * AdminCategoriesController
 *
 * @property Category $Category
 */
class AdminCategoriesController extends AdminAppController {

	public $uses = array('Category');

    public function index() {
	    $conditions = array();
        $categoriesTableURL = array('controller' => 'admin_categories', 'action' => 'index');

        //join get query & named params
        $params = array_merge($this->request->params['named']);
        foreach($this->request->query as $key => $value) $params[$key] = $value;

        foreach($params as $key => $value) {
            $split = explode('-', $key);
            $modelName = (sizeof($split) > 1) ? $split[0] : 'Category';
            $property = (sizeof($split) > 1) ? $split[1] : $key;
            if($modelName == 'Category' || !empty($this->Category->belongsTo[$modelName])) {
                $this->loadModel($modelName);
                $modelObj = new $modelName();
                if(!empty($modelObj)) {
                    $columnType = $modelObj->getColumnType($property);
                    if(!empty($columnType)){
                        //add it to url
                        $categoriesTableURL[$key] = $value;
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

		$this->Category->recursive = 0;
		$this->set('categories', $this->Paginator->paginate('Category', $conditions, array()));
		$this->set('categoriesTableURL', $categoriesTableURL);
		//render as local table if it is an ajax request
        if($this->request->is('ajax'))
        {
            $this->render('table');
        }
	}
    
	public function view($id = null) {
		$this->Category->id = $id;
		if (!$this->Category->exists()) {
			throw new NotFoundException(__('Invalid category'));
		}
        $category = $this->Category->read(null, $id);
		$this->set('category', $category);
	}
	
	public function add() {
		if ($this->request->is('post')) {
			$this->Category->create();
			if ($this->Category->save($this->request->data)) {
				$this->Session->setFlash(__('The category has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->Category->id));
			} else {
				$this->Session->setFlash(__('The category could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            //add the named params as data
            foreach($this->request->params['named'] as $param => $value) {
                $columnType = $this->Category->getColumnType($param);
                if(!empty($columnType)) {
                    if(empty($this->request->data['Category'])) $this->request->data['Category'] = array();
                    $this->request->data['Category'][$param] = $value;
                }
            }
        }
	}
	
	public function edit($id = null) {
		$this->Category->id = $id;
		if (!$this->Category->exists()) {
			throw new NotFoundException(__('Invalid category'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Category->save($this->request->data)) {
				$this->Session->setFlash(__('The category has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->Category->id));
			} else {
				$this->Session->setFlash(__('The category could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            $category = $this->Category->read(null, $id);
			$this->request->data = $category;
		}
	}
	
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Category->id = $id;
		if (!$this->Category->exists()) {
			throw new NotFoundException(__('Invalid category'));
		}
		if ($this->Category->delete()) {
			$this->Session->setFlash(__('Category deleted'), 'default', array(), 'good');
            $this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Category was not deleted'), 'default', array(), 'bad');
		$this->redirect(array('action' => 'index'));
	}

}
