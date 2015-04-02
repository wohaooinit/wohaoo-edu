<?php
App::uses('AdminAppController', 'Admin.Controller');
/**
 * AdminBookmarksController
 *
 * @property Bookmark $Bookmark
 */
class AdminBookmarksController extends AdminAppController {

	public $uses = array('Bookmark');

    public function index() {
	    $conditions = array();
        $bookmarksTableURL = array('controller' => 'admin_bookmarks', 'action' => 'index');

        //join get query & named params
        $params = array_merge($this->request->params['named']);
        foreach($this->request->query as $key => $value) $params[$key] = $value;

        foreach($params as $key => $value) {
            $split = explode('-', $key);
            $modelName = (sizeof($split) > 1) ? $split[0] : 'Bookmark';
            $property = (sizeof($split) > 1) ? $split[1] : $key;
            if($modelName == 'Bookmark' || !empty($this->Bookmark->belongsTo[$modelName])) {
                $this->loadModel($modelName);
                $modelObj = new $modelName();
                if(!empty($modelObj)) {
                    $columnType = $modelObj->getColumnType($property);
                    if(!empty($columnType)){
                        //add it to url
                        $bookmarksTableURL[$key] = $value;
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

		$this->Bookmark->recursive = 0;
		$this->set('bookmarks', $this->Paginator->paginate('Bookmark', $conditions, array()));
		$this->set('bookmarksTableURL', $bookmarksTableURL);
		//render as local table if it is an ajax request
        if($this->request->is('ajax'))
        {
            $this->render('table');
        }
	}
    
	public function view($id = null) {
		$this->Bookmark->id = $id;
		if (!$this->Bookmark->exists()) {
			throw new NotFoundException(__('Invalid bookmark'));
		}
        $bookmark = $this->Bookmark->read(null, $id);
		$this->set('bookmark', $bookmark);
	}
	
	public function add() {
		if ($this->request->is('post')) {
			$this->Bookmark->create();
			if ($this->Bookmark->save($this->request->data)) {
				$this->Session->setFlash(__('The bookmark has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->Bookmark->id));
			} else {
				$this->Session->setFlash(__('The bookmark could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            //add the named params as data
            foreach($this->request->params['named'] as $param => $value) {
                $columnType = $this->Bookmark->getColumnType($param);
                if(!empty($columnType)) {
                    if(empty($this->request->data['Bookmark'])) $this->request->data['Bookmark'] = array();
                    $this->request->data['Bookmark'][$param] = $value;
                }
            }
        }
		$dirs = $this->Bookmark->Dir->find('list', array('order' => $this->Bookmark->Dir->displayField));
		$this->set(compact('dirs'));
	}
	
	public function edit($id = null) {
		$this->Bookmark->id = $id;
		if (!$this->Bookmark->exists()) {
			throw new NotFoundException(__('Invalid bookmark'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Bookmark->save($this->request->data)) {
				$this->Session->setFlash(__('The bookmark has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->Bookmark->id));
			} else {
				$this->Session->setFlash(__('The bookmark could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            $bookmark = $this->Bookmark->read(null, $id);
			$this->request->data = $bookmark;
		}
		$dirs = $this->Bookmark->Dir->find('list', array('order' => $this->Bookmark->Dir->displayField));
		$this->set(compact('dirs'));
	}
	
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Bookmark->id = $id;
		if (!$this->Bookmark->exists()) {
			throw new NotFoundException(__('Invalid bookmark'));
		}
		if ($this->Bookmark->delete()) {
			$this->Session->setFlash(__('Bookmark deleted'), 'default', array(), 'good');
            $this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Bookmark was not deleted'), 'default', array(), 'bad');
		$this->redirect(array('action' => 'index'));
	}

}
