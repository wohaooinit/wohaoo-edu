<?php

App::uses('EduController', 'Controller');
/**
 * ImagesController
 *
 * @property Image $Image
 */
class ImagesController extends EduController {

	public $uses = array('Image');
	
	public function beforeFilter(){
		parent::beforeFilter();
		$this->Auth->allow('image', 'uploadImage');
	}

	/**
	 *This action is triggered when the thumbnail of a given Image object
	 *with specified width and height needs to be displayed
	 *@param $width, the requird width
	 *@param $height, the required height of the thumbnail
	 *@param $id, the id of the Image object
	 */
	public function image($id = null, $width = null, $height = null){
		$width = '';
		$height = '';
		if(isset($this->request->query['id']))
			$id = $this->request->query['id'];
		if(isset($this->request->query['width']))
			$width = $this->request->query['width'];
		if(isset($this->request->query['height']))
			$height = $this->request->query['height'];
		$id = $this->request->query['id'];
		$this->log("<images.show id=$id width=$width height=$height>", 'debug');
		
		$find_options = array(
			'conditions' => array('Image.id' => $id)
		);
		$image = $this->Image->find('first', $find_options);
		
		if($image){
			// send the right headers
			if($width && $height){
				$thumbnail_file = sprintf("%s/%d_w_%d_h_%d.png",
								TMP, $id, $width, $height);
				if(!file_exists($thumbnail_file)){
					$this->log("${thumbnail_file} does not exists. Creating new thumbnail ..", 'debug');
					$this->Image->id = $id;
					$this->Image->read();
					$thumbnail_file = $this->Image->createImageThumbnail($width, $height);
				}
				if($thumbnail_file && file_exists($thumbnail_file)){
					//Serving thumbnail <$thumbnail_file> to client ...
					$this->response->file($thumbnail_file);
					return $this->response;
				}else
					$this->response->statusCode(404); //return a file not found to client
			}else{
				//serve default
				$image_file_name = $image['Image']['img_name'];
				$image_file_path = sprintf("%s/%d_%s", TMP, $id,  $image_file_name);
		
				/* read the source image */
				$this->log("source image path is ${image_file_path}", 'debug');
				if(!file_exists($image_file_path) || !is_file($image_file_path)){
					$this->log('source file does not exists', 'debug');
					$image_data = $image['Image']['img_data'];
					$decoded=base64_decode($image_data);
					file_put_contents($image_file_path, $decoded);
				}
				//Serving thumbnail <${image_file_path}> to client ...
				if(file_exists($image_file_path))
					$this->response->file($image_file_path);
				return $this->response;
			}
		}
		$this->log('</images.show>', 'debug');
	}
	
	/**
	 *This action is triggered when an image resource needs to be uploaded
	 */
	public function uploadImage(){
		$this->log('<images.uploadImage>', 'debug');
		
		if(empty($this->request->params["requested"]) && !$this->request->is('post')){
			$this->log('wrong HTML form method ...', 'debug');
			return;
		}
		//HTML Form method is valid
		$error = '';
		$image_id = '';
		$message = '';
		
		$db = $this->Image->getDataSource();
		$db->begin();
		try{
			//adding any passed param
			$this->request->data  = array_merge($this->request->data, $this->request->params['pass']);
			
			//Checking resource image object ...
			if(!isset($this->request->data['Image']['image'])){
				$error = __('No image resource found in request');
				throw new Exception($error);
			}
			$errorFlag = $this->request->data['Image']['image']['error'];
			if($errorFlag){
				$error = __('Upload failed due to internal Server error');
				throw new Exception($error);
			}
			$name = $this->request->data['Image']['image']['name'];
			$size = $this->request->data['Image']['image']['size'];
			$tmp_name = $this->request->data['Image']['image']['tmp_name'];
			$tmp_data= isset($this->request->data['Image']['image']['tmp_data'])?
							$tmp_name = $this->request->data['Image']['image']['tmp_data']:"";
			$this->loadModel('Image');
		
			$this->Image->create();
			if(!$tmp_data){
				if(!file_exists($tmp_name)){
					$error = __('Invalid tmp file');
					throw new Exception($error);
				}
				if($size > 3145728){//size > 3MB
					$error = __('Image file is too large');
					throw new Exception($error);
				}
				$im = file_get_contents($tmp_name);
				$imdata = base64_encode($im);
				$size = getimagesize($tmp_name);
				$mime = $size["mime"];
   			}else{
   				$imdata = $tmp_data;
   				$mime = "image/png";//TODO
   			}
			if(!$imdata){
				$error = __('Unable to encode the uploaded file ');
				throw new Exception($error);
			}				
			$this->Image->data['Image']['img_name'] = $name;
			
			$this->Image->data['Image']['img_mime_type'] = $mime;
			$this->Image->data['Image']['img_data'] = $imdata;
			$date = new DateTime();
			$now = $date->getTimestamp();
			$this->Image->data['Image']['img_created'] = $now;
			
			if(!$this->Image->save($this->Image->data)){
				$error = __("Unable to save the image data");
				throw new Exception($error);
			}
			$image_id = $this->Image->id;
			$message = __('Image has been successfully uploaded');
			$message = htmlentities($message, ENT_QUOTES);
			$db->commit();
		}catch(Exception $e){
			$db->rollback();
			$this->log($e->getMessage());
			if(!$error)
				$error = __(AppModel::$SYSTEM_ERROR);
		}
		$data = array('error' => $error, 'image_id' => $image_id, 'message' => $message);
		if(!empty($this->request->params["requested"])){
			$this->log("</images.uploadImage>", 'debug');
			return $data;
		}
		$this->set($data);	
		
		$this->layout = 'empty';
		
		
		$this->log('</images.uploadImage>', 'debug');
	}
}