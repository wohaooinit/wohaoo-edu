<?php
App::uses('AdminAppController', 'Admin.Controller');
/**
 * AdminGroupMembersController
 *
 * @property GroupMember $GroupMember
 */
class AdminGroupMembersController extends AdminAppController {

	public $uses = array('GroupMember');

    public function index() {
	    $conditions = array();
        $groupMembersTableURL = array('controller' => 'admin_group_members', 'action' => 'index');

        //join get query & named params
        $params = array_merge($this->request->params['named']);
        foreach($this->request->query as $key => $value) $params[$key] = $value;

        foreach($params as $key => $value) {
            $split = explode('-', $key);
            $modelName = (sizeof($split) > 1) ? $split[0] : 'GroupMember';
            $property = (sizeof($split) > 1) ? $split[1] : $key;
            if($modelName == 'GroupMember' || !empty($this->GroupMember->belongsTo[$modelName])) {
                $this->loadModel($modelName);
                $modelObj = new $modelName();
                if(!empty($modelObj)) {
                    $columnType = $modelObj->getColumnType($property);
                    if(!empty($columnType)){
                        //add it to url
                        $groupMembersTableURL[$key] = $value;
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

		$this->GroupMember->recursive = 0;
		$this->set('groupMembers', $this->Paginator->paginate('GroupMember', $conditions, array()));
		$this->set('groupMembersTableURL', $groupMembersTableURL);
		//render as local table if it is an ajax request
        if($this->request->is('ajax'))
        {
            $this->render('table');
        }
	}
    
	public function view($id = null) {
		$this->GroupMember->id = $id;
		if (!$this->GroupMember->exists()) {
			throw new NotFoundException(__('Invalid group member'));
		}
        $groupMember = $this->GroupMember->read(null, $id);
		$this->set('groupMember', $groupMember);
	}
	
	public function add() {
		if ($this->request->is('post')) {
			$this->GroupMember->create();
			if ($this->GroupMember->save($this->request->data)) {
				$this->Session->setFlash(__('The group member has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->GroupMember->id));
			} else {
				$this->Session->setFlash(__('The group member could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            //add the named params as data
            foreach($this->request->params['named'] as $param => $value) {
                $columnType = $this->GroupMember->getColumnType($param);
                if(!empty($columnType)) {
                    if(empty($this->request->data['GroupMember'])) $this->request->data['GroupMember'] = array();
                    $this->request->data['GroupMember'][$param] = $value;
                }
            }
        }
		$users = $this->GroupMember->User->find('list', array('order' => $this->GroupMember->User->displayField));
		$this->set(compact('users'));
	}
	
	public function edit($id = null) {
		$this->GroupMember->id = $id;
		if (!$this->GroupMember->exists()) {
			throw new NotFoundException(__('Invalid group member'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->GroupMember->save($this->request->data)) {
				$this->Session->setFlash(__('The group member has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->GroupMember->id));
			} else {
				$this->Session->setFlash(__('The group member could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            $groupMember = $this->GroupMember->read(null, $id);
			$this->request->data = $groupMember;
		}
		$users = $this->GroupMember->User->find('list', array('order' => $this->GroupMember->User->displayField));
		$this->set(compact('users'));
	}
	
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->GroupMember->id = $id;
		if (!$this->GroupMember->exists()) {
			throw new NotFoundException(__('Invalid group member'));
		}
		if ($this->GroupMember->delete()) {
			$this->Session->setFlash(__('Group member deleted'), 'default', array(), 'good');
            $this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Group member was not deleted'), 'default', array(), 'bad');
		$this->redirect(array('action' => 'index'));
	}

}
