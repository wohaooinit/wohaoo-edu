<div class="users form">
<?php echo $this->ExtendedForm->create('User');?>
    <fieldset>
        <legend><?php echo __('New Registration'); ?></legend>
    <?php
    		//$types = array('user' => __('User'), 'group' => __('Group'));
    		//echo $this->ExtendedForm->input('usr_type', array('options' => $types, 'default' => 'user', 'label' => __('Register As')));
    		echo $this->ExtendedForm->input('usr_pict_id', array('type' => 'image', 'label' => __('Picture/Logo')));
    		echo $this->ExtendedForm->input('Person.per_first_name', 
								array('label' => __('First Name')));
		echo $this->ExtendedForm->input('Person.per_last_name', array('label' => __('Last Name')));
		echo $this->ExtendedForm->input('Person.per_birth_date', array(
															'type' => 'date', 'label' => __('Birth Date/Creation Date')));
		echo $this->ExtendedForm->input('usr_password', array('type' => 'password', 'label' => __('Password')));
		echo $this->ExtendedForm->input('usr_password_confirm', 
								array('type' => 'password', 'label' => __('Confirm Password')));
		echo $this->ExtendedForm->input('usr_email', array('type' => 'email', 'label' => __('Email Address')));
		echo $this->ExtendedForm->input('Person.per_mobile_tel', array('label' => __('Mobile (GSM)')));
		echo $this->ExtendedForm->input('Person.per_country_id', array('type' => 'data', 
												             'model' => 'Country', 
												             'field' => 'id', 
												             'dependents' => array('per_region_id'),
												             'label' => __('Country')));
		echo $this->ExtendedForm->input('Person.per_region_id', 
								array('type' => 'data', 
									 'model' => 'Region', 
									 'field' => 'id',
									 'conditions' => 'reg_country_id={$("input[id$=per_country_id]").val()}', 
									 'label' => __('Region')));
		echo $this->ExtendedForm->input('Person.per_town', array('label' => __('Town')));
		echo $this->ExtendedForm->input('Person.per_street1', array('label' => __('Street Address 1')));
		echo $this->ExtendedForm->input('Person.per_street2', array('label' => __('Street Address 2')));
        ?>
    </fieldset>
    <div class="user-form-footer">
	<?php echo $this->ExtendedForm->end(__('Register'));?>
    </div>
</div>