<?php
	$this->Paginator->options(array(
		'url' => $peopleTableURL,
		'update' => '.people.table',
		'evalScripts' => true
	));

	if(!isset($show_headers))
		$show_headers = false;


	function build_items($self = null, $people = array()){
		$items = array();
		
	
		foreach($people as $person){
			$status = array('class' => "", "content" => $self->Format->image($person['Person']['per_img_id'], 84, 76));
			$caption = $person['Person']['per_first_name'] . " " . $person['Person']['per_last_name'];
			$description = "<p><strong>". __("Birth Town") . ":</strong>" . h($person['Person']['per_birth_town']) . 
						"</p><p><strong>". __("Country") . ":</strong>" 
							. h($person['PerCountry']['con_display_name']) . ")</p><p><strong>" . __("Birth Date")  . ":</strong>" .
				$self->Format->date($person['Person']['per_birth_date']) . "</p>";

			$stats = array(array("caption" => __("Sex") , "value" => ($person['Person']['per_sex']?__("M"):__("F")) ));
			$items[] = array(
				"id" => $person['Person']['id'],
				"status" => $status,
				"caption" => $caption,
				"description" => $description,
				"stats" => $stats
			);
		}
		return $items;
	}

	$filters = array(
		array(
			"caption" => __("Country"),
			"name" => "sta_country_id",
			//"options" => $this->requestAction("/countries/autocomplete?use_codes=0")
			"options" => array("auto_complete_url" => "/countries/autocomplete?use_codes=0")
		)
	);

	$model_folders = $this->requestAction(array("controller" => "model_folders", "action" => "autocomplete",
											 "fol_model" => "Person")); 
	$folders = array(
		array(
			"caption" => __("All"),
			"size" => intval($this->Paginator->counter(array(
					'format' => '{:count}',
					'model' => 'Person'
					))),
			"id" => 0
		)
	);
	$this->log("ModelFolders=" . var_export($model_folders, true), 'debug');
	foreach($model_folders as $folder){
		$folders[] = array('caption' => $folder['ModelFolder']['fol_display_name'],
							'size' => $folder['ModelFolder']['fol_object_count'],
							'id' => $folder['ModelFolder']['id']
					);
	}

	$this->log("folders=" . var_export($folders, true), 'debug');
	$listViewOptions  = array( "url" => $this->Html->url($peopleTableURL),
					 "base_url" => "/people",
					 "title" => __("People"),
					 "add_url" => "/people/add",
					 "folder_service_url" => "/model_folders",
					 "move_to_folder_url" => "/object_folders/add",
					 "delete_url" => "/people/delete",
					 "name" => __("Person"),
					 "model" => "Person",
					 "show_headers" => $show_headers,
					 "actions" =>  array(),
					 "item_actions" => array( 
										array("caption" => "Edit", "target" => "/people/edit/%d"),
										array("caption" => "View", "target" => "/people/view/%d")
									 ),
					 "default_item_action" => array("caption" => "View" , "target" => "/people/view/%d"),
					 "items" => build_items($this, $persons),
					 "filters" => $filters,
					  "folders" => $folders,
					  'paging' => array(	
									'prev' =>  $this->Paginator->prev('<', 
											array('model' => 'Person'), null, array('class' => 'page')),
									'numbers' => $this->Paginator->numbers(array('first' => 'first', 'last' => 'last', 
											'class' => 'page', 'currentClass' => 'selected', 
											'separator' => '', 'model' => 'Person')),
									'next' =>  $this->Paginator->next('>', 
											array('model' => 'Person'), null, array('class' => 'page')),
								)
					);
	echo $this->element("../Elements/listview", $listViewOptions);
?>