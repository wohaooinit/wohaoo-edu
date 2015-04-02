<?php
App::uses('AdminAppController', 'Admin.Controller');
/**
 * AdminBookmarkDirsController
 *
 * @property BookmarkDir $BookmarkDir
 */
class AdminBookmarkDirsController extends AdminAppController {

	public $uses = array('BookmarkDir');

    public function index() {
	    $conditions = array();
        $bookmarkDirsTableURL = array('controller' => 'admin_bookmark_dirs', 'action' => 'index');

        //join get query & named params
        $params = array_merge($this->request->params['named']);
        foreach($this->request->query as $key => $value) $params[$key] = $value;

        foreach($params as $key => $value) {
            $split = explode('-', $key);
            $modelName = (sizeof($split) > 1) ? $split[0] : 'BookmarkDir';
            $property = (sizeof($split) > 1) ? $split[1] : $key;
            if($modelName == 'BookmarkDir' || !empty($this->BookmarkDir->belongsTo[$modelName])) {
                $this->loadModel($modelName);
                $modelObj = new $modelName();
                if(!empty($modelObj)) {
                    $columnType = $modelObj->getColumnType($property);
                    if(!empty($columnType)){
                        //add it to url
                        $bookmarkDirsTableURL[$key] = $value;
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

		$this->BookmarkDir->recursive = 0;
		$this->set('bookmarkDirs', $this->Paginator->paginate('BookmarkDir', $conditions, array()));
		$this->set('bookmarkDirsTableURL', $bookmarkDirsTableURL);
		//render as local table if it is an ajax request
        if($this->request->is('ajax'))
        {
            $this->render('table');
        }
	}
    
	public function view($id = null) {
		$this->BookmarkDir->id = $id;
		if (!$this->BookmarkDir->exists()) {
			throw new NotFoundException(__('Invalid bookmark dir'));
		}
        $bookmarkDir = $this->BookmarkDir->read(null, $id);
		$this->set('bookmarkDir', $bookmarkDir);
	}
	
	public function add() {
		if ($this->request->is('post')) {
			$this->BookmarkDir->create();
			if ($this->BookmarkDir->save($this->request->data)) {
				$this->Session->setFlash(__('The bookmark dir has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->BookmarkDir->id));
			} else {
				$this->Session->setFlash(__('The bookmark dir could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            //add the named params as data
            foreach($this->request->params['named'] as $param => $value) {
                $columnType = $this->BookmarkDir->getColumnType($param);
                if(!empty($columnType)) {
                    if(empty($this->request->data['BookmarkDir'])) $this->request->data['BookmarkDir'] = array();
                    $this->request->data['BookmarkDir'][$param] = $value;
                }
            }
        }
	}
	
	public function edit($id = null) {
		$this->BookmarkDir->id = $id;
		if (!$this->BookmarkDir->exists()) {
			throw new NotFoundException(__('Invalid bookmark dir'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->BookmarkDir->save($this->request->data)) {
				$this->Session->setFlash(__('The bookmark dir has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->BookmarkDir->id));
			} else {
				$this->Session->setFlash(__('The bookmark dir could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            $bookmarkDir = $this->BookmarkDir->read(null, $id);
			$this->request->data = $bookmarkDir;
		}
	}
	
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->BookmarkDir->id = $id;
		if (!$this->BookmarkDir->exists()) {
			throw new NotFoundException(__('Invalid bookmark dir'));
		}
		if ($this->BookmarkDir->delete()) {
			$this->Session->setFlash(__('Bookmark dir deleted'), 'default', array(), 'good');
            $this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Bookmark dir was not deleted'), 'default', array(), 'bad');
		$this->redirect(array('action' => 'index'));
	}

}
