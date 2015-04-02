<?php
App::uses('AppModel', 'Model');

/**
 *Video
 *
 * @package       app.Model
 */
class Video extends AppModel {
	public function getFileName($tmp_name){
		if($tmp_name && $this->id){
			$ext = pathinfo($tmp_name, PATHINFO_EXTENSION);
			$id = $this->id;
			return "res_$id.$ext";
		}
		return "";
	}
	
	public function getDuration($video_file_path = null){
		ob_start();
		passthru("ffmpeg -i ${video_file_path} 2>&1");
		$duration = ob_get_contents();
		ob_end_clean();

		$search='/Duration: (.*?),/';
		$duration=preg_match($search, $duration, $matches, PREG_OFFSET_CAPTURE);
		
		$this->log("matches=" . var_export($matches, true), 'debug');
		
		$duration = $matches[1][0];
		
		$this->log("ffmpeg.duration=$duration", 'debug');
		
		$duration_array = split(':', $duration);
		$duration = $duration_array[0] * 3600 + $duration_array[1] * 60 + intval($duration_array[2]);
		
		$this->log("Video.duration=$duration", 'debug');
		return $duration;
	}
	
	public function captureFrame($video_file_path = null, $width, $height, $frame_position = null){
		$this->log('Video.php.captureFrame =>', 'debug');
		if(!$width || !$height)
			return "";
		$id = $this->id;
		$this->log("width=$width", 'debug');
		$this->log("height=$height", 'debug');
		$source_file_path = pathinfo($video_file_path, PATHINFO_DIRNAME);
		$source_file_name = pathinfo($video_file_path, PATHINFO_FILENAME);
		
		$cmd = "";
		if(!$frame_position){
			//first compute the video duration
			$duration = $this->getDuration($video_file_path);
			
			//get a random frame position
			$frame_position = rand(1, $duration);
		}
		$capture_file_full_name = "${source_file_path}/${id}_w_${width}_h_${height}.jpg";
								
		$ffmpeg = 'ffmpeg';
	
		//screenshot size
		$width = intval($width);
		$height = intval($height);
		$size = $width. "x" . $height;

		//ffmpeg command

		$cmd = "$ffmpeg -i ${video_file_path} -deinterlace -an -ss ${frame_position} -f mjpeg -t 1 -r 1 -y -s $size ${capture_file_full_name} 2>&1";       
		$this->log("System.cmd=$cmd", 'debug');
		$output = `$cmd`;
		$this->log("System.output=$output", 'debug');
		$this->log("Video.capture_file_full_name=${thumb_file_full_name}", 'debug');
		$this->log('Video.php.captureFrame <=', 'debug');
		return $capture_file_full_name;
	}
	
	public function getVideoCodec($video_file){
		$output = "";
		ob_start();
		$cmd = "ffmpeg -i $video_file 2>&1";
		$this->log("executing shell ... $cmd", 'debug');
		passthru($cmd);
		$output = ob_get_contents();
		ob_end_clean();
		
		$this->log("ffmpeg.output=$output", 'debug');
		
		$search='/Video: ([^,(]*)[,(]/';
		preg_match($search, $output, $matches, PREG_OFFSET_CAPTURE);
		
		$this->log("matches=" . var_export($matches, true), 'debug');
		
		if(!$matches || !isset($matches))
			return 'System Error. Possible cause: Invalid video file.';
		
		$video_codec = strtolower($matches[1][0]);
		
		return trim($video_codec);
	}
}
?>