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

			App::import('Vendor', 'Cakeless.lessphp', array(
				'file' => 'lessphp' . DS . 'lessc.inc.php' )
			);
			if(!$this->lessc)
				$this->lessc = new lessc;

	}



	/* Methods
	------------------------------------------------------------------------- */

	/**
	 * Compiles a LESS syntax file and saves the compiled version
	 */
	public function compile( $lessFile, $compiledFile ) {
			$this->lessc->checkedCompile( $lessFile, $compiledFile );

	}
	
	/**
	 * Compiles a LESS syntax file and saves the compiled version
	 */
	public function setVariables($vars) {
		$this->log("setting variables to lessc, vars=" . var_export($vars, true), 'debug');
		if(!$this->lessc)
			die("unkown error");
		
	       $this->lessc->setVariables($vars  );

	}


}