<div class="resources form">
<?php echo $this->ExtendedForm->create('Resource');?>
	<fieldset>
		<legend><?php echo __('Add Resource'); ?></legend>
	<?php
		echo $this->ExtendedForm->input('res_model', array('label' => __('Model'), 'readonly' => true));
		echo $this->ExtendedForm->text('res_model_id', array('label' => __('Model ID'), 'readonly' => true));
		
		echo $this->ExtendedForm->input('res_type', array('label' => __('Type'), 
							'options' => array('audio' => __('Audio'), 'video' => __('Video'),
							'document' => __('Document'))));
		
		echo $this->ExtendedForm->input('res_mp4', array('label' => __('MP4'), 'type' => 'text'));
		echo $this->ExtendedForm->input('res_ogv', array('label' => __('OGV'), 'type' => 'text'));
		echo $this->ExtendedForm->input('res_webm', array('label' => __('WEBM'), 'type' => 'text'));
		
		echo $this->ExtendedForm->input('res_mp3', array('label' => __('MP3'), 'type' => 'text'));
		echo $this->ExtendedForm->input('res_ogg', array('label' => __('OGG'), 'type' => 'text'));
		echo $this->ExtendedForm->input('res_wav', array('label' => __('WAV'), 'type' => 'text'));
		
		echo $this->ExtendedForm->input('res_pdf', array('label' => __('PDF Url'), 'type' => 'text'));
		
		echo $this->ExtendedForm->input('res_embed', array('label' => __('Embed'), 'type' => 'text'));
	?>
	</fieldset>
<?php echo $this->ExtendedForm->end(__('Save'));?>
</div>