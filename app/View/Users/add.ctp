<?php
?>
<div class="users form">
<?php echo $this->ExtendedForm->create('User');?>
	<fieldset>
		<legend><?php echo __('Add User'); ?></legend>
	<?php
		echo $this->ExtendedForm->input('usr_pict_id', array('type' => 'image', 'label' => __('Picture')));
		echo $this->ExtendedForm->input('usr_first_name', array('label' => __('First Name')));
		echo $this->ExtendedForm->input('usr_last_name', array('label' => __('Last Name')));
		echo $this->ExtendedForm->input('usr_email', array('type' => 'email', 'label' => __('Email Address')));
		echo $this->ExtendedForm->input('usr_birth_date', array('type' => 'date', 'minYear' => '1900', 
												'label' => __('Birth Date')));
		echo $this->ExtendedForm->input('usr_password', array('type' => 'password', 'label' => __('Password')));
		echo $this->ExtendedForm->input('usr_password_confirm', array('type' => 'password', 
												'label' => __('Confirm Password')));
		echo $this->ExtendedForm->input('usr_country', array('type' => 'data', 
												             'model' => 'Country', 
												             'field' => 'con_code', 
												             'dependents' => 'usr_region',
												             'label' => __('Country')));
		echo $this->ExtendedForm->input('usr_region', 
						array('type' => 'data', 
						 'model' => 'Region', 
						 'field' => 'reg_code',
						 'conditions' => 'reg_country={$("#usr_country").val()}', 
						 'label' => __('Region')));
	?>
	</fieldset>
<?php echo $this->ExtendedForm->end(__('Save'));?>
</div>