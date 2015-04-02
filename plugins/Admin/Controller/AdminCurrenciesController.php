<?php
App::uses('AdminAppController', 'Admin.Controller');
/**
 * AdminCurrenciesController
 *
 * @property Currency $Currency
 */
class AdminCurrenciesController extends AdminAppController {

	public $uses = array('Currency');

    public function index() {
	    $conditions = array();
        $currenciesTableURL = array('controller' => 'admin_currencies', 'action' => 'index');

        //join get query & named params
        $params = array_merge($this->request->params['named']);
        foreach($this->request->query as $key => $value) $params[$key] = $value;

        foreach($params as $key => $value) {
            $split = explode('-', $key);
            $modelName = (sizeof($split) > 1) ? $split[0] : 'Currency';
            $property = (sizeof($split) > 1) ? $split[1] : $key;
            if($modelName == 'Currency' || !empty($this->Currency->belongsTo[$modelName])) {
                $this->loadModel($modelName);
                $modelObj = new $modelName();
                if(!empty($modelObj)) {
                    $columnType = $modelObj->getColumnType($property);
                    if(!empty($columnType)){
                        //add it to url
                        $currenciesTableURL[$key] = $value;
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

		$this->Currency->recursive = 0;
		$this->set('currencies', $this->Paginator->paginate('Currency', $conditions, array()));
		$this->set('currenciesTableURL', $currenciesTableURL);
		//render as local table if it is an ajax request
        if($this->request->is('ajax'))
        {
            $this->render('table');
        }
	}
    
	public function view($id = null) {
		$this->Currency->id = $id;
		if (!$this->Currency->exists()) {
			throw new NotFoundException(__('Invalid currency'));
		}
        $currency = $this->Currency->read(null, $id);
		$this->set('currency', $currency);
	}
	
	public function add() {
		if ($this->request->is('post')) {
			$this->Currency->create();
			if ($this->Currency->save($this->request->data)) {
				$this->Session->setFlash(__('The currency has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->Currency->id));
			} else {
				$this->Session->setFlash(__('The currency could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            //add the named params as data
            foreach($this->request->params['named'] as $param => $value) {
                $columnType = $this->Currency->getColumnType($param);
                if(!empty($columnType)) {
                    if(empty($this->request->data['Currency'])) $this->request->data['Currency'] = array();
                    $this->request->data['Currency'][$param] = $value;
                }
            }
        }
	}
	
	public function edit($id = null) {
		$this->Currency->id = $id;
		if (!$this->Currency->exists()) {
			throw new NotFoundException(__('Invalid currency'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Currency->save($this->request->data)) {
				$this->Session->setFlash(__('The currency has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->Currency->id));
			} else {
				$this->Session->setFlash(__('The currency could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            $currency = $this->Currency->read(null, $id);
			$this->request->data = $currency;
		}
	}
	
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Currency->id = $id;
		if (!$this->Currency->exists()) {
			throw new NotFoundException(__('Invalid currency'));
		}
		if ($this->Currency->delete()) {
			$this->Session->setFlash(__('Currency deleted'), 'default', array(), 'good');
            $this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Currency was not deleted'), 'default', array(), 'bad');
		$this->redirect(array('action' => 'index'));
	}

}
