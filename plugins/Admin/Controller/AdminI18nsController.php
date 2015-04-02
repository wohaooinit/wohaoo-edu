<?php
App::uses('AdminAppController', 'Admin.Controller');
/**
 * AdminI18nsController
 *
 * @property I18n $I18n
 */
class AdminI18nsController extends AdminAppController {

	public $uses = array('I18n');

    public function index() {
	    $conditions = array();
        $i18nsTableURL = array('controller' => 'admin_i18ns', 'action' => 'index');

        //join get query & named params
        $params = array_merge($this->request->params['named']);
        foreach($this->request->query as $key => $value) $params[$key] = $value;

        foreach($params as $key => $value) {
            $split = explode('-', $key);
            $modelName = (sizeof($split) > 1) ? $split[0] : 'I18n';
            $property = (sizeof($split) > 1) ? $split[1] : $key;
            if($modelName == 'I18n' || !empty($this->I18n->belongsTo[$modelName])) {
                $this->loadModel($modelName);
                $modelObj = new $modelName();
                if(!empty($modelObj)) {
                    $columnType = $modelObj->getColumnType($property);
                    if(!empty($columnType)){
                        //add it to url
                        $i18nsTableURL[$key] = $value;
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

		$this->I18n->recursive = 0;
		$this->set('i18ns', $this->Paginator->paginate('I18n', $conditions, array()));
		$this->set('i18nsTableURL', $i18nsTableURL);
		//render as local table if it is an ajax request
        if($this->request->is('ajax'))
        {
            $this->render('table');
        }
	}
    
	public function view($id = null) {
		$this->I18n->id = $id;
		if (!$this->I18n->exists()) {
			throw new NotFoundException(__('Invalid i18n'));
		}
        $i18n = $this->I18n->read(null, $id);
		$this->set('i18n', $i18n);
	}
	
	public function add() {
		if ($this->request->is('post')) {
			$this->I18n->create();
			if ($this->I18n->save($this->request->data)) {
				$this->Session->setFlash(__('The i18n has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->I18n->id));
			} else {
				$this->Session->setFlash(__('The i18n could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            //add the named params as data
            foreach($this->request->params['named'] as $param => $value) {
                $columnType = $this->I18n->getColumnType($param);
                if(!empty($columnType)) {
                    if(empty($this->request->data['I18n'])) $this->request->data['I18n'] = array();
                    $this->request->data['I18n'][$param] = $value;
                }
            }
        }
	}
	
	public function edit($id = null) {
		$this->I18n->id = $id;
		if (!$this->I18n->exists()) {
			throw new NotFoundException(__('Invalid i18n'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->I18n->save($this->request->data)) {
				$this->Session->setFlash(__('The i18n has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->I18n->id));
			} else {
				$this->Session->setFlash(__('The i18n could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            $i18n = $this->I18n->read(null, $id);
			$this->request->data = $i18n;
		}
	}
	
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->I18n->id = $id;
		if (!$this->I18n->exists()) {
			throw new NotFoundException(__('Invalid i18n'));
		}
		if ($this->I18n->delete()) {
			$this->Session->setFlash(__('I18n deleted'), 'default', array(), 'good');
            $this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('I18n was not deleted'), 'default', array(), 'bad');
		$this->redirect(array('action' => 'index'));
	}

}
