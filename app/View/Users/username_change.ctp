<div class="users form">
<?php echo $this->ExtendedForm->create('User');?>
	<fieldset>
		<legend><?php echo __('Change Password'); ?></legend>
	<?php
		$user_id = $this->ExtendedForm->value('id');
		echo $this->ExtendedForm->input('usr_username', array('type' => 'text', 'value' => $this->Format->username($user_id),
										  'label' => __('Username')));
	?>
	</fieldset>
<?php echo $this->ExtendedForm->end(__('Save'));?>
</div>