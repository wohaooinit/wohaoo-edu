<?php

$this->Paginator->options(array(
    'url' => $translationsTableURL,
    'update' => '.translations.table',
    'evalScripts' => true
));?>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $this->Paginator->sort('t9n_guid', null, array('model' => 'Translation'));?></th>
	<th><?php echo $this->Paginator->sort('t9n_orig_lang', null, array('model' => 'Translation'));?></th>
	<th><?php echo $this->Paginator->sort('t9n_orig_text', null, array('model' => 'Translation'));?></th>
	<th><?php echo $this->Paginator->sort('t9n_trans_lang', null, array('model' => 'Translation'));?></th>
	<th><?php echo $this->Paginator->sort('t9n_trans_text', null, array('model' => 'Translation'));?></th>
    <th class="actions"><?php echo __('Actions');?></th>
</tr>
<?php
foreach ($translations as $translation): ?>
	<tr>
		<td><?php echo h($translation['Translation']['t9n_guid']); ?>&nbsp;</td>
		<td><?php echo h($translation['Translation']['t9n_orig_lang']); ?>&nbsp;</td>
		<td><?php echo h($translation['Translation']['t9n_orig_text']); ?>&nbsp;</td>
		<td><?php echo h($translation['Translation']['t9n_trans_lang']); ?>&nbsp;</td>
		<td><?php echo h($translation['Translation']['t9n_trans_text']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('plugin' => 'admin', 'controller' => 'admin_translations', 'action' => 'view', $translation['Translation']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('plugin' => 'admin', 'controller' => 'admin_translations', 'action' => 'edit', $translation['Translation']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('plugin' => 'admin', 'controller' => 'admin_translations', 'action' => 'delete', $translation['Translation']['id']), null, __('Are you sure you want to delete  Translation# %s?', $translation['Translation']['toString'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
<p>
<?php
echo $this->Paginator->counter(array(
'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}'),
'model' => 'Translation'
));
?></p>

<div class="paging">
<?php
		echo $this->Paginator->prev('< ' . __('previous'), array('model' => 'Translation'), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => '', 'model' => 'Translation'));
		echo $this->Paginator->next(__('next') . ' >', array('model' => 'Translation'), null, array('class' => 'next disabled'));
	?>
</div>

<?php
      echo $this->Js->writeBuffer();