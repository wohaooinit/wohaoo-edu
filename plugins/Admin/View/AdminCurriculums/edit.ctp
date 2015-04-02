<div class="curriculums form">
<?php echo $this->ExtendedForm->create('Curriculum');?>
	<fieldset>
		<legend><?php echo __('Edit Curriculum'); ?></legend>
	<?php
		echo $this->ExtendedForm->input('cur_img_id', array('type' => 'image', 
								'default' => '/img/product.gif', 'label' => __('Icon')));
		echo $this->ExtendedForm->input('cur_presentation_file_id', array('type' => 'file', 
								'default' => '/js/file_uploader/uploadthumb.png', 
										'label' => __('Presentation')));
		
		echo $this->ExtendedForm->input('cur_code', array('label' => __('Code')));
		echo $this->ExtendedForm->input('cur_short_name', array('label' => __('Short Name')));
		echo $this->ExtendedForm->input('cur_name', array('label' => __('Title'), 'type' => 'text'));
		echo $this->ExtendedForm->input('cur_description', array('label' => __('Description')));
		echo $this->ExtendedForm->input('cur_copyright', array('label' => __('Copyright')));
		echo $this->ExtendedForm->input('cur_lang_id', array('type' => 'data', 
												             'model' => 'Lang', 
												             'field' => 'lan_display_name', 
												             'label' => __('Language'),
												             'conditions' => 'use_codes=0'));
	?>
	</fieldset>
<?php echo $this->ExtendedForm->end(__('Save'));?>
</div>