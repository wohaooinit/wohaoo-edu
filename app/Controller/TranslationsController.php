<?php

App::uses('EduController', 'Controller');
/**
 * TranslationsController
 *
 * @property Translation $Translation
 */
class TranslationsController extends EduController {
	public function database($orig_lang = "", $trans_langs=array()){
		$trans_langs = isset($this->request->query['langs'])?
				str_replace(",", "|", $this->request->query['langs']) : $orig_lang;
		if($trans_langs === 'all'){
			$trans_langs  = '.';
		}
		$conditions = array("Translation.t9n_orig_lang" => $orig_lang,
							"Translation.t9n_trans_lang ~ " => $trans_langs);
		$translations = $this->Translation->find('all', array('conditions' => $conditions));
		$this->set('translations', $translations);
		
		$this->RequestHandler->respondAs("text/javascript") ;
	}
	
	public function translate($trans_lang = "", $orig_text = "", $orig_lang = "en"){
		$message = "";
		$error = "";
		$trans_text = $orig_text;
		
		$orig_lang = isset($this->request->data['Translation']['t9n_orig_lang'])?
				$this->request->data['Translation']['t9n_orig_lang']: $orig_lang;
		
		$orig_text = isset($this->request->data['Translation']['t9n_orig_text'])?
				$this->request->data['Translation']['t9n_orig_text']: $orig_text;
		
		$trans_lang = isset($this->request->data['Translation']['t9n_trans_lang'])?
				$this->request->data['Translation']['t9n_trans_lang']: $trans_lang;
		//$this->log("<translate trans_lang=${trans_lang} orig_text=${orig_text} orig_lang=${orig_lang}>", 'debug');
					
		
		$this->viewClass = "Json";
		
		$db = $this->Translation->getDataSource();
		$db->begin();
		try{	
			//check if  a translation already exists
			//$this->log("check if  a translation already exists", 'debug');
			$guid = md5($orig_text);
			$t9n = $this->Translation->findByT9nGuid($guid);
			if($t9n){
				$trans_text = $t9n['Translation']['t9n_trans_text'];
				throw new Exception();
			}
			//validate dest and oig langs
			//$this->log("validate dest and oig langs", 'debug');
			$this->loadModel('Lang');
			$orig = $this->Lang->findByLanCode($orig_lang);
			if(!$orig){
				$error = __('Invalid Language Code: %s', $orig_lang);
				throw new Exception($error);
			}
			
			$trans = $this->Lang->findByLanCode($trans_lang);
			if(!$trans){
				$error = __('Invalid Language Code: %s', $trans_lang);
				throw new Exception($error);
			}
			
			//$this->log("creating translation object", 'debug');
			
			$this->Translation->create();
			$this->Translation->data = array('Translation' => array());
			//t9n_guid
			$this->Translation->data['Translation']['t9n_guid'] = $guid ;
			//t9n_orig_lang
			$this->Translation->data['Translation']['t9n_orig_lang'] = $orig_lang ;
			//t9n_orig_text
			$this->Translation->data['Translation']['t9n_orig_text'] = $orig_text ;
			//t9n_trans_lang
			$this->Translation->data['Translation']['t9n_trans_lang'] = $trans_lang ;
			//t9n_trans_text
			$this->Translation->data['Translation']['t9n_trans_text'] = $orig_text ;
			//t9n_modified
			$date = new DateTime();
			$now = $date->getTimestamp();
			$this->Translation->data['Translation']['t9n_modified'] = $now ;
			if (!$this->Translation->save($this->Translation->data)) {
				$error = __('The new translation cannot be saved. Please');
				throw new Exception($error);
			}
			
			$db->commit();
		}catch(Exception $e){
			$this->log($e->getMessage());
			$db->rollback();
		}
		
		if(!empty($this->request->params["requested"])){
			return $trans_text;
		}else{
			$this->set(compact("error", "trans_text"));
			$this->set("_serialize", array("error", "trans_text"));
		}
		//$this->log("</translate>", 'debug');
	}	
}
?>