<?php
App::uses('AppController', 'Controller');
App::uses('FormatHelper', 'Helper');
App::uses('HtmlHelper', 'Helper');
App::uses('AppModel', 'Model');

class EduController extends AppController {
	public $helpers = array('Form', 'ExtendedForm', 'Menu', 'Html', 'Html2', 'Js', 'Session', 'Time', 'Format', 'Number', 'Security', 'Misc');
	public $components = array(
        'Session',
        'Auth',
        'RequestHandler',
        'Paginator',
        'RandomCode',
        'Security',
        'Cakeless.Cakeless',
        'Config',
        'Language'
    );

    public function __construct($request = null, $response = null) {
        parent::__construct($request, $response);
    }

	public function beforeFilter() {
		//remove this block after you created the first user
		$this->Auth->allow();
		//return;
		//end remove this block after you created the first user
		if(!empty($this->request->params['requested'])){
			$this->Auth->allow();
		}
		$this->Auth->authorize = array('Controller');
		$this->Auth->authenticate = array('Md5' => array(
					'fields' => array('username' => 'usr_telephone', 'password' => 'usr_password')
				)
		);
   
		$this->Auth->loginAction = array('controller' => 'users',
			'action' => 'login', 'plugin' => false);
		$this->Auth->logoutRedirect = array('controller' => 'pages', 
			'action' => 'display',  "home",  'plugin' => false);
		$this->Auth->loginRedirect = array('controller' => 'pages', 
			'action' => 'display',  "home", 'plugin' => false);
	}

	public function beforeRender() {
	}

	public function isAuthorized() {
		$user = $this->Auth->user();
		if(!empty($user)) {
			return true;
		}
		return false;
       }
       
       public function user_id(){
       		$user = $this->Auth->user();
       		if(!empty($user)) {
			return $user['id'];
		}
		return null;
       }
       
         // Formats the time as received by Twitter
	public function formatTime($d = null) {
		// Get now
	
		$now = new DateTime();
	
		// Push string date into an Date object
		$tweetDate = new Datetime();
		$tweetDate->setTimestamp($d);
		$date_diff = $now->diff($tweetDate);
		if($date_diff){ 
			$years = $date_diff->y; 
			$months = $date_diff->m;
			$days = $date_diff->d; 
			$hours = $date_diff->h;
			$mins = $date_diff->i;
			$secs = $date_diff->s;
		}
	
		$date_diff_str = $this->format_plural($years, __('year'),  __('years')) . ' ' .
					$this->format_plural($months,  __('month'),  __('months')) . ' ' .
					$this->format_plural($days,  __('day'), __('days'));
		if(!$date_diff_str)
				$date_diff_str .= $this->format_plural($hours,  __('hour'), __('hours')) . ' ' .
					$this->format_plural($mins,  __('min'), __('mins'))  . ' ' ;
		//what if the comment was just posted a few seconds ago?
		if(!$date_diff_str) //then show the seconds
			$date_diff_str = format_plural($secs, __('sec'), __('secs'));
	
		return trim($date_diff_str);
	}

	/**
	 *This function helps formatting text labels in plural
	 *examples: format_plural(0, "year") will return "", format_plural(1, "year") => "year", 
	 *format_plural(2, "year") => "years".
	 *@param $val, the quantity indicator
	 *@param $string, the singular version of the text
	 *@returns the text in the plural or an empty string
	 */
	public function format_plural($val, $string){
		if($val <= 0)
			return "";
		if($val == 1)
			return $string;
		return $string . 's';
	}
}

