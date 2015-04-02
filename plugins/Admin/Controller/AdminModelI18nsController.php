<?php
App::uses('AdminAppController', 'Admin.Controller');
/**
 * AdminModelI18nsController
 *
 * @property ModelI18n $ModelI18n
 */
class AdminModelI18nsController extends AdminAppController {

	public $uses = array('ModelI18n');

    public function index() {
	    $conditions = array();
        $modelI18nsTableURL = array('controller' => 'admin_model_i18ns', 'action' => 'index');

        //join get query & named params
        $params = array_merge($this->request->params['named']);
        foreach($this->request->query as $key => $value) $params[$key] = $value;

        foreach($params as $key => $value) {
            $split = explode('-', $key);
            $modelName = (sizeof($split) > 1) ? $split[0] : 'ModelI18n';
            $property = (sizeof($split) > 1) ? $split[1] : $key;
            if($modelName == 'ModelI18n' || !empty($this->ModelI18n->belongsTo[$modelName])) {
                $this->loadModel($modelName);
                $modelObj = new $modelName();
                if(!empty($modelObj)) {
                    $columnType = $modelObj->getColumnType($property);
                    if(!empty($columnType)){
                        //add it to url
                        $modelI18nsTableURL[$key] = $value;
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

		$this->ModelI18n->recursive = 0;
		$this->set('modelI18ns', $this->Paginator->paginate('ModelI18n', $conditions, array()));
		$this->set('modelI18nsTableURL', $modelI18nsTableURL);
		//render as local table if it is an ajax request
        if($this->request->is('ajax'))
        {
            $this->render('table');
        }
	}
    
	public function view($id = null) {
		$this->ModelI18n->id = $id;
		if (!$this->ModelI18n->exists()) {
			throw new NotFoundException(__('Invalid model i18n'));
		}
        $modelI18n = $this->ModelI18n->read(null, $id);
		$this->set('modelI18n', $modelI18n);
	}
	
	public function add() {
		if ($this->request->is('post')) {
			$this->ModelI18n->create();
			if ($this->ModelI18n->save($this->request->data)) {
				$this->Session->setFlash(__('The model i18n has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->ModelI18n->id));
			} else {
				$this->Session->setFlash(__('The model i18n could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            //add the named params as data
            foreach($this->request->params['named'] as $param => $value) {
                $columnType = $this->ModelI18n->getColumnType($param);
                if(!empty($columnType)) {
                    if(empty($this->request->data['ModelI18n'])) $this->request->data['ModelI18n'] = array();
                    $this->request->data['ModelI18n'][$param] = $value;
                }
            }
        }
	}
	
	public function edit($id = null) {
		$this->ModelI18n->id = $id;
		if (!$this->ModelI18n->exists()) {
			throw new NotFoundException(__('Invalid model i18n'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->ModelI18n->save($this->request->data)) {
				$this->Session->setFlash(__('The model i18n has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->ModelI18n->id));
			} else {
				$this->Session->setFlash(__('The model i18n could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            $modelI18n = $this->ModelI18n->read(null, $id);
			$this->request->data = $modelI18n;
		}
	}
	
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->ModelI18n->id = $id;
		if (!$this->ModelI18n->exists()) {
			throw new NotFoundException(__('Invalid model i18n'));
		}
		if ($this->ModelI18n->delete()) {
			$this->Session->setFlash(__('Model i18n deleted'), 'default', array(), 'good');
            $this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Model i18n was not deleted'), 'default', array(), 'bad');
		$this->redirect(array('action' => 'index'));
	}

}
