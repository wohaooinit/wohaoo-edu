<div class="options form">
<?php echo $this->ExtendedForm->create('Option');?>
	<fieldset>
		<legend><?php echo __('Edit Option'); ?></legend>
	<?php
		if(!isset($assessment)){
			echo $this->ExtendedForm->input('opt_assessment_id', array('type' => 'data', 
												             'model' => 'Assessment', 
												             'field' => 'ass_code', 
												             'label' => __('Assessment'),
												             'conditions' => 'use_codes=0'));
		}else{
			echo "<label><strong>". $assessment['Assessment']['ass_code'] . "</strong></label>";
			echo "<label>". $assessment['Assessment']['ass_description'] . "</label>";
			echo $this->ExtendedForm->input('opt_assessment_id', 
				array('type' => 'hidden',  'value' => $assessment['Assessment']['id']));
		}
		echo $this->ExtendedForm->input('opt_code', array('label' => __('Code')));
		echo $this->ExtendedForm->input('opt_display_text', array('label' => __('Text')));
		echo $this->ExtendedForm->input('opt_is_ok', array('label' => __('Right Answer')));
	?>
	</fieldset>
<?php echo $this->ExtendedForm->end(__('Save'));?>
</div>