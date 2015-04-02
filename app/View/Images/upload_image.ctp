<?php
	/**
	 * /images/uploadImage always return a JSON object
	 * Because of SWFUpload component output must be done in plaintext.
	 */
	 $this->layout = 'empty';
	 echo "{ 'error': '$error'";
	 
	 if(isset($image_id) && $image_id)
	 	 echo ", 'image_id': ${image_id}";
	  if(isset($message) && $message)
	 	 echo ", 'message': '${message}'";
	 echo "}";
?>