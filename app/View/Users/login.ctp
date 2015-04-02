<?php

?>

<div class="users form">
<?php echo $this->Session->flash('auth'); ?>
<?php echo $this->Form->create('User');?>
    <fieldset>
        <legend><?php echo __('Please enter your email and password'); ?></legend>
    <?php
        echo $this->Form->input('usr_email', array('label' => __('Email')));
        echo $this->Form->input('usr_password', array('type' => 'password', 'label' => __('Password')));
        ?>
    </fieldset>
    <div class="user-form-footer">
	<?php echo $this->Form->end(array('label' => __('Login'), 
									'name' => __('Login'), 'div' => array('class' => 'bs-inline submit')));?>&nbsp;
	<?php echo $this->Html->link(__('Register'), array('controller' => 'users', 'action' => 'register'),
										  array('class' => 'bs-button bs-button-blue')); 
	?>
    </div>
</div>