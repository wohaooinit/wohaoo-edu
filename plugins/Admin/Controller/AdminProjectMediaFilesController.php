<?php
App::uses('AdminAppController', 'Admin.Controller');
/**
 * AdminProjectMediaFilesController
 *
 * @property ProjectMediaFile $ProjectMediaFile
 */
class AdminProjectMediaFilesController extends AdminAppController {

	public $uses = array('ProjectMediaFile');

    public function index() {
	    $conditions = array();
        $projectMediaFilesTableURL = array('controller' => 'admin_project_media_files', 'action' => 'index');

        //join get query & named params
        $params = array_merge($this->request->params['named']);
        foreach($this->request->query as $key => $value) $params[$key] = $value;

        foreach($params as $key => $value) {
            $split = explode('-', $key);
            $modelName = (sizeof($split) > 1) ? $split[0] : 'ProjectMediaFile';
            $property = (sizeof($split) > 1) ? $split[1] : $key;
            if($modelName == 'ProjectMediaFile' || !empty($this->ProjectMediaFile->belongsTo[$modelName])) {
                $this->loadModel($modelName);
                $modelObj = new $modelName();
                if(!empty($modelObj)) {
                    $columnType = $modelObj->getColumnType($property);
                    if(!empty($columnType)){
                        //add it to url
                        $projectMediaFilesTableURL[$key] = $value;
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

		$this->ProjectMediaFile->recursive = 0;
		$this->set('projectMediaFiles', $this->Paginator->paginate('ProjectMediaFile', $conditions, array()));
		$this->set('projectMediaFilesTableURL', $projectMediaFilesTableURL);
		//render as local table if it is an ajax request
        if($this->request->is('ajax'))
        {
            $this->render('table');
        }
	}
    
	public function view($id = null) {
		$this->ProjectMediaFile->id = $id;
		if (!$this->ProjectMediaFile->exists()) {
			throw new NotFoundException(__('Invalid project media file'));
		}
        $projectMediaFile = $this->ProjectMediaFile->read(null, $id);
		$this->set('projectMediaFile', $projectMediaFile);
	}
	
	public function add() {
		if ($this->request->is('post')) {
			$this->ProjectMediaFile->create();
			if ($this->ProjectMediaFile->save($this->request->data)) {
				$this->Session->setFlash(__('The project media file has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->ProjectMediaFile->id));
			} else {
				$this->Session->setFlash(__('The project media file could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            //add the named params as data
            foreach($this->request->params['named'] as $param => $value) {
                $columnType = $this->ProjectMediaFile->getColumnType($param);
                if(!empty($columnType)) {
                    if(empty($this->request->data['ProjectMediaFile'])) $this->request->data['ProjectMediaFile'] = array();
                    $this->request->data['ProjectMediaFile'][$param] = $value;
                }
            }
        }
	}
	
	public function edit($id = null) {
		$this->ProjectMediaFile->id = $id;
		if (!$this->ProjectMediaFile->exists()) {
			throw new NotFoundException(__('Invalid project media file'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->ProjectMediaFile->save($this->request->data)) {
				$this->Session->setFlash(__('The project media file has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->ProjectMediaFile->id));
			} else {
				$this->Session->setFlash(__('The project media file could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            $projectMediaFile = $this->ProjectMediaFile->read(null, $id);
			$this->request->data = $projectMediaFile;
		}
	}
	
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->ProjectMediaFile->id = $id;
		if (!$this->ProjectMediaFile->exists()) {
			throw new NotFoundException(__('Invalid project media file'));
		}
		if ($this->ProjectMediaFile->delete()) {
			$this->Session->setFlash(__('Project media file deleted'), 'default', array(), 'good');
            $this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Project media file was not deleted'), 'default', array(), 'bad');
		$this->redirect(array('action' => 'index'));
	}

}
