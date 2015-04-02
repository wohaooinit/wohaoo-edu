<div class="assessments form">
<?php echo $this->ExtendedForm->create('Assessment');?>
	<fieldset>
		<legend><?php echo __('Add Assessment'); ?></legend>
	<?php
		if(!isset($module)){
			echo $this->ExtendedForm->input('ass_module_id', array('type' => 'data', 
												             'model' => 'Module', 
												             'field' => 'mod_name', 
												             'label' => __('Module'),
												             'conditions' => 'use_codes=0'));
		}else{
			echo "<label>" .  $module['Module']['mod_code'].  "</label>";
			echo $this->ExtendedForm->input('ass_module_id', 
						array('type' => 'hidden', 'value' => $module['Module']['id']));
		}
		echo $this->ExtendedForm->input('ass_code', array('label' => __('Code')));
		echo $this->ExtendedForm->input('ass_description', array('label' => __('Description')));
		echo $this->ExtendedForm->input('ass_type', array('label' => __('Type')));
		echo $this->ExtendedForm->input('ass_serial', array('label' => __('Position')));
	?>
	</fieldset>
<?php echo $this->ExtendedForm->end(__('Save'));?>
</div>