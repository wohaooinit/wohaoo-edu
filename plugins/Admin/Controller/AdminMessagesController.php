<?php
App::uses('AdminAppController', 'Admin.Controller');
/**
 * AdminMessagesController
 *
 * @property Message $Message
 */
class AdminMessagesController extends AdminAppController {

	public $uses = array('Message');

    public function index() {
	    $conditions = array();
        $messagesTableURL = array('controller' => 'admin_messages', 'action' => 'index');

        //join get query & named params
        $params = array_merge($this->request->params['named']);
        foreach($this->request->query as $key => $value) $params[$key] = $value;

        foreach($params as $key => $value) {
            $split = explode('-', $key);
            $modelName = (sizeof($split) > 1) ? $split[0] : 'Message';
            $property = (sizeof($split) > 1) ? $split[1] : $key;
            if($modelName == 'Message' || !empty($this->Message->belongsTo[$modelName])) {
                $this->loadModel($modelName);
                $modelObj = new $modelName();
                if(!empty($modelObj)) {
                    $columnType = $modelObj->getColumnType($property);
                    if(!empty($columnType)){
                        //add it to url
                        $messagesTableURL[$key] = $value;
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

		$this->Message->recursive = 0;
		$this->set('messages', $this->Paginator->paginate('Message', $conditions, array()));
		$this->set('messagesTableURL', $messagesTableURL);
		//render as local table if it is an ajax request
        if($this->request->is('ajax'))
        {
            $this->render('table');
        }
	}
    
	public function view($id = null) {
		$this->Message->id = $id;
		if (!$this->Message->exists()) {
			throw new NotFoundException(__('Invalid message'));
		}
        $message = $this->Message->read(null, $id);
		$this->set('message', $message);
	}
	
	public function add() {
		if ($this->request->is('post')) {
			$this->Message->create();
			if ($this->Message->save($this->request->data)) {
				$this->Session->setFlash(__('The message has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->Message->id));
			} else {
				$this->Session->setFlash(__('The message could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            //add the named params as data
            foreach($this->request->params['named'] as $param => $value) {
                $columnType = $this->Message->getColumnType($param);
                if(!empty($columnType)) {
                    if(empty($this->request->data['Message'])) $this->request->data['Message'] = array();
                    $this->request->data['Message'][$param] = $value;
                }
            }
        }
		$senders = $this->Message->Sender->find('list', array('order' => $this->Message->Sender->displayField));
		$receivers = $this->Message->Receiver->find('list', array('order' => $this->Message->Receiver->displayField));
		$this->set(compact('senders', 'receivers'));
	}
	
	public function edit($id = null) {
		$this->Message->id = $id;
		if (!$this->Message->exists()) {
			throw new NotFoundException(__('Invalid message'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Message->save($this->request->data)) {
				$this->Session->setFlash(__('The message has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->Message->id));
			} else {
				$this->Session->setFlash(__('The message could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            $message = $this->Message->read(null, $id);
			$this->request->data = $message;
		}
		$senders = $this->Message->Sender->find('list', array('order' => $this->Message->Sender->displayField));
		$receivers = $this->Message->Receiver->find('list', array('order' => $this->Message->Receiver->displayField));
		$this->set(compact('senders', 'receivers'));
	}
	
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Message->id = $id;
		if (!$this->Message->exists()) {
			throw new NotFoundException(__('Invalid message'));
		}
		if ($this->Message->delete()) {
			$this->Session->setFlash(__('Message deleted'), 'default', array(), 'good');
            $this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Message was not deleted'), 'default', array(), 'bad');
		$this->redirect(array('action' => 'index'));
	}

}
