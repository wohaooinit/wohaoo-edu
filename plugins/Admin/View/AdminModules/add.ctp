<div class="modules form">
<?php echo $this->ExtendedForm->create('Module');?>
	<fieldset>
		<legend><?php echo __('Add Module'); ?></legend>
	<?php
		echo $this->ExtendedForm->input('mod_presentation_file_id', array('type' => 'file', 
								'default' => '/js/file_uploader/uploadthumb.png', 'label' => __('Presentation')));
		echo $this->ExtendedForm->input('mod_code', array('label' => __('Code')));
		echo $this->ExtendedForm->input('mod_name', array('label' => __('Title')));
		echo $this->ExtendedForm->input('mod_description', array('label' => __('Description')));
		if(!isset($curriculum)){
			echo $this->ExtendedForm->input('mod_curriculum_id', array('type' => 'data', 
												             'model' => 'Curriculum', 
												             'field' => 'cur_short_name', 
												             'label' => __('Curriculum'),
												             'conditions' => 'use_codes=0'));
		}else{
			echo $this->ExtendedForm->input('mod_curriculum_id', 
							array('type' => 'hidden', 'value' => $curriculum['Curriculum']['id']));
		}
		echo $this->ExtendedForm->input('mod_next_module_id', array('type' => 'data', 
												             'model' => 'Module', 
												             'field' => 'mod_name', 
												             'label' => __('Next Module'),
												             'conditions' => 'use_codes=0'));
												        
		echo $this->ExtendedForm->input('mod_prev_module_id', array('type' => 'data', 
												             'model' => 'Module', 
												             'field' => 'mod_name', 
												             'label' => __('Previous Module'),
												             'conditions' => 'use_codes=0'));
	?>
	</fieldset>
<?php echo $this->ExtendedForm->end(__('Save'));?>
</div>