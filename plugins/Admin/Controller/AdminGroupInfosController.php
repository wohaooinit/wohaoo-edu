<?php
App::uses('AdminAppController', 'Admin.Controller');
/**
 * AdminGroupInfosController
 *
 * @property GroupInfo $GroupInfo
 */
class AdminGroupInfosController extends AdminAppController {

	public $uses = array('GroupInfo');

    public function index() {
	    $conditions = array();
        $groupInfosTableURL = array('controller' => 'admin_group_infos', 'action' => 'index');

        //join get query & named params
        $params = array_merge($this->request->params['named']);
        foreach($this->request->query as $key => $value) $params[$key] = $value;

        foreach($params as $key => $value) {
            $split = explode('-', $key);
            $modelName = (sizeof($split) > 1) ? $split[0] : 'GroupInfo';
            $property = (sizeof($split) > 1) ? $split[1] : $key;
            if($modelName == 'GroupInfo' || !empty($this->GroupInfo->belongsTo[$modelName])) {
                $this->loadModel($modelName);
                $modelObj = new $modelName();
                if(!empty($modelObj)) {
                    $columnType = $modelObj->getColumnType($property);
                    if(!empty($columnType)){
                        //add it to url
                        $groupInfosTableURL[$key] = $value;
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

		$this->GroupInfo->recursive = 0;
		$this->set('groupInfos', $this->Paginator->paginate('GroupInfo', $conditions, array()));
		$this->set('groupInfosTableURL', $groupInfosTableURL);
		//render as local table if it is an ajax request
        if($this->request->is('ajax'))
        {
            $this->render('table');
        }
	}
    
	public function view($id = null) {
		$this->GroupInfo->id = $id;
		if (!$this->GroupInfo->exists()) {
			throw new NotFoundException(__('Invalid group info'));
		}
        $groupInfo = $this->GroupInfo->read(null, $id);
		$this->set('groupInfo', $groupInfo);
	}
	
	public function add() {
		if ($this->request->is('post')) {
			$this->GroupInfo->create();
			if ($this->GroupInfo->save($this->request->data)) {
				$this->Session->setFlash(__('The group info has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->GroupInfo->id));
			} else {
				$this->Session->setFlash(__('The group info could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            //add the named params as data
            foreach($this->request->params['named'] as $param => $value) {
                $columnType = $this->GroupInfo->getColumnType($param);
                if(!empty($columnType)) {
                    if(empty($this->request->data['GroupInfo'])) $this->request->data['GroupInfo'] = array();
                    $this->request->data['GroupInfo'][$param] = $value;
                }
            }
        }
		$categories = $this->GroupInfo->Category->find('list', array('order' => $this->GroupInfo->Category->displayField));
		$countries = $this->GroupInfo->Country->find('list', array('order' => $this->GroupInfo->Country->displayField));
		$creators = $this->GroupInfo->Creator->find('list', array('order' => $this->GroupInfo->Creator->displayField));
		$this->set(compact('categories', 'countries', 'creators'));
	}
	
	public function edit($id = null) {
		$this->GroupInfo->id = $id;
		if (!$this->GroupInfo->exists()) {
			throw new NotFoundException(__('Invalid group info'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->GroupInfo->save($this->request->data)) {
				$this->Session->setFlash(__('The group info has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->GroupInfo->id));
			} else {
				$this->Session->setFlash(__('The group info could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            $groupInfo = $this->GroupInfo->read(null, $id);
			$this->request->data = $groupInfo;
		}
		$categories = $this->GroupInfo->Category->find('list', array('order' => $this->GroupInfo->Category->displayField));
		$countries = $this->GroupInfo->Country->find('list', array('order' => $this->GroupInfo->Country->displayField));
		$creators = $this->GroupInfo->Creator->find('list', array('order' => $this->GroupInfo->Creator->displayField));
		$this->set(compact('categories', 'countries', 'creators'));
	}
	
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->GroupInfo->id = $id;
		if (!$this->GroupInfo->exists()) {
			throw new NotFoundException(__('Invalid group info'));
		}
		if ($this->GroupInfo->delete()) {
			$this->Session->setFlash(__('Group info deleted'), 'default', array(), 'good');
            $this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Group info was not deleted'), 'default', array(), 'bad');
		$this->redirect(array('action' => 'index'));
	}

}
