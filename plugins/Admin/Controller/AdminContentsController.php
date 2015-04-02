<?php
App::uses('AdminAppController', 'Admin.Controller');
/**
 * AdminContentsController
 *
 * @property Content $Content
 */
class AdminContentsController extends AdminAppController {

	public $uses = array('Content');

    public function index() {
	    $conditions = array();
        $contentsTableURL = array('controller' => 'admin_contents', 'action' => 'index');

        //join get query & named params
        $params = array_merge($this->request->params['named']);
        foreach($this->request->query as $key => $value) $params[$key] = $value;

        foreach($params as $key => $value) {
            $split = explode('-', $key);
            $modelName = (sizeof($split) > 1) ? $split[0] : 'Content';
            $property = (sizeof($split) > 1) ? $split[1] : $key;
            if($modelName == 'Content' || !empty($this->Content->belongsTo[$modelName])) {
                $this->loadModel($modelName);
                $modelObj = new $modelName();
                if(!empty($modelObj)) {
                    $columnType = $modelObj->getColumnType($property);
                    if(!empty($columnType)){
                        //add it to url
                        $contentsTableURL[$key] = $value;
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

		$this->Content->recursive = 0;
		$this->set('contents', $this->Paginator->paginate('Content', $conditions, array()));
		$this->set('contentsTableURL', $contentsTableURL);
		//render as local table if it is an ajax request
        if($this->request->is('ajax'))
        {
            $this->render('table');
        }
	}
    
	public function view($id = null) {
		$this->Content->id = $id;
		if (!$this->Content->exists()) {
			throw new NotFoundException(__('Invalid content'));
		}
        $content = $this->Content->read(null, $id);
		$this->set('content', $content);
	}
	
	public function add() {
		if ($this->request->is('post')) {
			$this->Content->create();
			if ($this->Content->save($this->request->data)) {
				$this->Session->setFlash(__('The content has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->Content->id));
			} else {
				$this->Session->setFlash(__('The content could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            //add the named params as data
            foreach($this->request->params['named'] as $param => $value) {
                $columnType = $this->Content->getColumnType($param);
                if(!empty($columnType)) {
                    if(empty($this->request->data['Content'])) $this->request->data['Content'] = array();
                    $this->request->data['Content'][$param] = $value;
                }
            }
        }
	}
	
	public function edit($id = null) {
		$this->Content->id = $id;
		if (!$this->Content->exists()) {
			throw new NotFoundException(__('Invalid content'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Content->save($this->request->data)) {
				$this->Session->setFlash(__('The content has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->Content->id));
			} else {
				$this->Session->setFlash(__('The content could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            $content = $this->Content->read(null, $id);
			$this->request->data = $content;
		}
	}
	
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Content->id = $id;
		if (!$this->Content->exists()) {
			throw new NotFoundException(__('Invalid content'));
		}
		if ($this->Content->delete()) {
			$this->Session->setFlash(__('Content deleted'), 'default', array(), 'good');
            $this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Content was not deleted'), 'default', array(), 'bad');
		$this->redirect(array('action' => 'index'));
	}

}
