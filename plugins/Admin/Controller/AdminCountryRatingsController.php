<?php
App::uses('AdminAppController', 'Admin.Controller');
/**
 * AdminCountryRatingsController
 *
 * @property CountryRating $CountryRating
 */
class AdminCountryRatingsController extends AdminAppController {

	public $uses = array('CountryRating');

    public function index() {
	    $conditions = array();
        $countryRatingsTableURL = array('controller' => 'admin_country_ratings', 'action' => 'index');

        //join get query & named params
        $params = array_merge($this->request->params['named']);
        foreach($this->request->query as $key => $value) $params[$key] = $value;

        foreach($params as $key => $value) {
            $split = explode('-', $key);
            $modelName = (sizeof($split) > 1) ? $split[0] : 'CountryRating';
            $property = (sizeof($split) > 1) ? $split[1] : $key;
            if($modelName == 'CountryRating' || !empty($this->CountryRating->belongsTo[$modelName])) {
                $this->loadModel($modelName);
                $modelObj = new $modelName();
                if(!empty($modelObj)) {
                    $columnType = $modelObj->getColumnType($property);
                    if(!empty($columnType)){
                        //add it to url
                        $countryRatingsTableURL[$key] = $value;
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

		$this->CountryRating->recursive = 0;
		$this->set('countryRatings', $this->Paginator->paginate('CountryRating', $conditions, array()));
		$this->set('countryRatingsTableURL', $countryRatingsTableURL);
		//render as local table if it is an ajax request
        if($this->request->is('ajax'))
        {
            $this->render('table');
        }
	}
    
	public function view($id = null) {
		$this->CountryRating->id = $id;
		if (!$this->CountryRating->exists()) {
			throw new NotFoundException(__('Invalid country rating'));
		}
        $countryRating = $this->CountryRating->read(null, $id);
		$this->set('countryRating', $countryRating);
	}
	
	public function add() {
		if ($this->request->is('post')) {
			$this->CountryRating->create();
			if ($this->CountryRating->save($this->request->data)) {
				$this->Session->setFlash(__('The country rating has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->CountryRating->id));
			} else {
				$this->Session->setFlash(__('The country rating could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            //add the named params as data
            foreach($this->request->params['named'] as $param => $value) {
                $columnType = $this->CountryRating->getColumnType($param);
                if(!empty($columnType)) {
                    if(empty($this->request->data['CountryRating'])) $this->request->data['CountryRating'] = array();
                    $this->request->data['CountryRating'][$param] = $value;
                }
            }
        }
	}
	
	public function edit($id = null) {
		$this->CountryRating->id = $id;
		if (!$this->CountryRating->exists()) {
			throw new NotFoundException(__('Invalid country rating'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->CountryRating->save($this->request->data)) {
				$this->Session->setFlash(__('The country rating has been saved'), 'default', array(), 'good');
                $this->redirect(array('action' => 'view', $this->CountryRating->id));
			} else {
				$this->Session->setFlash(__('The country rating could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
            $countryRating = $this->CountryRating->read(null, $id);
			$this->request->data = $countryRating;
		}
	}
	
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->CountryRating->id = $id;
		if (!$this->CountryRating->exists()) {
			throw new NotFoundException(__('Invalid country rating'));
		}
		if ($this->CountryRating->delete()) {
			$this->Session->setFlash(__('Country rating deleted'), 'default', array(), 'good');
            $this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Country rating was not deleted'), 'default', array(), 'bad');
		$this->redirect(array('action' => 'index'));
	}

}
