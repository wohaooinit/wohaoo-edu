<?php
App::uses('AdminAppController', 'Admin.Controller');
/**
 * AdminRegionsController
 *
 * @property Region $Region
 */
class AdminRegionsController extends AdminAppController {

	public $uses = array('Region');

    public function index() {
	    $conditions = array();
        $regionsTableURL = array('controller' => 'admin_regions', 'action' => 'index');

        //join get query & named params
        $params = array_merge($this->request->params['named']);
        foreach($this->request->query as $key => $value) $params[$key] = $value;

        foreach($params as $key => $value) {
            $split = explode('-', $key);
            $modelName = (sizeof($split) > 1) ? $split[0] : 'Region';
            $property = (sizeof($split) > 1) ? $split[1] : $key;
            if($modelName == 'Region' || !empty($this->Region->belongsTo[$modelName])) {
                $this->loadModel($modelName);
                $modelObj = new $modelName();
                if(!empty($modelObj)) {
                    $columnType = $modelObj->getColumnType($property);
                    if(!empty($columnType)){
                        //add it to url
                        $regionsTableURL[$key] = $value;
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

		$this->Region->recursive = 0;
		$this->set('regions', $this->Paginator->paginate('Region', $conditions, array()));
		$this->set('regionsTableURL', $regionsTableURL);
		//render as local table if it is an ajax request
        if($this->request->is('ajax'))
        {
            $this->render('table');
        }
	}
    
	public function view($id = null) {
		$this->Region->id = $id;
		if (!$this->Region->exists()) {
			throw new NotFoundException(__('Invalid region'));
		}
        $region = $this->Region->read(null, $id);
		$this->set('region', $region);
	}
	
	public function add() {
		if ($this->request->is('post')) {
			$this->Region->create();
			if ($this->Region->save($this->request->data)) {
				$this->Session->setFlash(__('The region has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->Region->id));
			} else {
				$this->Session->setFlash(__('The region could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            //add the named params as data
            foreach($this->request->params['named'] as $param => $value) {
                $columnType = $this->Region->getColumnType($param);
                if(!empty($columnType)) {
                    if(empty($this->request->data['Region'])) $this->request->data['Region'] = array();
                    $this->request->data['Region'][$param] = $value;
                }
            }
        }
	}
	
	public function edit($id = null) {
		$this->Region->id = $id;
		if (!$this->Region->exists()) {
			throw new NotFoundException(__('Invalid region'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Region->save($this->request->data)) {
				$this->Session->setFlash(__('The region has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->Region->id));
			} else {
				$this->Session->setFlash(__('The region could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            $region = $this->Region->read(null, $id);
			$this->request->data = $region;
		}
	}
	
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Region->id = $id;
		if (!$this->Region->exists()) {
			throw new NotFoundException(__('Invalid region'));
		}
		if ($this->Region->delete()) {
			$this->Session->setFlash(__('Region deleted'), 'default', array(), 'good');
            $this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Region was not deleted'), 'default', array(), 'bad');
		$this->redirect(array('action' => 'index'));
	}

}
