<h2><?php  echo __('User') . ': ' . $user['User']['toString'];?></h2>
<div id="tabs">
    <ul>
        <li><a href="#tabs-1"><?php  echo __('User Details');?></a></li>
    </ul>

    <div id="tabs-1">
        <div class="users view">
            <dl>
            				<dt><?php echo __('Id'); ?></dt>
				<dd>
			<?php echo h($user['User']['id']); ?>
			&nbsp;
		</dd>
				<dt><?php echo __('First Name'); ?></dt>
				<dd>
			<?php echo h($user['UsrPerson']['per_first_name']); ?>
			&nbsp;
		</dd>
				<dt><?php echo __('Last Name'); ?></dt>
				<dd>
			<?php echo h($user['UsrPerson']['per_last_name']); ?>
			&nbsp;
		</dd>
				<dt><?php echo __('Creation Date'); ?></dt>
				<dd>
			<?php echo h($user['User']['usr_created']); ?>
			&nbsp;
		</dd>
				<dt><?php echo __('Email'); ?></dt>
				<dd>
			<?php echo h($user['User']['usr_email']); ?>
			&nbsp;
		</dd>
				<dt><?php echo __('Last Login Date'); ?></dt>
				<dd>
			<?php echo h($user['User']['usr_last_connect_date']); ?>
			&nbsp;
		</dd>
				<dt><?php echo __('Is Active'); ?></dt>
				<dd>
			<?php echo h($user['User']['usr_is_active']); ?>
			&nbsp;
		</dd>
				<dt><?php echo __('Activation Date'); ?></dt>
				<dd>
			<?php echo h($user['User']['activation_date']); ?>
			&nbsp;
		</dd>
				<dt><?php echo __('User Type'); ?></dt>
				<dd>
			<?php echo h($user['User']['usr_type']); ?>
			&nbsp;
		</dd>
				<dt><?php echo __('Is Omni Admin'); ?></dt>
				<dd>
			<?php echo h($user['User']['usr_is_admin']); ?>
			&nbsp;
		</dd>
				<dt><?php echo __('Password Hash'); ?></dt>
				<dd>
			<?php echo h($user['User']['usr_password']); ?>
			&nbsp;
		</dd>
				<dt><?php echo __('Is Deleted'); ?></dt>
				<dd>
			<?php echo h($user['User']['usr_is_deleted']); ?>
			&nbsp;
		</dd>
            	</dl>
        </div>
        <div class="actions">
            <h3><?php echo __('Actions'); ?></h3>
            <ul>
        				<li><?php echo $this->Html->link(__('Edit User'), array('action' => 'edit', $user['User']['id'])); ?> </li>
				<li><?php echo $this->Form->postLink(__('Delete User'), array('action' => 'delete', $user['User']['id']), null, __('Are you sure you want to delete # %s?', $user['User']['toString'])); ?> </li>
				<li><?php echo $this->Html->link(__('List Users'), array('action' => 'index')); ?> </li>
            </ul>
        </div>
    </div>

</div>
<script type="text/javascript">
    $(function() {
        $( "#tabs" ).tabs();
    });
</script>