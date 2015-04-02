<div class="translations form">
<?php echo $this->ExtendedForm->create('Translation');?>
	<fieldset>
		<legend><?php echo __('Edit Translation'); ?></legend>
	<?php
		echo $this->ExtendedForm->input('t9n_guid', array('label' => __('GUID'),  'readonly' => true));
		echo $this->ExtendedForm->input('t9n_orig_lang', array('label' => __('Source Lang'),  'readonly' => true));
		echo $this->ExtendedForm->info('t9n_orig_text', array('label' => __('Original Text'), 'readonly' => true, 
					'style'=>'height:500px;'));
		echo $this->ExtendedForm->input('t9n_trans_lang', array('label' => __('Dest Lang'),  'readonly' => true));
		echo $this->ExtendedForm->text('t9n_trans_text', array('label' => __('Translated Text')));
	?>
	</fieldset>
<?php echo $this->ExtendedForm->end(__('Save'));?>
</div>