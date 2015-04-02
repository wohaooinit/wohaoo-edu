<?php
App::uses('AdminAppController', 'Admin.Controller');
/**
 * AdminAttributesController
 *
 * @property Attribute $Attribute
 */
class AdminAttributesController extends AdminAppController {

	public $uses = array('Attribute');

    public function index() {
	    $conditions = array();
        $attributesTableURL = array('controller' => 'admin_attributes', 'action' => 'index');

        //join get query & named params
        $params = array_merge($this->request->params['named']);
        foreach($this->request->query as $key => $value) $params[$key] = $value;

        foreach($params as $key => $value) {
            $split = explode('-', $key);
            $modelName = (sizeof($split) > 1) ? $split[0] : 'Attribute';
            $property = (sizeof($split) > 1) ? $split[1] : $key;
            if($modelName == 'Attribute' || !empty($this->Attribute->belongsTo[$modelName])) {
                $this->loadModel($modelName);
                $modelObj = new $modelName();
                if(!empty($modelObj)) {
                    $columnType = $modelObj->getColumnType($property);
                    if(!empty($columnType)){
                        //add it to url
                        $attributesTableURL[$key] = $value;
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

		$this->Attribute->recursive = 0;
		$this->set('attributes', $this->Paginator->paginate('Attribute', $conditions, array()));
		$this->set('attributesTableURL', $attributesTableURL);
		//render as local table if it is an ajax request
        if($this->request->is('ajax'))
        {
            $this->render('table');
        }
	}
    
	public function view($id = null) {
		$this->Attribute->id = $id;
		if (!$this->Attribute->exists()) {
			throw new NotFoundException(__('Invalid attribute'));
		}
        $attribute = $this->Attribute->read(null, $id);
		$this->set('attribute', $attribute);
	}
	
	public function add() {
		if ($this->request->is('post')) {
			$this->Attribute->create();
			if ($this->Attribute->save($this->request->data)) {
				$this->Session->setFlash(__('The attribute has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->Attribute->id));
			} else {
				$this->Session->setFlash(__('The attribute could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            //add the named params as data
            foreach($this->request->params['named'] as $param => $value) {
                $columnType = $this->Attribute->getColumnType($param);
                if(!empty($columnType)) {
                    if(empty($this->request->data['Attribute'])) $this->request->data['Attribute'] = array();
                    $this->request->data['Attribute'][$param] = $value;
                }
            }
        }
	}
	
	public function edit($id = null) {
		$this->Attribute->id = $id;
		if (!$this->Attribute->exists()) {
			throw new NotFoundException(__('Invalid attribute'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Attribute->save($this->request->data)) {
				$this->Session->setFlash(__('The attribute has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->Attribute->id));
			} else {
				$this->Session->setFlash(__('The attribute could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            $attribute = $this->Attribute->read(null, $id);
			$this->request->data = $attribute;
		}
	}
	
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Attribute->id = $id;
		if (!$this->Attribute->exists()) {
			throw new NotFoundException(__('Invalid attribute'));
		}
		if ($this->Attribute->delete()) {
			$this->Session->setFlash(__('Attribute deleted'), 'default', array(), 'good');
            $this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Attribute was not deleted'), 'default', array(), 'bad');
		$this->redirect(array('action' => 'index'));
	}

}
