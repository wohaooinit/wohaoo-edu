<?php

$this->layout = 'empty';
if(isset($lang_list)){
	$result = array();
	$suggestions = array();
	foreach($lang_list as $code => $name){
		$suggestion = new stdClass();
		$suggestion->value = $name;
		$suggestion->data = $code;
		$suggestions[] = $suggestion;
	}
	$result['suggestions'] = $suggestions;
	
	echo json_encode($result);
}
?>