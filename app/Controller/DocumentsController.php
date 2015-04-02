<?php

App::uses('EduController', 'Controller');
/**
 * DocumentsController
 *
 * @property Document $Document
 */
class DocumentsController extends EduController {
	public $uses = array('Document');
	
	public function beforeFilter(){
		parent::beforeFilter();
		$this->Auth->allow('download', 'upload');
	}
	
	public function icon($id = null){
		if(isset($this->request->query['id']))
			$id = $this->request->query['id'];
		$this->log("<icon id=$id>", 'debug');
		$this->Document->id = $id;
		if(!$this->Document->exists()){
			throw new Exception("Unkown Document");
		}
		$file = $this->Document->read("fil_mime_type", $id);
		$mime = $file['Document']['fil_mime_type'];
		
		if($mime === "image/png" || $mime === "image/jpeg" ||
			$mime === "image/jpg" || $mime === "image/gif" ||
			$mime === "image/tiff" || $mime === "video/mp4" ||
			$mime === "audio/mpeg" || $mime === "video/avi" ||
			$mime === "video/mpeg"){
			$this->redirect("/js/file_uploader/multimedia.jpg");
		}else
		if($mime == "application/pdf" || $mime == "application/msword" || $mime == "document/docx" ||
			$mime == "text/plain"){
			$this->redirect("/js/file_uploader/document.png");
		}else
			$this->redirect("/js/file_uploader/unknown.jpg");
		$this->log("</icon>", 'debug');
	}

	/**
	 *This action is triggered when the Document object needs to be downloaded
	 *@param $id, the id of the Document object
	 */
	public function download($id = null){
		if(isset($this->request->query['id']))
			$id = $this->request->query['id'];
		if(isset($this->request->params['id']))
			$id = $this->request->params['id'];
			
		$this->log("<documents.download id=$id>", 'debug');
		
		$find_options = array(
			'conditions' => array('Document.id' => $id)
		);
		
		$file = $this->Document->find('first', $find_options);
		
		if($file){
			// send the right headers
			$fil_name = $file['Document']['fil_name'];
			$fil_path = sprintf("%s/%d_%s", TMP, $id,  $fil_name);
	
			/* read the source file */
			$this->log("source file path is ${fil_path}", 'debug');
			if(!file_exists($fil_path) || !is_file($fil_path)){
				$this->log('source file does not exists', 'debug');
				$fil_data=$file['Document']['fil_data'];
				$decoded=base64_decode($fil_data);
				file_put_contents($fil_path, $decoded);
			}
			//Serving thumbnail <${fil_path}> to client ...
			App::uses('Document', 'Utility');
			if(file_exists($fil_path))
				$this->response->file($fil_path);
			return $this->response;
		}
		$this->log('</documents.download>', 'debug');
	}
	
	/**
	 *This action is triggered when an file resource needs to be uploaded
	 */
	public function upload(){
		$this->log('<documents.upload>', 'debug');
		
		if(empty($this->request->params["requested"]) && !$this->request->is('post')){
			$this->log('wrong HTML form method ...', 'debug');
			return;
		}
		//HTML Form method is valid
		$error = '';
		$file_id = '';
		$message = '';
		
		$db = $this->Document->getDataSource();
		$db->begin();
		try{
			//adding any passed param
			$this->request->data  = array_merge($this->request->data, $this->request->params['pass']);
			
			//Checking resource file object ...
			if(!isset($this->request->data['Document']['file'])){
				$error = __('No file resource found in request');
				throw new Exception($error);
			}
			$errorFlag = $this->request->data['Document']['file']['error'];
			if($errorFlag){
				$error = __('Upload failed due to internal Server error');
				throw new Exception($error);
			}
			$name = $this->request->data['Document']['file']['name'];
			$size = $this->request->data['Document']['file']['size'];
			$tmp_name = $this->request->data['Document']['file']['tmp_name'];
			$tmp_data= isset($this->request->data['Document']['file']['tmp_data'])?
							$tmp_name = $this->request->data['Document']['file']['tmp_data']:"";
		
			$this->Document->create();
			if(!$tmp_data){
				if(!file_exists($tmp_name)){
					$error = __('Invalid tmp file');
					throw new Exception($error);
				}
				if($size > 104857600){//size > 100MB
					$error = __('Document file is too large');
					throw new Exception($error);
				}
				$im = file_get_contents($tmp_name);
				$imdata = base64_encode($im);
				$info = $this->Security->getfileinfo($tmp_name);
				$this->log("info:=". var_export($info, true), 'debug');
				$mime = $info["mimetype"];
   			}else{
   				$imdata = $tmp_data;
   				$mime = "document/binary";//TODO
   			}
			if(!$imdata){
				$error = __('Unable to encode the uploaded file ');
				throw new Exception($error);
			}				
			$this->Document->data['Document']['fil_name'] = $name;
			$this->Document->data['Document']['fil_mime_type'] = $mime;
			$this->Document->data['Document']['fil_data'] = $imdata;
			$date = new DateTime();
			$now = $date->getTimestamp();
			$this->Document->data['Document']['fil_created'] = $now;
			
			if(!$this->Document->save($this->Document->data)){
				$error = __("Unable to save the file data");
				throw new Exception($error);
			}
			$file_id = $this->Document->id;
			$message = __('Document has been successfully uploaded');
			$message = htmlentities($message, ENT_QUOTES);
			$db->commit();
		}catch(Exception $e){
			$db->rollback();
			$this->log($e->getMessage());
			if(!$error)
				$error = __(AppModel::$DEFAULT_ERROR_MESSAGE);
		}
		$data = array('error' => $error, 'file_id' => $file_id, 'message' => $message);
		if(!empty($this->request->params["requested"])){
			$this->log("</documents.upload>", 'debug');
			return $data;
		}
		$this->set($data);	
		
		$this->layout = 'empty';
		
		
		$this->log('</documents.upload>', 'debug');
	}
	
	public function resources($type = "", $model = "", $object_id = 0){
		$this->log("<Documents.resources type=$type model=$model, object_id=${object_id}>", 'debug');
		$this->loadModel('Resource');
		$this->Resource->recursive = 0;
		
		$options  = array();
		$options['conditions'] = array("Resource.res_type" => $type, 
							"Resource.res_model_id" => $object_id, 
							"Resource.res_model" => $model);
							
        	$options['order'] = array('Resource.res_created' => 'DESC');
        	
        	$resources = $this->Resource->find('all', $options);
        	
        	if(!empty($this->request->params['requested'])){
			$this->log("</Documents.resources>", 'debug');
			return $resources;
		}
		$this->set('resources', $resources);
	}
}