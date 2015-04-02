<?php

$this->Paginator->options(array(
    'url' => $paymentsTableURL,
    'update' => '.payments.table',
    'evalScripts' => true
));?>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $this->Paginator->sort('pay_type_id', null, array('model' => 'Payment'));?></th>
	<th><?php echo $this->Paginator->sort('pay_amount', null, array('model' => 'Payment'));?></th>
	<th><?php echo $this->Paginator->sort('pay_currency_id', null, array('model' => 'Payment'));?></th>
	<th><?php echo $this->Paginator->sort('pay_started', null, array('model' => 'Payment'));?></th>
	<th><?php echo $this->Paginator->sort('pay_ended', null, array('model' => 'Payment'));?></th>
	<th><?php echo $this->Paginator->sort('pay_transaction_id', null, array('model' => 'Payment'));?></th>
	<th><?php echo $this->Paginator->sort('pay_model_name', null, array('model' => 'Payment'));?></th>
	<th><?php echo $this->Paginator->sort('pay_object_id', null, array('model' => 'Payment'));?></th>
	<th><?php echo $this->Paginator->sort('pay_is_approved', null, array('model' => 'Payment'));?></th>
        <th class="actions"><?php echo __('Actions');?></th>
</tr>
<?php
foreach ($payments as $payment): ?>
	<tr>
		<td><?php echo h($payment['PayType']['pat_code']); ?>&nbsp;</td>
		<td><?php echo h($payment['Payment']['pay_amount']); ?>&nbsp;</td>
		<td><?php echo h($payment['PayCurrency']['cur_code']); ?>&nbsp;</td>
		<td><?php echo h($payment['Payment']['pay_started']); ?>&nbsp;</td>
		<td><?php echo h($payment['Payment']['pay_ended']); ?>&nbsp;</td>
		<td><?php echo h($payment['Payment']['pay_transaction_id']); ?>&nbsp;</td>
		<td><?php echo h($payment['Payment']['pay_model_name']); ?>&nbsp;</td>
		<td><?php echo h($payment['Payment']['pay_object_id']); ?>&nbsp;</td>
		<td><?php echo h($payment['Payment']['pay_is_approved']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Form->postLink(__('Delete'), array('plugin' => 'admin', 'controller' => 'admin_payments', 'action' => 'delete', $payment['Payment']['id']), null, __('Are you sure you want to delete  Payment# %s?', $payment['Payment']['toString'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
<p>
<?php
echo $this->Paginator->counter(array(
'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}'),
'model' => 'Payment'
));
?></p>

<div class="paging">
<?php
		echo $this->Paginator->prev('< ' . __('previous'), array('model' => 'Payment'), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => '', 'model' => 'Payment'));
		echo $this->Paginator->next(__('next') . ' >', array('model' => 'Payment'), null, array('class' => 'next disabled'));
	?>
</div>

<?php
      echo $this->Js->writeBuffer();