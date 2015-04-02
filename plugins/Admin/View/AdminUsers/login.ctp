<div class="users form">
<?php echo $this->Session->flash('auth'); ?>
<?php echo $this->Form->create('User');?>
    <fieldset>
        <legend><?php echo __('Please enter your email and password'); ?></legend>
    <?php
        echo $this->Form->input('usr_email');
        echo $this->Form->input('usr_password', array('type' => 'password') );
        ?>
    </fieldset>
<?php echo $this->Form->end(__('Login'));?>
</div>