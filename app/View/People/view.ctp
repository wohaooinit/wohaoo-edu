<?php
if(!isset($id))
	$id = $person['Person']['id'];
if(!isset($part) || !$part)
	$part = 'details';
$part = "../People/Parts/$part";

$pageOptions = array(
	"part" => $part,
	"title" => $person['Person']['per_first_name'] . " " . $person['Person']['per_last_name'],
	"desc" => $person['Person']['per_description_short'],
	"actions" => array(
		"edit" => array(
			"caption" => __("Edit Person"),
			"target" => "/people/edit/$id",
			'disabled' => !$this->Security->has('admin') 
		),
		"new" => array(
			"caption" => __("New Person"),
			"target" => "/people/add",
			'disabled' => !$this->Security->has('admin') 
		)
	),
	'data' => array(
		"person" => $person
	)
);
 echo $this->element("../Elements/page", $pageOptions);
?>