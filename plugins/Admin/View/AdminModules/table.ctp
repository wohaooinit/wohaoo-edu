<?php

$this->Paginator->options(array(
    'url' => $modulesTableURL,
    'update' => '.modules.table',
    'evalScripts' => true
));?>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $this->Paginator->sort('mod_code', null, array('model' => 'Module'));?></th>
	<th><?php echo $this->Paginator->sort('mod_name', null, array('model' => 'Module'));?></th>
	<th><?php echo $this->Paginator->sort('mod_created', null, array('model' => 'Module'));?></th>
	<th><?php echo $this->Paginator->sort('mod_modified', null, array('model' => 'Module'));?></th>
	<th><?php echo $this->Paginator->sort('mod_curriculum_id', null, array('model' => 'Module'));?></th>
	<th><?php echo $this->Paginator->sort('mod_prev_module_id', null, array('model' => 'Module'));?></th>
	<th><?php echo $this->Paginator->sort('mod_next_module_id', null, array('model' => 'Module'));?></th>
    <th class="actions"><?php echo __('Actions');?></th>
</tr>
<?php
foreach ($modules as $module): ?>
	<tr>
		<td><?php echo h($module['Module']['mod_code']); ?>&nbsp;</td>
		<td><?php echo h($module['Module']['mod_name']); ?>&nbsp;</td>
		<td><?php echo $this->Format->date($module['Module']['mod_created']); ?>&nbsp;</td>
		<td><?php echo $this->Format->date($module['Module']['mod_modified']); ?>&nbsp;</td>
		<td><?php echo h($module['ModCurriculum']['cur_short_name']); ?>&nbsp;</td>
		<td><?php if(isset($module['PrevModule']))  echo h($module['PrevModule']['mod_name']); ?>&nbsp;</td>
		<td><?php if(isset($module['NextModule'])) echo h($module['NextModule']['mod_name']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('plugin' => 'admin', 'controller' => 'admin_modules', 'action' => 'view', $module['Module']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('plugin' => 'admin', 'controller' => 'admin_modules', 'action' => 'edit', $module['Module']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('plugin' => 'admin', 'controller' => 'admin_modules', 'action' => 'delete', $module['Module']['id']), null, __('Are you sure you want to delete  Module# %s?', $module['Module']['toString'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
<p>
<?php
echo $this->Paginator->counter(array(
'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}'),
'model' => 'Module'
));
?></p>

<div class="paging">
<?php
		echo $this->Paginator->prev('< ' . __('previous'), array('model' => 'Module'), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => '', 'model' => 'Module'));
		echo $this->Paginator->next(__('next') . ' >', array('model' => 'Module'), null, array('class' => 'next disabled'));
	?>
</div>

<?php
      echo $this->Js->writeBuffer();