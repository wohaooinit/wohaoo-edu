<?php
App::uses('AdminAppController', 'Admin.Controller');
/**
 * AdminCountriesController
 *
 * @property Country $Country
 */
class AdminCountriesController extends AdminAppController {

	public $uses = array('Country');

    public function index() {
	    $conditions = array();
        $countriesTableURL = array('controller' => 'admin_countries', 'action' => 'index');

        //join get query & named params
        $params = array_merge($this->request->params['named']);
        foreach($this->request->query as $key => $value) $params[$key] = $value;

        foreach($params as $key => $value) {
            $split = explode('-', $key);
            $modelName = (sizeof($split) > 1) ? $split[0] : 'Country';
            $property = (sizeof($split) > 1) ? $split[1] : $key;
            if($modelName == 'Country' || !empty($this->Country->belongsTo[$modelName])) {
                $this->loadModel($modelName);
                $modelObj = new $modelName();
                if(!empty($modelObj)) {
                    $columnType = $modelObj->getColumnType($property);
                    if(!empty($columnType)){
                        //add it to url
                        $countriesTableURL[$key] = $value;
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

		$this->Country->recursive = 0;
		$this->set('countries', $this->Paginator->paginate('Country', $conditions, array()));
		$this->set('countriesTableURL', $countriesTableURL);
		//render as local table if it is an ajax request
        if($this->request->is('ajax'))
        {
            $this->render('table');
        }
	}
    
	public function view($id = null) {
		$this->Country->id = $id;
		if (!$this->Country->exists()) {
			throw new NotFoundException(__('Invalid country'));
		}
        $country = $this->Country->read(null, $id);
		$this->set('country', $country);
	}
	
	public function add() {
		if ($this->request->is('post')) {
			$this->Country->create();
			if ($this->Country->save($this->request->data)) {
				$this->Session->setFlash(__('The country has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->Country->id));
			} else {
				$this->Session->setFlash(__('The country could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            //add the named params as data
            foreach($this->request->params['named'] as $param => $value) {
                $columnType = $this->Country->getColumnType($param);
                if(!empty($columnType)) {
                    if(empty($this->request->data['Country'])) $this->request->data['Country'] = array();
                    $this->request->data['Country'][$param] = $value;
                }
            }
        }
	}
	
	public function edit($id = null) {
		$this->Country->id = $id;
		if (!$this->Country->exists()) {
			throw new NotFoundException(__('Invalid country'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Country->save($this->request->data)) {
				$this->Session->setFlash(__('The country has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->Country->id));
			} else {
				$this->Session->setFlash(__('The country could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            $country = $this->Country->read(null, $id);
			$this->request->data = $country;
		}
	}
	
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Country->id = $id;
		if (!$this->Country->exists()) {
			throw new NotFoundException(__('Invalid country'));
		}
		if ($this->Country->delete()) {
			$this->Session->setFlash(__('Country deleted'), 'default', array(), 'good');
            $this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Country was not deleted'), 'default', array(), 'bad');
		$this->redirect(array('action' => 'index'));
	}

}
