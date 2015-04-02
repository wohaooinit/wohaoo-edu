<?php
	/*$pageOptions = array(
		"part" => {PART},
		"title" => {TITLE},
		"desc" => {DESC},
		"actions" => array(
			"ACTION1" => array(
				"caption" => {CAPTION},
				"target" => {TARGET}
				'disabled' => TRUE|FALSE
			),
			"ACTION2" => array(
				""caption" => {CAPTION},
				"target" => {TARGET}
				'disabled' => TRUE|FALSE
			)
		),
		'data' => [DATA]
	);*/

?>

<div class="pageContainer">
	<div class="pageHeader">
		<h2 class="title"><?php echo $title;?></h2>
		<h4 class="desc"><?php echo $desc;?></h4>
	</div>
	<div class="pageActionsBar">
		<?php
			foreach($actions as $key => $action){
				$action_caption = $action['caption'];
				$action_target = $action['target'];
				$action_disabled = $action['disabled'];
		?>
			<a class="pageActionLink" href="<?php echo $action_target;?>"><?php echo $action_caption;?></a>
		<?php
			}
		?>
	</div>
	<div class="pageContent">
	<?php
		echo  $this->element($part, $data);
	?>
	</div>
</div>