<?php

	/**
	 * /files/uploadFile always return a JSON object
	 * Because of SWFUpload component output must be done in plaintext.
	 */
	 $this->layout = 'empty';
	 echo "{ 'error': '$error'";
	 
	 if(isset($file_id) && $file_id)
	 	 echo ", 'file_id': ${file_id}";
	  if(isset($message) && $message)
	 	 echo ", 'message': '${message}'";
	 echo "}";
?>