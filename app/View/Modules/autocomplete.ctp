<?php

$this->layout = 'empty';
if(isset($module_list)){
	$result = array();
	$suggestions = array();
	foreach($module_list as $code => $name){
		$suggestion = new stdClass();
		$suggestion->value = $name;
		$suggestion->data = $code;
		$suggestions[] = $suggestion;
	}
	$result['suggestions'] = $suggestions;
	
	echo json_encode($result);
}
?>