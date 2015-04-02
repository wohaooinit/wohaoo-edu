<?php
App::uses('AdminAppController', 'Admin.Controller');
/**
 * AdminDonationsController
 *
 * @property Donation $Donation
 */
class AdminDonationsController extends AdminAppController {

	public $uses = array('Donation');

    public function index() {
	    $conditions = array();
        $donationsTableURL = array('controller' => 'admin_donations', 'action' => 'index');

        //join get query & named params
        $params = array_merge($this->request->params['named']);
        foreach($this->request->query as $key => $value) $params[$key] = $value;

        foreach($params as $key => $value) {
            $split = explode('-', $key);
            $modelName = (sizeof($split) > 1) ? $split[0] : 'Donation';
            $property = (sizeof($split) > 1) ? $split[1] : $key;
            if($modelName == 'Donation' || !empty($this->Donation->belongsTo[$modelName])) {
                $this->loadModel($modelName);
                $modelObj = new $modelName();
                if(!empty($modelObj)) {
                    $columnType = $modelObj->getColumnType($property);
                    if(!empty($columnType)){
                        //add it to url
                        $donationsTableURL[$key] = $value;
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

		$this->Donation->recursive = 0;
		$this->set('donations', $this->Paginator->paginate('Donation', $conditions, array()));
		$this->set('donationsTableURL', $donationsTableURL);
		//render as local table if it is an ajax request
        if($this->request->is('ajax'))
        {
            $this->render('table');
        }
	}
    
	public function view($id = null) {
		$this->Donation->id = $id;
		if (!$this->Donation->exists()) {
			throw new NotFoundException(__('Invalid donation'));
		}
        $donation = $this->Donation->read(null, $id);
		$this->set('donation', $donation);
	}
	
	public function add() {
		if ($this->request->is('post')) {
			$this->Donation->create();
			if ($this->Donation->save($this->request->data)) {
				$this->Session->setFlash(__('The donation has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->Donation->id));
			} else {
				$this->Session->setFlash(__('The donation could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            //add the named params as data
            foreach($this->request->params['named'] as $param => $value) {
                $columnType = $this->Donation->getColumnType($param);
                if(!empty($columnType)) {
                    if(empty($this->request->data['Donation'])) $this->request->data['Donation'] = array();
                    $this->request->data['Donation'][$param] = $value;
                }
            }
        }
		$donors = $this->Donation->Donor->find('list', array('order' => $this->Donation->Donor->displayField));
		$this->set(compact('donors'));
	}
	
	public function edit($id = null) {
		$this->Donation->id = $id;
		if (!$this->Donation->exists()) {
			throw new NotFoundException(__('Invalid donation'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Donation->save($this->request->data)) {
				$this->Session->setFlash(__('The donation has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->Donation->id));
			} else {
				$this->Session->setFlash(__('The donation could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            $donation = $this->Donation->read(null, $id);
			$this->request->data = $donation;
		}
		$donors = $this->Donation->Donor->find('list', array('order' => $this->Donation->Donor->displayField));
		$this->set(compact('donors'));
	}
	
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Donation->id = $id;
		if (!$this->Donation->exists()) {
			throw new NotFoundException(__('Invalid donation'));
		}
		if ($this->Donation->delete()) {
			$this->Session->setFlash(__('Donation deleted'), 'default', array(), 'good');
            $this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Donation was not deleted'), 'default', array(), 'bad');
		$this->redirect(array('action' => 'index'));
	}

}
