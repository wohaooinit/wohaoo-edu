<?php

App::uses('AppModel', 'Model');

/**
 *Images
 *
 * @package       app.Model
 */
class Image extends AppModel {
	public $columnPrefix ="img_";
	/*www.ashishrevar.com*/
	/*Function to create image thumbnails*/
	public function createImageThumbnail($width = 0, $height = 0) {
		$this->log('image.createImageThumb =>', 'debug');
		if(!$width || !$height)
			return "";
		$this->log("width=$width", 'debug');
		$this->log("height=$height", 'debug');
		
		$orig_width = $width;
		$orig_height = $height;
		
		$dest = "";
		if(!$this->data)
			$this->read();
		$image_file_name = $this->data['Image']['img_name'];
		$image_file_name = pathinfo($image_file_name, PATHINFO_FILENAME);
		$source_file_path = sprintf("%s/%d_%s", TMP, $this->id,  $image_file_name);
		
		/* read the source image */
		$this->log("source image path is ${source_file_path}", 'debug');
		if(!file_exists($source_file_path) || !is_file($source_file_path)){
			$this->log('source file does not exists', 'debug');
			$image_data = $this->data['Image']['img_data'];
			$decoded=base64_decode($image_data);
			file_put_contents($source_file_path, $decoded);
		}
			
		$source_image = $this->open_image($source_file_path);
		if(!$source_image){
			$this->log('Illegal, unable to open source image', 'debug');
			return $dest;
		}
		$w = imagesx($source_image);
		$h = imagesy($source_image);

		$this->log('find the desired height and width', 'debug');
		if($width){
			/* find the “desired height” of this thumbnail, relative to the desired width  */
			$height = floor($h * ($width / $w));
		}
		else if($height){
			/* find the “desired width” of this thumbnail, relative to the desired height  */
			$width = floor($w * ($height / $h));
		}else{
			$this->log('illegal, no dimensions speficified.', 'debug');
			return false; //illegal, no dimensions speficified.
		}
		
		$this->log("width=$width/height=$height", 'debug');
		
		$dest = sprintf("%s/%d_w_%d_h_%d.png",
								TMP, $this->id, $orig_width, $orig_height);
								
		$this->log("dest=$dest", 'debug');

		/* create a new, “virtual” image */
		$this->log('create a new, virtual image', 'debug');
		$virtual_image = imagecreatetruecolor($width, $height);
		
		//set image transparent
		imagesavealpha($virtual_image, true);
		imagealphablending($virtual_image, false);
		$col=imagecolorallocatealpha($virtual_image,0,0,0,127);
		imagefill($virtual_image, 0, 0, $col);
		
		/* copy source image at a resized size */
		$this->log('copy source image at a resized size', 'debug');
		imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, 
			$width, $height, $w, $h);

		/* create the physical thumbnail image to its destination */
		$this->log('create the physical thumbnail image to its destination', 'debug');
		imagepng($virtual_image, $dest);
		
		imagedestroy($source_image);
		imagedestroy($virtual_image);
		
		$this->log('image.createImageThumb =>', 'debug');
		return $dest;
	}
	
	/**juozaspo@gmail.com**/
	function open_image ($file) {
		$size=getimagesize($file);
		switch($size["mime"]){
			case "image/jpeg":
				$im = imagecreatefromjpeg($file); //jpeg file
				break;
			case "image/gif":
				$im = imagecreatefromgif($file); //gif file
				break;
			case "image/png":
			    $im = imagecreatefrompng($file); //png file
				break;
			default: 
				$im=false;
				break;
		}
		return $im;
	}
}
?>