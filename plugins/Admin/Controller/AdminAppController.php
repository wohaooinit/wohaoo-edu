 <?php
App::uses('AppController', 'Controller');
class  AdminAppController extends AppController {

    public $backendPluginName = null;
    public $backendPluginNameUnderscored = null;

	public $helpers = array('Form', 'Html', 'Html2', 'Js', 'Session', 'Time');
	public $components = array(
        'Session',
        'Auth',
        'RequestHandler',
        'Paginator'
    );

    public function __construct($request = null, $response = null) {
        parent::__construct($request, $response);
        $this->backendPluginName = $this->plugin;
        $this->backendPluginNameUnderscored = Inflector::underscore($this->backendPluginName);
        $this->set('backendPluginName', $this->backendPluginName);
        $this->set('backendPluginNameUnderscored', $this->backendPluginNameUnderscored);
    }
    
    protected function _loadParent($parentModel = "", $parent_id = 0){
    		$this->log("<_loadParent parentModel=$parentModel parent_id=${parent_id}>", 'debug');
		$this->loadModel($parentModel);
		$this->$parentModel->id = $parent_id;
		if (!$this->$parentModel->exists()) {
			throw new NotFoundException(__("Invalid %s", $parentModel));
		}
        	$parent = $this->$parentModel->read(null, $parent_id);
		$this->set(Inflector::underscore($parentModel), $parent);
		$this->log("</_loadParent>", 'debug');
	}

	public function beforeFilter() {
		//remove this block after you created the first user
		//$this->Auth->allow();
		//return;
		//end remove this block after you created the first user
		//remove this block after you created the first user
		if(!empty($this->request->params['requested'])){
			$this->Auth->allow();
		}
		$this->Auth->authorize = array('Controller');
		$this->Auth->authenticate = array('Admin.Admin' => array(
					'fields' => array('username' => 'usr_email', 'password' => 'usr_password')
				)
		);

		$this->Auth->loginAction = array('controller' => 'admin_users',
			'action' => 'login', 'plugin' => $this->backendPluginName);
		$this->Auth->logoutRedirect = "/" . strtolower($this->backendPluginName);
		$this->Auth->loginRedirect = "/" . strtolower($this->backendPluginName);
	}

	public function beforeRender() {
		$menuItems = array('items' => array());

		$menuItems['items']['General'] = array('target' => "/{$this->backendPluginNameUnderscored}", 'items' => array());
		$menuItems['items']['General']['items'][__('Translations')] = array('target' => "/{$this->backendPluginNameUnderscored}/translations", 'items' => array());
		$menuItems['items']['General']['items'][__('Users')] = array('target' => "/{$this->backendPluginNameUnderscored}/users", 'items' => array());
		$menuItems['items']['General']['items'][__('Students')] = array('target' => "/{$this->backendPluginNameUnderscored}/students", 
			 	'items' => array());
		$menuItems['items']['General']['items'][__('Curriculums')] = array('target' => "/{$this->backendPluginNameUnderscored}/curriculums", 
			 	'items' => array());
		$menuItems['items']['General']['items'][__('Modules')] = array('target' => "/{$this->backendPluginNameUnderscored}/modules", 'items' => array());
		$menuItems['items']['General']['items'][__('Assessments')] = array('target' => "/{$this->backendPluginNameUnderscored}/assessments",
			 		 'items' => array());
		$menuItems['items']['General']['items'][__('Payments')] = array('target' => "/{$this->backendPluginNameUnderscored}/payments", 'items' => array());
		$menuItems['items']['Logout'] = array ('target' => "/{$this->backendPluginNameUnderscored}/users/logout", 'items' => array ());

		$this->set('menuItems', $menuItems);
	}

	public function isAuthorized($user = null) {
		if(!empty($user)) {
			return true;
		}
        }
}

