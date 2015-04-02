<?php
App::uses('AdminAppController', 'Admin.Controller');
/**
 * AdminProjectMembersController
 *
 * @property ProjectMember $ProjectMember
 */
class AdminProjectMembersController extends AdminAppController {

	public $uses = array('ProjectMember');

    public function index() {
	    $conditions = array();
        $projectMembersTableURL = array('controller' => 'admin_project_members', 'action' => 'index');

        //join get query & named params
        $params = array_merge($this->request->params['named']);
        foreach($this->request->query as $key => $value) $params[$key] = $value;

        foreach($params as $key => $value) {
            $split = explode('-', $key);
            $modelName = (sizeof($split) > 1) ? $split[0] : 'ProjectMember';
            $property = (sizeof($split) > 1) ? $split[1] : $key;
            if($modelName == 'ProjectMember' || !empty($this->ProjectMember->belongsTo[$modelName])) {
                $this->loadModel($modelName);
                $modelObj = new $modelName();
                if(!empty($modelObj)) {
                    $columnType = $modelObj->getColumnType($property);
                    if(!empty($columnType)){
                        //add it to url
                        $projectMembersTableURL[$key] = $value;
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

		$this->ProjectMember->recursive = 0;
		$this->set('projectMembers', $this->Paginator->paginate('ProjectMember', $conditions, array()));
		$this->set('projectMembersTableURL', $projectMembersTableURL);
		//render as local table if it is an ajax request
        if($this->request->is('ajax'))
        {
            $this->render('table');
        }
	}
    
	public function view($id = null) {
		$this->ProjectMember->id = $id;
		if (!$this->ProjectMember->exists()) {
			throw new NotFoundException(__('Invalid project member'));
		}
        $projectMember = $this->ProjectMember->read(null, $id);
		$this->set('projectMember', $projectMember);
	}
	
	public function add() {
		if ($this->request->is('post')) {
			$this->ProjectMember->create();
			if ($this->ProjectMember->save($this->request->data)) {
				$this->Session->setFlash(__('The project member has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->ProjectMember->id));
			} else {
				$this->Session->setFlash(__('The project member could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            //add the named params as data
            foreach($this->request->params['named'] as $param => $value) {
                $columnType = $this->ProjectMember->getColumnType($param);
                if(!empty($columnType)) {
                    if(empty($this->request->data['ProjectMember'])) $this->request->data['ProjectMember'] = array();
                    $this->request->data['ProjectMember'][$param] = $value;
                }
            }
        }
		$users = $this->ProjectMember->User->find('list', array('order' => $this->ProjectMember->User->displayField));
		$this->set(compact('users'));
	}
	
	public function edit($id = null) {
		$this->ProjectMember->id = $id;
		if (!$this->ProjectMember->exists()) {
			throw new NotFoundException(__('Invalid project member'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->ProjectMember->save($this->request->data)) {
				$this->Session->setFlash(__('The project member has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->ProjectMember->id));
			} else {
				$this->Session->setFlash(__('The project member could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            $projectMember = $this->ProjectMember->read(null, $id);
			$this->request->data = $projectMember;
		}
		$users = $this->ProjectMember->User->find('list', array('order' => $this->ProjectMember->User->displayField));
		$this->set(compact('users'));
	}
	
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->ProjectMember->id = $id;
		if (!$this->ProjectMember->exists()) {
			throw new NotFoundException(__('Invalid project member'));
		}
		if ($this->ProjectMember->delete()) {
			$this->Session->setFlash(__('Project member deleted'), 'default', array(), 'good');
            $this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Project member was not deleted'), 'default', array(), 'bad');
		$this->redirect(array('action' => 'index'));
	}

}
