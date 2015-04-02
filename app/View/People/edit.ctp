<?php
if($this->Security->has('admin')){
?>
<div class="people form">
<?php echo $this->ExtendedForm->create('Person');?>
	<fieldset>
		<legend><?php echo __('Edit Person'); ?></legend>
	<?php
		echo $this->ExtendedForm->input('per_img_id', 
					array('type' => 'image', 'label' => __('Picture')));
		echo $this->ExtendedForm->input('per_prefix', array('type' => 'text', 'label' => __('Name Prefix')));
		echo $this->ExtendedForm->input('per_first_name', array('label' => __('First Name')));
		echo $this->ExtendedForm->input('per_last_name', array('type' => 'text', 'label' => __('Last Name')));
		echo $this->ExtendedForm->input('per_suffix', array('type' => 'text', 'label' => __('Name Suffix')));
		echo $this->ExtendedForm->input('per_nick_name', array('type' => 'text', 'label' => __('Nick Name')));
		echo $this->ExtendedForm->input('per_description_short', array('label' => __('Description Short')));
		echo $this->ExtendedForm->textarea('per_description', array('label' => __('Description')));
		echo $this->ExtendedForm->input('per_birth_date', array('type' => 'date', 'minYear' => '1800',
			 'label' => __('Birth Date')));
		$sex = array('1' => __('M'), '0' => __('F'));
    		echo $this->ExtendedForm->input('per_sex', array('options' => $sex, 'default' => '1', 'label' => __('Sex')));
		echo $this->ExtendedForm->input('per_country_id', array('type' => 'data', 
												             'model' => 'Country', 
												             'field' => 'id', 
												             'label' => __('Country')));
		echo $this->ExtendedForm->input('per_region_id', array('type' => 'data', 
												             'model' => 'Region', 
												             'field' => 'id', 
												             'label' => __('Region')));
		echo $this->ExtendedForm->input('per_town', array('type' => 'text', 'label' => __('Town')));
		echo $this->ExtendedForm->input('per_post_code', array('type' => 'text', 'label' => __('Post code')));
		echo $this->ExtendedForm->input('per_street1', array('type' => 'text', 'label' => __('Address 1')));
		echo $this->ExtendedForm->input('per_street2', array('type' => 'text', 'label' => __('Address 2')));
		echo $this->ExtendedForm->input('per_email', array('type' => 'text', 'label' => __('Email')));
		echo $this->ExtendedForm->input('per_office_tel', array('type' => 'text', 'label' => __('Office Tel')));
		echo $this->ExtendedForm->input('per_mobile_tel', array('type' => 'text', 'label' => __('Mobile Tel')));
		echo $this->ExtendedForm->input('per_twitter_account', array('type' => 'text', 'label' => __('Twiiter Account')));
	?>
	</fieldset>
<?php echo $this->ExtendedForm->end(__('Save'));?>
</div>
<?php
}
?>