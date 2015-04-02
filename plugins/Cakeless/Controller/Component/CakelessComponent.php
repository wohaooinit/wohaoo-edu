<?php
App::uses('lessc', 'Lib');
/**
 * Cakeless CakePHP plugin Component
 *
 */
class CakelessComponent extends Component {

	private $lessc = null;
	/* Callbacks
	------------------------------------------------------------------------- */

	public function initialize( Controller $controller, $settings = array() ) {

		// imports LESSPHP Class (only for debug modes)
		if ( Configure::read('debug') > 0 ) {

			App::import('Vendor', 'Cakeless.lessphp', array(
				'file' => 'lessphp' . DS . 'lessc.inc.php' )
			);
			if(!$this->lessc)
				$this->lessc = new lessc;
		}

	}



	/* Methods
	------------------------------------------------------------------------- */

	/**
	 * Compiles a LESS syntax file and saves the compiled version
	 */
	public function compile( $lessFile, $compiledFile ) {

		if (Configure::read('debug') > 0 ) {

			$this->lessc->checkedCompile( $lessFile, $compiledFile );

		}

	}
	
	/**
	 * Compiles a LESS syntax file and saves the compiled version
	 */
	public function setVariables($vars) {
		$this->log("setting variables to lessc, vars=" . var_export($vars, true), 'debug');
		if(!$this->lessc)
			die("unkown error");
		if (Configure::read('debug') > 0 ) {

			$this->lessc->setVariables($vars  );

		}

	}


}