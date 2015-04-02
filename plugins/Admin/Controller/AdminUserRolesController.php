<?php
App::uses('AdminAppController', 'Admin.Controller');
/**
 * AdminUserRolesController
 *
 * @property UserRole $UserRole
 */
class AdminUserRolesController extends AdminAppController {

	public $uses = array('UserRole');

    public function index() {
	    $conditions = array();
        $userRolesTableURL = array('controller' => 'admin_user_roles', 'action' => 'index');

        //join get query & named params
        $params = array_merge($this->request->params['named']);
        foreach($this->request->query as $key => $value) $params[$key] = $value;

        foreach($params as $key => $value) {
            $split = explode('-', $key);
            $modelName = (sizeof($split) > 1) ? $split[0] : 'UserRole';
            $property = (sizeof($split) > 1) ? $split[1] : $key;
            if($modelName == 'UserRole' || !empty($this->UserRole->belongsTo[$modelName])) {
                $this->loadModel($modelName);
                $modelObj = new $modelName();
                if(!empty($modelObj)) {
                    $columnType = $modelObj->getColumnType($property);
                    if(!empty($columnType)){
                        //add it to url
                        $userRolesTableURL[$key] = $value;
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

		$this->UserRole->recursive = 0;
		$this->set('userRoles', $this->Paginator->paginate('UserRole', $conditions, array()));
		$this->set('userRolesTableURL', $userRolesTableURL);
		//render as local table if it is an ajax request
        if($this->request->is('ajax'))
        {
            $this->render('table');
        }
	}
    
	public function view($id = null) {
		$this->UserRole->id = $id;
		if (!$this->UserRole->exists()) {
			throw new NotFoundException(__('Invalid user role'));
		}
        $userRole = $this->UserRole->read(null, $id);
		$this->set('userRole', $userRole);
	}
	
	public function add() {
		if ($this->request->is('post')) {
			$this->UserRole->create();
			if ($this->UserRole->save($this->request->data)) {
				$this->Session->setFlash(__('The user role has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->UserRole->id));
			} else {
				$this->Session->setFlash(__('The user role could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            //add the named params as data
            foreach($this->request->params['named'] as $param => $value) {
                $columnType = $this->UserRole->getColumnType($param);
                if(!empty($columnType)) {
                    if(empty($this->request->data['UserRole'])) $this->request->data['UserRole'] = array();
                    $this->request->data['UserRole'][$param] = $value;
                }
            }
        }
	}
	
	public function edit($id = null) {
		$this->UserRole->id = $id;
		if (!$this->UserRole->exists()) {
			throw new NotFoundException(__('Invalid user role'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->UserRole->save($this->request->data)) {
				$this->Session->setFlash(__('The user role has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->UserRole->id));
			} else {
				$this->Session->setFlash(__('The user role could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            $userRole = $this->UserRole->read(null, $id);
			$this->request->data = $userRole;
		}
	}
	
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->UserRole->id = $id;
		if (!$this->UserRole->exists()) {
			throw new NotFoundException(__('Invalid user role'));
		}
		if ($this->UserRole->delete()) {
			$this->Session->setFlash(__('User role deleted'), 'default', array(), 'good');
            $this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('User role was not deleted'), 'default', array(), 'bad');
		$this->redirect(array('action' => 'index'));
	}

}
