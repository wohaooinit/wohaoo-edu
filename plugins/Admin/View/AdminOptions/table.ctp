<?php

$this->Paginator->options(array(
    'url' => $optionsTableURL,
    'update' => '.options.table',
    'evalScripts' => true
));?>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $this->Paginator->sort('opt_code', null, array('model' => 'Option'));?></th>
	<th><?php echo $this->Paginator->sort('opt_display_text', null, array('model' => 'Option'));?></th>
	<th><?php echo $this->Paginator->sort('opt_is_ok', null, array('model' => 'Option'));?></th>
    <th class="actions"><?php echo __('Actions');?></th>
</tr>
<?php
foreach ($options as $option): ?>
	<tr>
		<td><?php echo h($option['Option']['opt_code']); ?>&nbsp;</td>
		<td><?php echo h($option['Option']['opt_display_text']); ?>&nbsp;</td>
		<td><?php echo h($option['Option']['opt_is_ok']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('plugin' => 'admin', 'controller' => 'admin_options', 'action' => 'view', $option['Option']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('plugin' => 'admin', 'controller' => 'admin_options', 'action' => 'edit', $option['Option']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('plugin' => 'admin', 'controller' => 'admin_options', 'action' => 'delete', $option['Option']['id']), null, __('Are you sure you want to delete  Option# %s?', $option['Option']['toString'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
<p>
<?php
echo $this->Paginator->counter(array(
'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}'),
'model' => 'Option'
));
?></p>

<div class="paging">
<?php
		echo $this->Paginator->prev('< ' . __('previous'), array('model' => 'Option'), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => '', 'model' => 'Option'));
		echo $this->Paginator->next(__('next') . ' >', array('model' => 'Option'), null, array('class' => 'next disabled'));
	?>
</div>

<?php
      echo $this->Js->writeBuffer();