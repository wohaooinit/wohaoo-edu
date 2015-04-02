<?php
?>
<div class="users form">
<?php echo $this->ExtendedForm->create('Championship');?>
	<fieldset>
		<legend><?php echo __('Edit Championship'); ?></legend>
	<?php
		echo $this->ExtendedForm->input('cha_short_name', array('label' => __('Short Name')));
		echo $this->ExtendedForm->input('cha_name', array('type' => 'text', 'label' => __('Name')));
		echo $this->ExtendedForm->input('cha_website', array('type' => 'text', 'label' => __('Website')));
		echo $this->ExtendedForm->input('cha_founded', array('type' => 'date', 'label' => __('Founded')));
		echo $this->ExtendedForm->input('cha_country_id', array('type' => 'data', 
												             'model' => 'Country', 
												             'field' => 'id', 
												             'label' => __('Country')));
	?>
	</fieldset>
<?php echo $this->ExtendedForm->end(__('Save'));?>
</div>