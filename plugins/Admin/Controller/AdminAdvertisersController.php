<?php
App::uses('AdminAppController', 'Admin.Controller');
/**
 * AdminAdvertisersController
 *
 * @property Advertiser $Advertiser
 */
class AdminAdvertisersController extends AdminAppController {

	public $uses = array('Advertiser');

    public function index() {
	    $conditions = array();
        $advertisersTableURL = array('controller' => 'admin_advertisers', 'action' => 'index');

        //join get query & named params
        $params = array_merge($this->request->params['named']);
        foreach($this->request->query as $key => $value) $params[$key] = $value;

        foreach($params as $key => $value) {
            $split = explode('-', $key);
            $modelName = (sizeof($split) > 1) ? $split[0] : 'Advertiser';
            $property = (sizeof($split) > 1) ? $split[1] : $key;
            if($modelName == 'Advertiser' || !empty($this->Advertiser->belongsTo[$modelName])) {
                $this->loadModel($modelName);
                $modelObj = new $modelName();
                if(!empty($modelObj)) {
                    $columnType = $modelObj->getColumnType($property);
                    if(!empty($columnType)){
                        //add it to url
                        $advertisersTableURL[$key] = $value;
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

		$this->Advertiser->recursive = 0;
		$this->set('advertisers', $this->Paginator->paginate('Advertiser', $conditions, array()));
		$this->set('advertisersTableURL', $advertisersTableURL);
		//render as local table if it is an ajax request
        if($this->request->is('ajax'))
        {
            $this->render('table');
        }
	}
    
	public function view($id = null) {
		$this->Advertiser->id = $id;
		if (!$this->Advertiser->exists()) {
			throw new NotFoundException(__('Invalid advertiser'));
		}
        $advertiser = $this->Advertiser->read(null, $id);
		$this->set('advertiser', $advertiser);
	}
	
	public function add() {
		if ($this->request->is('post')) {
			$this->Advertiser->create();
			if ($this->Advertiser->save($this->request->data)) {
				$this->Session->setFlash(__('The advertiser has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->Advertiser->id));
			} else {
				$this->Session->setFlash(__('The advertiser could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            //add the named params as data
            foreach($this->request->params['named'] as $param => $value) {
                $columnType = $this->Advertiser->getColumnType($param);
                if(!empty($columnType)) {
                    if(empty($this->request->data['Advertiser'])) $this->request->data['Advertiser'] = array();
                    $this->request->data['Advertiser'][$param] = $value;
                }
            }
        }
		$contactPeople = $this->Advertiser->ContactPerson->find('list', array('order' => $this->Advertiser->ContactPerson->displayField));
		$countries = $this->Advertiser->Country->find('list', array('order' => $this->Advertiser->Country->displayField));
		$this->set(compact('contactPeople', 'countries'));
	}
	
	public function edit($id = null) {
		$this->Advertiser->id = $id;
		if (!$this->Advertiser->exists()) {
			throw new NotFoundException(__('Invalid advertiser'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Advertiser->save($this->request->data)) {
				$this->Session->setFlash(__('The advertiser has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->Advertiser->id));
			} else {
				$this->Session->setFlash(__('The advertiser could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            $advertiser = $this->Advertiser->read(null, $id);
			$this->request->data = $advertiser;
		}
		$contactPeople = $this->Advertiser->ContactPerson->find('list', array('order' => $this->Advertiser->ContactPerson->displayField));
		$countries = $this->Advertiser->Country->find('list', array('order' => $this->Advertiser->Country->displayField));
		$this->set(compact('contactPeople', 'countries'));
	}
	
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Advertiser->id = $id;
		if (!$this->Advertiser->exists()) {
			throw new NotFoundException(__('Invalid advertiser'));
		}
		if ($this->Advertiser->delete()) {
			$this->Session->setFlash(__('Advertiser deleted'), 'default', array(), 'good');
            $this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Advertiser was not deleted'), 'default', array(), 'bad');
		$this->redirect(array('action' => 'index'));
	}

}
