 <?php
App::uses('AdminAppController', 'Admin.Controller');
/**
 * AdminAssessmentsController
 *
 * @property Assessment $Assessment
 */
class AdminAssessmentsController extends AdminAppController {

	public $uses = array('Assessment');

    public function index() {
	    $conditions = array();
        $assessmentsTableURL = array('controller' => 'admin_assessments', 'action' => 'index');

        //join get query & named params
        $params = array_merge($this->request->params['named']);
        foreach($this->request->query as $key => $value) $params[$key] = $value;

        foreach($params as $key => $value) {
            $split = explode('-', $key);
            $modelName = (sizeof($split) > 1) ? $split[0] : 'Assessment';
            $property = (sizeof($split) > 1) ? $split[1] : $key;
            if($modelName == 'Assessment' || !empty($this->Assessment->belongsTo[$modelName])) {
                $this->loadModel($modelName);
                $modelObj = new $modelName();
                if(!empty($modelObj)) {
                    $columnType = $modelObj->getColumnType($property);
                    if(!empty($columnType)){
                        //add it to url
                        $assessmentsTableURL[$key] = $value;
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

		$this->Assessment->recursive = 0;
		$this->set('assessments', $this->Paginator->paginate('Assessment', $conditions, array()));
		$this->set('assessmentsTableURL', $assessmentsTableURL);
		//render as local table if it is an ajax request
        if($this->request->is('ajax'))
        {
            $this->render('table');
        }
	}
    
	public function view($id = null) {
		$this->Assessment->id = $id;
		if (!$this->Assessment->exists()) {
			throw new NotFoundException(__('Invalid assessment'));
		}
        	$assessment = $this->Assessment->read(null, $id);
		$this->set('assessment', $assessment);
		
		$this->loadModel('Option');
		$this->Option->recursive = 0;
		$conditions = array("Option.opt_assessment_id" => $id);
		//http://localhost/admin/modules/view/2/page:2#tabs-3
        	$optionsTableURL = array('controller' => 'admin_assessments', 'action' => 'view', $id);
        
		$this->set('options', $this->Paginator->paginate('Option', $conditions, array()));
		$this->set('optionsTableURL', $optionsTableURL);
	}
	
	public function add($module_id = 0) {
		$this->_loadParent("Module", $module_id);
		if ($this->request->is('post')) {
			$date = new DateTime();
			if(empty($this->request->data['Assessment']['ass_created']))
				$this->request->data['Assessment']['ass_created'] = $date->getTimestamp();
			
			$this->Assessment->create();
			if ($this->Assessment->save($this->request->data)) {
				$this->Session->setFlash(__('The assessment has been saved'), 'default', array(), 'good');
                		if($this->Session->check('Url.referer'))
					$this->redirect($this->Session->read('Url.referer'));
                		$this->redirect($this->referer());
			} else {
				$this->Session->setFlash(__('The assessment could not be saved. Please, try again.'), 'default', array(), 'bad');
			}
		} else {
			//add the named params as data
			foreach($this->request->params['named'] as $param => $value) {
				$columnType = $this->Assessment->getColumnType($param);
				if(!empty($columnType)) {
					if(empty($this->request->data['Assessment'])) $this->request->data['Assessment'] = array();
					$this->request->data['Assessment'][$param] = $value;
				}
			}
			
			$this->Session->write('Url.referer', $this->referer());
		}
	}
	
	public function edit($id = null) {
		$this->Assessment->id = $id;
		if (!$this->Assessment->exists()) {
			throw new NotFoundException(__('Invalid assessment'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$date = new DateTime();
			if(empty($this->request->data['Assessment']['ass_created']))
				$this->request->data['Assessment']['ass_created'] = $date->getTimestamp();
			
			if ($this->Assessment->save($this->request->data)) {
				$this->Session->setFlash(__('The assessment has been saved'), 'default', array(), 'good');
                	
                	if($this->Session->check('Url.referer'))
					$this->redirect($this->Session->read('Url.referer'));
                	$this->redirect($this->referer());
                		
		} else {
			$this->Session->setFlash(__('The assessment could not be saved. Please, try again.'), 'default', array(), 'bad');
		}
		} else {
            		$assessment = $this->Assessment->read(null, $id);
			$this->request->data = $assessment;
			$this->Session->write('Url.referer', $this->referer());
		}
	}
	
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Assessment->id = $id;
		if (!$this->Assessment->exists()) {
			throw new NotFoundException(__('Invalid assessment'));
		}
		if ($this->Assessment->delete()) {
			$this->Session->setFlash(__('Assessment deleted'), 'default', array(), 'good');
            $this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Assessment was not deleted'), 'default', array(), 'bad');
		$this->redirect(array('action' => 'index'));
	}

}
