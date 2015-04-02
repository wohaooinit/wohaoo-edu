<?php

$this->Paginator->options(array(
    'url' => $assessmentsTableURL,
    'update' => '.assessments.table',
    'evalScripts' => true
));?>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $this->Paginator->sort('ass_module_id', null, array('model' => 'Assessment'));?></th>
	<th><?php echo $this->Paginator->sort('ass_code', null, array('model' => 'Assessment'));?></th>
	<th><?php echo $this->Paginator->sort('ass_type', null, array('model' => 'Assessment'));?></th>
	<th><?php echo $this->Paginator->sort('ass_description', null, array('model' => 'Assessment'));?></th>
	<th><?php echo $this->Paginator->sort('ass_created', null, array('model' => 'Assessment'));?></th>
	<th><?php echo $this->Paginator->sort('ass_serial', null, array('model' => 'Assessment'));?></th>
	<th><?php echo $this->Paginator->sort('ass_option_count', null, array('model' => 'Assessment'));?></th>
    <th class="actions"><?php echo __('Actions');?></th>
</tr>
<?php
foreach ($assessments as $assessment): ?>
	<tr>
		<td><?php echo h($assessment['AssModule']['mod_code']); ?>&nbsp;</td>
		<td><?php echo h($assessment['Assessment']['ass_code']); ?>&nbsp;</td>
		<td><?php echo h($assessment['Assessment']['ass_type']); ?>&nbsp;</td>
		<td><?php echo $assessment['Assessment']['ass_description']; ?>&nbsp;</td>
		<td><?php echo $this->Format->date($assessment['Assessment']['ass_created']); ?>&nbsp;</td>
		<td><?php echo h($assessment['Assessment']['ass_serial']); ?>&nbsp;</td>
		<td><?php echo h($assessment['Assessment']['ass_option_count']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('plugin' => 'admin', 'controller' => 'admin_assessments', 'action' => 'view', $assessment['Assessment']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('plugin' => 'admin', 'controller' => 'admin_assessments', 'action' => 'edit', $assessment['Assessment']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('plugin' => 'admin', 'controller' => 'admin_assessments', 'action' => 'delete', $assessment['Assessment']['id']), null, 
					__('Are you sure you want to delete  Assessment# %s?', $assessment['Assessment']['toString'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
<p>
<?php
echo $this->Paginator->counter(array(
'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}'),
'model' => 'Assessment'
));
?></p>

<div class="paging">
<?php
		echo $this->Paginator->prev('< ' . __('previous'), array('model' => 'Assessment'), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => '', 'model' => 'Assessment'));
		echo $this->Paginator->next(__('next') . ' >', array('model' => 'Assessment'), null, array('class' => 'next disabled'));
	?>
</div>

<?php
      echo $this->Js->writeBuffer();