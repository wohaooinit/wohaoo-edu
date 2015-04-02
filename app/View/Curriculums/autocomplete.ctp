<?php

$this->layout = 'empty';
if(isset($curriculum_list)){
	$result = array();
	$suggestions = array();
	foreach($curriculum_list as $code => $name){
		$suggestion = new stdClass();
		$suggestion->value = $name;
		$suggestion->data = $code;
		$suggestions[] = $suggestion;
	}
	$result['suggestions'] = $suggestions;
	
	echo json_encode($result);
}
?>