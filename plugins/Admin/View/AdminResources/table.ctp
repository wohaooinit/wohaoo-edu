<?php

$this->Paginator->options(array(
    'url' => $resourcesTableURL,
    'update' => '.resources.table',
    'evalScripts' => true
));?>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $this->Paginator->sort('res_type', null, array('model' => 'Resource'));?></th>
	<th><?php echo $this->Paginator->sort('res_model', null, array('model' => 'Resource'));?></th>
	<th><?php echo $this->Paginator->sort('res_created', null, array('model' => 'Resource'));?></th>
	<th><?php echo $this->Paginator->sort('res_mp4', null, array('model' => 'Resource'));?></th>
	<th><?php echo $this->Paginator->sort('res_mp3', null, array('model' => 'Resource'));?></th>
	<th><?php echo $this->Paginator->sort('res_embed', null, array('model' => 'Resource'));?></th>
    <th class="actions"><?php echo __('Actions');?></th>
</tr>
<?php
foreach ($resources as $resource): ?>
	<tr>
		<td><?php echo h($resource['Resource']['res_type']); ?>&nbsp;</td>
		<td><?php echo h($resource['Resource']['res_model']); ?>&nbsp;</td>
		<td><?php echo $this->Format->date($resource['Resource']['res_created']); ?>&nbsp;</td>
		<td><?php echo h($resource['Resource']['res_mp4']); ?>&nbsp;</td>
		<td><?php echo $resource['Resource']['res_mp3']; ?>&nbsp;</td>
		<td><?php echo h($resource['Resource']['res_embed']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('plugin' => 'admin', 'controller' => 'admin_resources', 'action' => 'view', $resource['Resource']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('plugin' => 'admin', 'controller' => 'admin_resources', 'action' => 'edit', $resource['Resource']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('plugin' => 'admin', 'controller' => 'admin_resources', 'action' => 'delete', $resource['Resource']['id']), null, 
					__('Are you sure you want to delete  Resource# %s?', $resource['Resource']['toString'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
<p>
<?php
echo $this->Paginator->counter(array(
'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}'),
'model' => 'Resource'
));
?></p>

<div class="paging">
<?php
		echo $this->Paginator->prev('< ' . __('previous'), array('model' => 'Resource'), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => '', 'model' => 'Resource'));
		echo $this->Paginator->next(__('next') . ' >', array('model' => 'Resource'), null, array('class' => 'next disabled'));
	?>
</div>

<?php
      echo $this->Js->writeBuffer();