<?php

$this->Paginator->options(array(
    'url' => $studentsTableURL,
    'update' => '.students.table',
    'evalScripts' => true
));?>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $this->Paginator->sort('stu_code', null, array('model' => 'Student'));?></th>
	<th><?php echo $this->Paginator->sort('stu_person_id', null, array('model' => 'Student'));?></th>
	<th><?php echo $this->Paginator->sort('stu_person_id', null, array('model' => 'Student'));?></th>
	<th><?php echo $this->Paginator->sort('stu_person_id', null, array('model' => 'Student'));?></th>
	<th><?php echo $this->Paginator->sort('stu_created', null, array('model' => 'Student'));?></th>
    <th class="actions"><?php echo __('Actions');?></th>
</tr>
<?php
foreach ($students as $student): ?>
	<tr>
		<td><?php echo h($student['Student']['stu_code']); ?>&nbsp;</td>
		<td><?php echo h($student['StuPerson']['per_first_name']); ?>&nbsp;</td>
		<td><?php echo h($student['StuPerson']['per_last_name']); ?>&nbsp;</td>
		<td><?php echo $this->Format->data("Country", "id", $student['StuPerson']['per_country_id'], false, "con_code"); ?>&nbsp;</td>
		<td><?php echo h($student['Student']['stu_created']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('plugin' => 'admin', 'controller' => 'admin_students', 'action' => 'view', $student['Student']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('plugin' => 'admin', 'controller' => 'admin_students', 'action' => 'delete', $student['Student']['id']), null, __('Are you sure you want to delete  Student# %s?', $student['Student']['toString'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
<p>
<?php
echo $this->Paginator->counter(array(
'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}'),
'model' => 'Student'
));
?></p>

<div class="paging">
<?php
		echo $this->Paginator->prev('< ' . __('previous'), array('model' => 'Student'), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => '', 'model' => 'Student'));
		echo $this->Paginator->next(__('next') . ' >', array('model' => 'Student'), null, array('class' => 'next disabled'));
	?>
</div>

<?php
      echo $this->Js->writeBuffer();