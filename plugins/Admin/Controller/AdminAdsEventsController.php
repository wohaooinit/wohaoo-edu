<?php
App::uses('AdminAppController', 'Admin.Controller');
/**
 * AdminAdsEventsController
 *
 * @property AdsEvent $AdsEvent
 */
class AdminAdsEventsController extends AdminAppController {

	public $uses = array('AdsEvent');

    public function index() {
	    $conditions = array();
        $adsEventsTableURL = array('controller' => 'admin_ads_events', 'action' => 'index');

        //join get query & named params
        $params = array_merge($this->request->params['named']);
        foreach($this->request->query as $key => $value) $params[$key] = $value;

        foreach($params as $key => $value) {
            $split = explode('-', $key);
            $modelName = (sizeof($split) > 1) ? $split[0] : 'AdsEvent';
            $property = (sizeof($split) > 1) ? $split[1] : $key;
            if($modelName == 'AdsEvent' || !empty($this->AdsEvent->belongsTo[$modelName])) {
                $this->loadModel($modelName);
                $modelObj = new $modelName();
                if(!empty($modelObj)) {
                    $columnType = $modelObj->getColumnType($property);
                    if(!empty($columnType)){
                        //add it to url
                        $adsEventsTableURL[$key] = $value;
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

		$this->AdsEvent->recursive = 0;
		$this->set('adsEvents', $this->Paginator->paginate('AdsEvent', $conditions, array()));
		$this->set('adsEventsTableURL', $adsEventsTableURL);
		//render as local table if it is an ajax request
        if($this->request->is('ajax'))
        {
            $this->render('table');
        }
	}
    
	public function view($id = null) {
		$this->AdsEvent->id = $id;
		if (!$this->AdsEvent->exists()) {
			throw new NotFoundException(__('Invalid ads event'));
		}
        $adsEvent = $this->AdsEvent->read(null, $id);
		$this->set('adsEvent', $adsEvent);
	}
	
	public function add() {
		if ($this->request->is('post')) {
			$this->AdsEvent->create();
			if ($this->AdsEvent->save($this->request->data)) {
				$this->Session->setFlash(__('The ads event has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->AdsEvent->id));
			} else {
				$this->Session->setFlash(__('The ads event could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            //add the named params as data
            foreach($this->request->params['named'] as $param => $value) {
                $columnType = $this->AdsEvent->getColumnType($param);
                if(!empty($columnType)) {
                    if(empty($this->request->data['AdsEvent'])) $this->request->data['AdsEvent'] = array();
                    $this->request->data['AdsEvent'][$param] = $value;
                }
            }
        }
	}
	
	public function edit($id = null) {
		$this->AdsEvent->id = $id;
		if (!$this->AdsEvent->exists()) {
			throw new NotFoundException(__('Invalid ads event'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->AdsEvent->save($this->request->data)) {
				$this->Session->setFlash(__('The ads event has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->AdsEvent->id));
			} else {
				$this->Session->setFlash(__('The ads event could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            $adsEvent = $this->AdsEvent->read(null, $id);
			$this->request->data = $adsEvent;
		}
	}
	
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->AdsEvent->id = $id;
		if (!$this->AdsEvent->exists()) {
			throw new NotFoundException(__('Invalid ads event'));
		}
		if ($this->AdsEvent->delete()) {
			$this->Session->setFlash(__('Ads event deleted'), 'default', array(), 'good');
            $this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Ads event was not deleted'), 'default', array(), 'bad');
		$this->redirect(array('action' => 'index'));
	}

}
