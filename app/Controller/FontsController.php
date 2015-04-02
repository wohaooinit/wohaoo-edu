<?php
App::uses('Controller', 'Controller');
require_once APP . DS. 'Lib' . DS . 'portable_utf8.php';

class FontsController extends Controller {
	public $uses = false; //no model, no table 
	
	/*
	 *
	 *The font files (.ttf) are stored in app/Lib/Fonts
	 */
	public function sprite($fontName = '', $text= '', $size ="15", $color = '000000'){
		set_time_limit(100);
		$this->response->type('image/png');
		$this->autoRender = false;
		if(isset($this->request->query['name']) )
				$fontName = $this->request->query['name'];
		if(isset($this->request->query['text']) )
				$text = $this->request->query['text'];
		if(isset($this->request->query['size']) )
				$size = $this->request->query['size'];
		if(isset($this->request->query['color']) )
				$color = $this->request->query['color'];
		$this->log("<font fontName=$fontName text=$text size=$size color=$color>", 'debug');
		// Gets full path to fonts dir
        	$fontsPath = dirname(dirname(__FILE__)) . DS . 'Lib' . DS . 'Fonts';
		$font = $fontsPath . DS . $fontName . ".ttf";
		$this->log("fontPath=$font", 'debug');
		
		$text_length = strlen($text);
		if(is_utf8($text)){
			$text = utf8_clean( $text );
			$text = $this->__utf8_urldecode($text);
			$text_length  = utf8_strlen($text);
			$this->log("decoded text=$text", 'debug');
		}
		$box = imagettfbbox($size, 0, $font, $text);
		$width = abs($box[0]+$box[2]) + 12; 
    		$height = abs($box[1]-$box[7]) + 12; 
    		
		$this->log("width=$width, height=$height", 'debug');
		
		$rgb = $this->_hex2rgb($color);
		$this->log("rgb=" . var_export($rgb, true), 'debug');
		$img = imagecreatetruecolor($width, $height);
		//set image transparent
		imagesavealpha($img, true);
		imagealphablending($img, false);
		$col=imagecolorallocatealpha($img,255,255,255,127);
		imagefill($img, 0, 0, $col);
		
		//$almostblack = imagecolorallocate($img, 254, 254, 254);
		//imagefill($img,0,0,$almostblack); 
		$gdColor     = imagecolorallocate($img, $rgb[0], $rgb[1], $rgb[2]);
		//set background transparent
		//imagecolortransparent($img, $almostblack);
		
		
		$x = 0;
		$y = ($height - $box[5]) / 2;
		$this->log("x=$x,y=$y", 'debug');
		imagettftext($img, /*font size*/$size,/*angle*/0,/*x*/$x,/*y*/$y,
									/*text color*/$gdColor,/*font path*/$font,/*text to render*/$text); 
        	$this->response->body(imagepng($img));
		imagedestroy($img);
		$this->log("</font>", 'debug');
	}
	
	private function __utf8_urldecode($str) {
		return ereg_replace('%u([[:alnum:]]{4})', '&#x\1;',$str);
	}
	
	private function _imagettftext_spacing($image, $size, $angle, $x, $y, $color, $font, $text, $spacing = 0)
	{        
		$temp_x = $x;
		for ($i = 0; $i < strlen($text); $i++)
		{
			$bbox = imagettftext($image, $size, $angle, $temp_x, $y, $color, $font, $text[$i]);
			$temp_x += $spacing + ($bbox[2] - $bbox[0]);
		}
	}
	
	private function _hex2rgb($hex) {
	   $hex = str_replace("#", "", $hex);

	   if(strlen($hex) == 3) {
		  $r = hexdec(substr($hex,0,1).substr($hex,0,1));
		  $g = hexdec(substr($hex,1,1).substr($hex,1,1));
		  $b = hexdec(substr($hex,2,1).substr($hex,2,1));
	   } else {
		  $r = hexdec(substr($hex,0,2));
		  $g = hexdec(substr($hex,2,2));
		  $b = hexdec(substr($hex,4,2));
	   }
	   $rgb = array($r, $g, $b);
	   //return implode(",", $rgb); // returns the rgb values separated by commas
	   return $rgb; // returns an array with the rgb values
	}
}
?>