<?php
?>
<div class="attributes form">
<?php echo $this->ExtendedForm->create('Attribute');?>
	<fieldset>
		<legend><?php echo __('Edit Attribute'); ?></legend>
	<?php
		echo $this->ExtendedForm->input('att_property_name', array('type' => 'info',  'value' => $attribute['Definition']['atd_display_name'], 'label' => __('Attribute Name')));
		$is_image = $attribute['Definition']['atd_is_image'];
		$is_password = $attribute['Definition']['atd_is_password'];
		$is_data = $attribute['Definition']['atd_is_data'];
		$is_date = $attribute['Definition']['atd_is_date'];
		$is_number = $attribute['Definition']['atd_is_number'];
		if($is_image){
			echo $this->ExtendedForm->input('att_content', array('type' => 'image', 'label' => __('Attribute Value')));
		}else
		if($is_password){
			echo $this->ExtendedForm->input('att_content', array('type' => 'password', 'label' => __('Attribute Value')));
		}else
		if($is_data){
			$model = $attribute['Definition']['atd_data_model'];
			$data_key = $attribute['Definition']['atd_data_key'];
			$use_codes = ($data_key === "id")? 0 : 1;
			
			echo $this->ExtendedForm->input('att_content', array('type' => 'data', 
												             'model' => $model, 
												             'field' => $data_key, 
												             'label' => __('Content'),
												             'conditions' => "use_codes=${use_codes}"));
		}else
		if($is_date){
			//debug("date");
			echo $this->ExtendedForm->input('att_content', array('type' => 'date', 'label' => __('Attribute Value')));
		}else
			echo $this->ExtendedForm->input('att_content', array('type' => 'text', 'label' => __('Attribute Value')));
	?>
	</fieldset>
<?php echo $this->ExtendedForm->end(__('Save'));?>
</div>