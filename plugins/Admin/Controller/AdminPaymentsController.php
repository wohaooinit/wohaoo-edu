<?php

App::uses('AdminAppController', 'Admin.Controller');
/**
 * AdminPaymentsController
 *
 * @property Payment $Payment
 */
class AdminPaymentsController extends AdminAppController {

	public $uses = array('Payment');

    public function index() {
	    $conditions = array();
        $paymentsTableURL = array('controller' => 'admin_payments', 'action' => 'index');

        //join get query & named params
        $params = array_merge($this->request->params['named']);
        foreach($this->request->query as $key => $value) $params[$key] = $value;

        foreach($params as $key => $value) {
            $split = explode('-', $key);
            $modelName = (sizeof($split) > 1) ? $split[0] : 'Payment';
            $property = (sizeof($split) > 1) ? $split[1] : $key;
            if($modelName == 'Payment' || !empty($this->Payment->belongsTo[$modelName])) {
                $this->lopaymentModel($modelName);
                $modelObj = new $modelName();
                if(!empty($modelObj)) {
                    $columnType = $modelObj->getColumnType($property);
                    if(!empty($columnType)){
                        //paymentd it to url
                        $paymentsTableURL[$key] = $value;
                        //paymentd it to conditions
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

		$this->Payment->recursive = 0;
		$this->set('payments', $this->Paginator->paginate('Payment', $conditions, array()));
		$this->set('paymentsTableURL', $paymentsTableURL);
		//render as local table if it is an ajax request
        if($this->request->is('ajax'))
        {
            $this->render('table');
        }
	}
    
	public function view($id = null) {
		$this->Payment->id = $id;
		if (!$this->Payment->exists()) {
			throw new NotFoundException(__('Invalid payment'));
		}
        $payment = $this->Payment->read(null, $id);
		$this->set('payment', $payment);
	}
	
	public function add() {
		if ($this->request->is('post')) {
			$this->Payment->create();
			if ($this->Payment->save($this->request->data)) {
				$this->Session->setFlash(__('The payment has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->Payment->id));
			} else {
				$this->Session->setFlash(__('The payment could not be saved. Please, try again.'), 'default', array(), 'bpayment');
			}
		} else {
            //paymentd the named params as data
            foreach($this->request->params['named'] as $param => $value) {
                $columnType = $this->Payment->getColumnType($param);
                if(!empty($columnType)) {
                    if(empty($this->request->data['Payment'])) $this->request->data['Payment'] = array();
                    $this->request->data['Payment'][$param] = $value;
                }
            }
        }
	}
	
	public function edit($id = null) {
		$this->Payment->id = $id;
		if (!$this->Payment->exists()) {
			throw new NotFoundException(__('Invalid payment'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Payment->save($this->request->data)) {
				$this->Session->setFlash(__('The payment has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->Payment->id));
			} else {
				$this->Session->setFlash(__('The payment could not be saved. Please, try again.'), 'default', array(), 'bpayment');
			}
		} else {
            $payment = $this->Payment->read(null, $id);
			$this->request->data = $payment;
		}
	}
	
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Payment->id = $id;
		if (!$this->Payment->exists()) {
			throw new NotFoundException(__('Invalid payment'));
		}
		if ($this->Payment->delete()) {
			$this->Session->setFlash(__('Payment deleted'), 'default', array(), 'good');
            $this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Payment was not deleted'), 'default', array(), 'bpayment');
		$this->redirect(array('action' => 'index'));
	}

}
