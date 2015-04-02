<?php

$this->Paginator->options(array(
    'url' => $coursesTableURL,
    'update' => '.courses.table',
    'evalScripts' => true
));?>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $this->Paginator->sort('cou_curriculum_id', null, array('model' => 'Course'));?></th>
	<th><?php echo $this->Paginator->sort('cou_payment_id', null, array('model' => 'Course'));?></th>
	<th><?php echo $this->Paginator->sort('cou_created', null, array('model' => 'Course'));?></th>
	<th><?php echo $this->Paginator->sort('cou_graduated', null, array('model' => 'Course'));?></th>
    <th class="actions"><?php echo __('Actions');?></th>
</tr>
<?php
foreach ($courses as $course): ?>
	<tr>
		<td><?php echo h($course['CouCurriculum']['cur_code']); ?>&nbsp;</td>
		<td><?php echo h($course['CouPayment']['pay_transaction_id']); ?>&nbsp;</td>
		<td><?php echo $this->Format->datetime($course['Course']['cou_created']); ?>&nbsp;</td>
		<td><?php echo $this->Format->datetime($course['Course']['cou_graduated']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('plugin' => 'admin', 'controller' => 'admin_courses', 'action' => 'view', $course['Course']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('plugin' => 'admin', 'controller' => 'admin_courses', 'action' => 'delete', $course['Course']['id']), null, __('Are you sure you want to delete  Course# %s?', $course['Course']['toString'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
<p>
<?php
echo $this->Paginator->counter(array(
'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}'),
'model' => 'Course'
));
?></p>

<div class="paging">
<?php
		echo $this->Paginator->prev('< ' . __('previous'), array('model' => 'Course'), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => '', 'model' => 'Course'));
		echo $this->Paginator->next(__('next') . ' >', array('model' => 'Course'), null, array('class' => 'next disabled'));
	?>
</div>

<?php
      echo $this->Js->writeBuffer();