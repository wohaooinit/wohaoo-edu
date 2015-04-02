<?php

$this->Paginator->options(array(
    'url' => $usersTableURL,
    'update' => '.users.table',
    'evalScripts' => true
));?>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $this->Paginator->sort('id', null, array('model' => 'User'));?></th>
	<th><?php echo  __("First Name"); ?></th>
	<th><?php echo __("Last Name"); ?></th>
	<th><?php echo  __("Country Id"); ?></th>
	<th><?php echo $this->Paginator->sort('usr_last_ip', null, array('model' => 'User'));?></th>
	<th><?php echo $this->Paginator->sort('usr_email', null, array('model' => 'User'));?></th>
	<th><?php echo $this->Paginator->sort('usr_created', null, array('model' => 'User'));?></th>
	<th><?php echo $this->Paginator->sort('usr_is_active', null, array('model' => 'User'));?></th>
    <th class="actions"><?php echo __('Actions');?></th>
</tr>
<?php
foreach ($users as $user): ?>
	<tr>
		<td><?php echo h($user['User']['id']); ?>&nbsp;</td>
		<td><?php echo h($user['UsrPerson']['per_first_name']); ?>&nbsp;</td>
		<td><?php echo h($user['UsrPerson']['per_last_name']); ?>&nbsp;</td>
		<td><?php echo $this->Format->data("Country", "id", $user['UsrPerson']['per_country_id'], false, "con_display_name"); ?>&nbsp;</td>
		<td><?php echo h($user['User']['usr_last_ip']); ?>&nbsp;</td>
		<td><?php echo h($user['User']['usr_email']); ?>&nbsp;</td>
		<td><?php echo h($user['User']['usr_created']); ?>&nbsp;</td>
		<td><?php echo h($user['User']['usr_is_active']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('plugin' => 'admin', 'controller' => 'admin_users', 'action' => 'view', $user['User']['id'])); ?>
			<?php echo $this->Html->link(__('Approve'), array('plugin' => 'admin', 'controller' => 'admin_users', 'action' => 'approve', $user['User']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('plugin' => 'admin', 'controller' => 'admin_users', 'action' => 'delete', $user['User']['id']), null, __('Are you sure you want to delete # %s?', $user['User']['toString'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
<p>
<?php
echo $this->Paginator->counter(array(
'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}'),
'model' => 'User'
));
?></p>

<div class="paging">
<?php
		echo $this->Paginator->prev('< ' . __('previous'), array('model' => 'User'), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => '', 'model' => 'User'));
		echo $this->Paginator->next(__('next') . ' >', array('model' => 'User'), null, array('class' => 'next disabled'));
	?>
</div>

<?php
      echo $this->Js->writeBuffer();