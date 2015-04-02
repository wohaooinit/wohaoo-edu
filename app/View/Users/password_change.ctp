<?php
?>
<div class="users form">
<?php echo $this->ExtendedForm->create('User');?>
	<fieldset>
		<legend><?php echo __('Change Password'); ?></legend>
	<?php
		echo $this->ExtendedForm->input('usr_password', array('type' => 'password', 'label' => __('Password')));
		echo $this->ExtendedForm->input('usr_password_confirm', 
								array('type' => 'password', 'label' => __('Confirm Password')));
	?>
	</fieldset>
<?php echo $this->ExtendedForm->end(__('Save'));?>
</div>