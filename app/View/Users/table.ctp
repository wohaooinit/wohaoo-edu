<?php
$this->Paginator->options(array(
    'url' => $usersTableURL,
    'update' => '.users.table',
    'evalScripts' => true
));

$vars = array("name" => "dj-activate-user-ticket-form", "caption" => __("Activate Ticket"),
			  "action_url" => $this->Html->url(
								array("controller" => "users", 
									  "action" => "activate")),
			  "method" => "POST", "width" => 250, "height" => 250,
			  "hidden_fields" => array("user_id" => array("name" => "data[user_id]",
															   "value" => 0)
							),
			  "fields" => array("usr_guid" => array("name" => "data[user_guid]",
													 "spellcheck" => false,
													 "type" => "info",
													 "caption" => __("User ID"),
													 "value" => ""),
								"ticket_id" => array("name" => "data[ticket_id]",
													 "spellcheck" => false,
													 "type" => "text",
													 'cols' => 10,
													 "caption" => __("Ticket ID"),
													 "value" => ""
								)
			)
);
echo $this->element('ajax_form', $vars);
?>
<table cellpadding="0" cellspacing="0">
<tr>
	<th></th>
	<th><?php echo $this->Paginator->sort('usr_guid', null, array('model' => 'User'));?></th>
	<th><?php echo $this->Paginator->sort('usr_first_name', null, array('model' => 'User'));?></th>
	<th><?php echo $this->Paginator->sort('usr_last_name', null, array('model' => 'User'));?></th>
	<th><?php echo $this->Paginator->sort('usr_created', null, array('model' => 'User'));?></th>
    	<th class="actions"><?php echo __('Actions');?></th>
</tr>
<?php
foreach ($users as $user): ?>
	<tr>
		<td><?php echo $this->Format->image($user['User']['usr_pict_id'], 42, 38); ?></td>
		<td><?php echo h($user['User']['usr_guid']); ?>&nbsp;</td>
		<td><?php echo h($user['User']['usr_first_name']); ?>&nbsp;</td>
		<td><?php echo h($user['User']['usr_last_name']); ?>&nbsp;</td>
		<td><?php echo h($user['User']['usr_created']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $user['User']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $user['User']['id'])); ?>
			<?php echo $this->Html->link(__('Activate Ticket'), array('action' => 'activate', $user['User']['id']),
									array('class' => 'dj-activate-user-ticket-form-show',
										   'userId' => $user['User']['id'],
										   'userGuid' => $user['User']['usr_guid'])
								); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
<script type="text/javascript">
	$(".dj-activate-user-ticket-form-show").click(function(){
		$('input[id$=user_id]').val($(this).attr('userId')); 
		$('label[id$=usr_guid]').text($(this).attr('userGuid'));
	});
</script>
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