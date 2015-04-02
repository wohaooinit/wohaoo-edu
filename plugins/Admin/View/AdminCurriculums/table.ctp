<?php

$this->Paginator->options(array(
    'url' => $curriculumsTableURL,
    'update' => '.curriculums.table',
    'evalScripts' => true
));?>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $this->Paginator->sort('cur_img_id', null, array('model' => 'Curriculum'));?></th>
	<th><?php echo $this->Paginator->sort('cur_code', null, array('model' => 'Curriculum'));?></th>
	<th><?php echo $this->Paginator->sort('cur_short_name', null, array('model' => 'Curriculum'));?></th>
	<th><?php echo $this->Paginator->sort('cur_name', null, array('model' => 'Curriculum'));?></th>
	<th><?php echo $this->Paginator->sort('cur_created', null, array('model' => 'Curriculum'));?></th>
	<th><?php echo $this->Paginator->sort('cur_modified', null, array('model' => 'Curriculum'));?></th>
	<th><?php echo $this->Paginator->sort('cur_lang_id', null, array('model' => 'Curriculum'));?></th>
    <th class="actions"><?php echo __('Actions');?></th>
</tr>
<?php
foreach ($curriculums as $curriculum): ?>
	<tr>
		<td><?php echo $this->Format->image($curriculum['Curriculum']['cur_img_id'], 128, 128); ?></td>
		<td><?php echo h($curriculum['Curriculum']['cur_code']); ?>&nbsp;</td>
		<td><?php echo h($curriculum['Curriculum']['cur_short_name']); ?>&nbsp;</td>
		<td><?php echo h($curriculum['Curriculum']['cur_name']); ?>&nbsp;</td>
		<td><?php echo $this->Format->date($curriculum['Curriculum']['cur_created']); ?>&nbsp;</td>
		<td><?php echo $this->Format->date($curriculum['Curriculum']['cur_modified']); ?>&nbsp;</td>
		<td><?php echo h($curriculum['CurLang']['lan_display_name']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('plugin' => 'admin', 'controller' => 'admin_curriculums', 'action' => 'view', $curriculum['Curriculum']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('plugin' => 'admin', 'controller' => 'admin_curriculums', 'action' => 'edit', $curriculum['Curriculum']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('plugin' => 'admin', 'controller' => 'admin_curriculums', 'action' => 'delete', $curriculum['Curriculum']['id']), null, __('Are you sure you want to delete  Curriculum# %s?', $curriculum['Curriculum']['toString'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
<p>
<?php
echo $this->Paginator->counter(array(
'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}'),
'model' => 'Curriculum'
));
?></p>

<div class="paging">
<?php
		echo $this->Paginator->prev('< ' . __('previous'), array('model' => 'Curriculum'), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => '', 'model' => 'Curriculum'));
		echo $this->Paginator->next(__('next') . ' >', array('model' => 'Curriculum'), null, array('class' => 'next disabled'));
	?>
</div>

<?php
      echo $this->Js->writeBuffer();