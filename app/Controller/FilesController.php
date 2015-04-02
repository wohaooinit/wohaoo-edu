<?php
App::uses('EduController', 'Controller');
/**
 * FilesController
 *
 * @property File $File
 */
class VideosController extends EduController {

	public $uses = array('File');
	
	public function beforeFilter(){
		parent::beforeFilter();
		$this->Auth->allow('resources', 'video', 'audio');
	}
	
	public function resources($type = "", $model = "", $object_id = 0){
	
	}
	
	public function video($file_id = 0, $format = "mp4"){
	
	}
	
	public function audio($file_id = 0, $format = "mp3"){
	
	}
	

	/**
	 *This action is triggered when the thumbnail of a given Video object
	 *with specified width and height needs to be displayed
	 *@param $width, the requird width
	 *@param $height, the required height of the thumbnail
	 *@param $id, the id of the Resource object
	 */
	public function thumbnail($width = 0, $height = 0, $id = 0){
		$this->log('<videos.thumbnail>', 'debug');
		$width = '';
		$height = '';
		if(isset($this->request->query['width']))
			$width = $this->request->query['width'];
		if(isset($this->request->query['height']))
			$height = $this->request->query['height'];
		$id = $this->request->query['id'];
		
		$find_options = array(
			'conditions' => array('Video.id' => $id)
		);
		$video = $this->Video->find('first', $find_options);
		if($video){
			// send the right headers
			$video_file_name = $video['Video']['vid_name'];
			$video_file_path = sprintf("%s/%d_%s", TMP, $id,  $video_file_name);
	
			/* read the source video */
			$this->log("source video path is ${video_file_path}", 'debug');
			if(!file_exists($video_file_path) || !is_file($video_file_path)){
				$this->log('source file does not exists', 'debug');
				$video_data = $video['Video']['vid_data'];
				$decoded=base64_decode($video_data);
				file_put_contents($video_file_path, $decoded);
			}
			$video_file_dir = pathinfo($video_file_path, PATHINFO_DIRNAME);
			if($width || $height){
				$thumbnail_file = "${video_file_dir}/${id}_w_${width}_h_${height}.jpg";
				if(!file_exists($thumbnail_file)){
					$this->log("${thumbnail_file} does not exists. Creating new thumbnail ..", 'debug');
					$this->Video->id = $id;
					$this->Video->read();
					$thumbnail_file = $this->Video->captureFrame($video_file_path, $width, $height);
				}
				if($thumbnail_file && is_file($thumbnail_file)){
					//Serving thumbnail <$thumbnail_file> to client ...
					$this->response->file($thumbnail_file);
					return $this->response;
				}else
					$this->response->statusCode(404); //return a file not found to client
			}
		}
		$this->log('</videos.thumbnail>', 'debug');
	}
	
	/**
	 *This action is triggered when the resource object need s to be played (video)
	 *or simply displayed (image).
	 *@pram $id, the id of the Resource object
	 */
	public function video($id = ''){
		$this->log('<videos.view>', 'debug');
		$id = $this->request->query['id'];
		
		$find_options = array(
			'conditions' => array('Video.id' => $id)
		);
		$video = $this->Video->find('first', $find_options);
		if($video){
			// send the right headers
			$video_file_name = $video['Video']["vid_name"];
			$video_file_path = sprintf("%s/%d_%s", TMP, $id,  $video_file_name);
	
			/* read the source video */
			$this->log("source video path is ${video_file_path}", 'debug');
			if(!file_exists($video_file_path) || !is_file($video_file_path)){
				$this->log('source file does not exists', 'debug');
				$video_data = $video['Video']['vid_data'];
				$decoded=base64_decode($video_data);
				file_put_contents($video_file_path, $decoded);
			}
			$this->log("Serving <$file_name> to client ...", 'debug');
			$extension = pathinfo($video_file_path, PATHINFO_EXTENSION);
			$this->response->type("video/$extension");
			$this->response->file($video_file_path);
			return $this->response;
		}
		$this->log('</videos.view>', 'debug');
	}
	
	/**
	 *This action is triggered when an video resource needs to be uploaded
	 */
	public function uploadVideo(){
		$this->log('<videos.uploadVideo>', 'debug');
		
		if(!$this->request->is('post')){
			$this->log('wrong HTML form method ...', 'debug');
			return;
		}
		//HTML Form method is valid
		$error = '';
		$video_id = '';
		$message = '';
		
		$db = $this->Video->getDataSource();
		$db->begin();
		try{
			//Checking resource video object ...
		
			if(!isset($this->request->data['Video']['video'])){
				$error = __('No video resource found in request');
				throw new Exception($error);
			}
			$errorFlag = $this->request->data['Video']['video']['error'];
			if($errorFlag){
				$error = __('Upload failed due to internal Server error');
				throw new Exception($error);
			}
			$name = $this->request->data['Video']['video']['name'];
			$size = $this->request->data['Video']['video']['size'];
			$tmp_name = $this->request->data['Video']['video']['tmp_name'];
		
			$this->loadModel('Video');
		
			$this->Video->create();
		
			if(!file_exists($tmp_name)){
				$error = __('Invalid tmp file');
				throw new Exception($error);
			}
			if($size > 3145728){//size > 3MB
				$error = __('Video file is too large');
				throw new Exception($error);
			}
			$im = file_get_contents($tmp_name);
   			$imdata = base64_encode($im);
			if(!$imdata){
				$error = __('Unable to encode the uploaded file ');
				throw new Exception($error);
			}				
			$this->Video->data['Video']['vid_name'] = $name;
			$codec = $this->Video->getVideoCodec($tmp_name);
			$this->Video->data['Video']['vid_mime_type'] = $codec;
			$this->Video->data['Video']['vid_data'] = $imdata;
			$date = new DateTime();
			$now = $date->getTimestamp();
			$this->Video->data['Video']['vid_created'] = $now;
			
			if(!$this->Video->save($this->Video->data)){
				$error = __("Unable to save the video data");
				throw new Exception($error);
			}
			$video_id = $this->Video->id;
			$message = __('Video has been successfully uploaded');
			$message = htmlentities($message, ENT_QUOTES);
			$db->commit();
		}catch(Exception $e){
			$db->rollback();
			$this->log($e->getMessage());
			if(!$error)
				$error = __(AppModel::$DEFAULT_ERROR_MESSAGE);
		}
			
		$this->set('error', $error);
		$this->set('video_id', $video_id);
		$this->set('message', $message);
		$this->layout = 'empty';
		
		
		$this->log('</videos.uploadVideo>', 'debug');
	}
}