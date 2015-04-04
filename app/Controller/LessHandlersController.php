<?php
//App::uses('FootNationController', 'Controller');
App::uses('AppController', 'Controller');
//App::import('Lib', 'Constants');


/**
 * LessHandlersController
 *
 * @property Lang $Lang
 */
class LessHandlersController extends AppController {
	public $components = array(
			'Cakeless.Cakeless'
		);
		
	public function beforeFilter(){
		parent::beforeFilter();
	}
	public function preprocess($filename = null){
		$this->log("<less_handlers preprocess filename=$filename>", 'debug');
		$less_filename = WWW_ROOT . $filename . ".less";
		$this->log("less_filename=${less_filename}", 'debug');
		if($filename && file_exists($less_filename)){
			$css_filename = TMP . $filename . ".css";
			$this->log("css_filename=${css_filename}", 'debug');
			$this->log("compiling less file", 'debug');
			try{
				$less_path = pathinfo($filename, PATHINFO_DIRNAME);
				$this->log("less_path=" . $less_path, 'debug');
				//$less = new lessc;
				$this->Cakeless->setVariables(array(
				  "css-path" => "${less_path}"
				));
				$this->Cakeless->compile( $less_filename, $css_filename);
			}catch(Exception $e){
				$this->log($e->getMessage());
				throw $e;
			}
			$this->log("less file is compiled, serving css file ...", 'debug');
			$this->response->type('css');
			if(file_exists($css_filename)){
				$this->response->file($css_filename);
				$this->log("file is loaded it from file system", 'debug');
			}
			$this->log("</less_handlers.preprocess>", 'debug');
			return $this->response;
		}else{
			throw new Exception("returnfile ${filename} not found to client");
			$this->response->statusCode(404); //return a file not found to client
		}
		$this->log("</less_handlers.preprocess>", 'debug');
	}
}
?>