<?php
App::uses('AdminAppController', 'Admin.Controller');
/**
 * AdminContactCategoriesController
 *
 * @property ContactCategory $ContactCategory
 */
class AdminContactCategoriesController extends AdminAppController {

	public $uses = array('ContactCategory');

    public function index() {
	    $conditions = array();
        $contactCategoriesTableURL = array('controller' => 'admin_contact_categories', 'action' => 'index');

        //join get query & named params
        $params = array_merge($this->request->params['named']);
        foreach($this->request->query as $key => $value) $params[$key] = $value;

        foreach($params as $key => $value) {
            $split = explode('-', $key);
            $modelName = (sizeof($split) > 1) ? $split[0] : 'ContactCategory';
            $property = (sizeof($split) > 1) ? $split[1] : $key;
            if($modelName == 'ContactCategory' || !empty($this->ContactCategory->belongsTo[$modelName])) {
                $this->loadModel($modelName);
                $modelObj = new $modelName();
                if(!empty($modelObj)) {
                    $columnType = $modelObj->getColumnType($property);
                    if(!empty($columnType)){
                        //add it to url
                        $contactCategoriesTableURL[$key] = $value;
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

		$this->ContactCategory->recursive = 0;
		$this->set('contactCategories', $this->Paginator->paginate('ContactCategory', $conditions, array()));
		$this->set('contactCategoriesTableURL', $contactCategoriesTableURL);
		//render as local table if it is an ajax request
        if($this->request->is('ajax'))
        {
            $this->render('table');
        }
	}
    
	public function view($id = null) {
		$this->ContactCategory->id = $id;
		if (!$this->ContactCategory->exists()) {
			throw new NotFoundException(__('Invalid contact category'));
		}
        $contactCategory = $this->ContactCategory->read(null, $id);
		$this->set('contactCategory', $contactCategory);
	}
	
	public function add() {
		if ($this->request->is('post')) {
			$this->ContactCategory->create();
			if ($this->ContactCategory->save($this->request->data)) {
				$this->Session->setFlash(__('The contact category has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->ContactCategory->id));
			} else {
				$this->Session->setFlash(__('The contact category could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            //add the named params as data
            foreach($this->request->params['named'] as $param => $value) {
                $columnType = $this->ContactCategory->getColumnType($param);
                if(!empty($columnType)) {
                    if(empty($this->request->data['ContactCategory'])) $this->request->data['ContactCategory'] = array();
                    $this->request->data['ContactCategory'][$param] = $value;
                }
            }
        }
	}
	
	public function edit($id = null) {
		$this->ContactCategory->id = $id;
		if (!$this->ContactCategory->exists()) {
			throw new NotFoundException(__('Invalid contact category'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->ContactCategory->save($this->request->data)) {
				$this->Session->setFlash(__('The contact category has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->ContactCategory->id));
			} else {
				$this->Session->setFlash(__('The contact category could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            $contactCategory = $this->ContactCategory->read(null, $id);
			$this->request->data = $contactCategory;
		}
	}
	
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->ContactCategory->id = $id;
		if (!$this->ContactCategory->exists()) {
			throw new NotFoundException(__('Invalid contact category'));
		}
		if ($this->ContactCategory->delete()) {
			$this->Session->setFlash(__('Contact category deleted'), 'default', array(), 'good');
            $this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Contact category was not deleted'), 'default', array(), 'bad');
		$this->redirect(array('action' => 'index'));
	}

}
