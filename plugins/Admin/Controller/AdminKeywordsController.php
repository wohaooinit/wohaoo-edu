<?php
App::uses('AdminAppController', 'Admin.Controller');
/**
 * AdminKeywordsController
 *
 * @property Keyword $Keyword
 */
class AdminKeywordsController extends AdminAppController {

	public $uses = array('Keyword');

    public function index() {
	    $conditions = array();
        $keywordsTableURL = array('controller' => 'admin_keywords', 'action' => 'index');

        //join get query & named params
        $params = array_merge($this->request->params['named']);
        foreach($this->request->query as $key => $value) $params[$key] = $value;

        foreach($params as $key => $value) {
            $split = explode('-', $key);
            $modelName = (sizeof($split) > 1) ? $split[0] : 'Keyword';
            $property = (sizeof($split) > 1) ? $split[1] : $key;
            if($modelName == 'Keyword' || !empty($this->Keyword->belongsTo[$modelName])) {
                $this->loadModel($modelName);
                $modelObj = new $modelName();
                if(!empty($modelObj)) {
                    $columnType = $modelObj->getColumnType($property);
                    if(!empty($columnType)){
                        //add it to url
                        $keywordsTableURL[$key] = $value;
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

		$this->Keyword->recursive = 0;
		$this->set('keywords', $this->Paginator->paginate('Keyword', $conditions, array()));
		$this->set('keywordsTableURL', $keywordsTableURL);
		//render as local table if it is an ajax request
        if($this->request->is('ajax'))
        {
            $this->render('table');
        }
	}
    
	public function view($id = null) {
		$this->Keyword->id = $id;
		if (!$this->Keyword->exists()) {
			throw new NotFoundException(__('Invalid keyword'));
		}
        $keyword = $this->Keyword->read(null, $id);
		$this->set('keyword', $keyword);
	}
	
	public function add() {
		if ($this->request->is('post')) {
			$this->Keyword->create();
			if ($this->Keyword->save($this->request->data)) {
				$this->Session->setFlash(__('The keyword has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->Keyword->id));
			} else {
				$this->Session->setFlash(__('The keyword could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            //add the named params as data
            foreach($this->request->params['named'] as $param => $value) {
                $columnType = $this->Keyword->getColumnType($param);
                if(!empty($columnType)) {
                    if(empty($this->request->data['Keyword'])) $this->request->data['Keyword'] = array();
                    $this->request->data['Keyword'][$param] = $value;
                }
            }
        }
	}
	
	public function edit($id = null) {
		$this->Keyword->id = $id;
		if (!$this->Keyword->exists()) {
			throw new NotFoundException(__('Invalid keyword'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Keyword->save($this->request->data)) {
				$this->Session->setFlash(__('The keyword has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->Keyword->id));
			} else {
				$this->Session->setFlash(__('The keyword could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            $keyword = $this->Keyword->read(null, $id);
			$this->request->data = $keyword;
		}
	}
	
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Keyword->id = $id;
		if (!$this->Keyword->exists()) {
			throw new NotFoundException(__('Invalid keyword'));
		}
		if ($this->Keyword->delete()) {
			$this->Session->setFlash(__('Keyword deleted'), 'default', array(), 'good');
            $this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Keyword was not deleted'), 'default', array(), 'bad');
		$this->redirect(array('action' => 'index'));
	}

}
