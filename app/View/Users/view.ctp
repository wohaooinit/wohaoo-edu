<?php
	$valid_till = __('N/A');
	$memberships = false;
	if(isset($user['Memberships'])){
		$memberships = $user['Memberships'];
		$memberships_count = count($memberships);
		if($memberships_count){
			$last_membership = $memberships[0];//memberships are ordered mem_created DESC
			$mem_created = $last_membership['mem_created'];
			$mem_duration = $last_membership['mem_duration'];
			$valid_till = $this->Format->date($mem_created + $mem_duration*86400);
		}
	}
	
	$vars = array("name" => "dj-membership-form", "caption" => __("Renew Membership"),
				  "action_url" => $this->Html->url(
									array("controller" => "users", 
										  "action" => "renew")),
				  "method" => "POST", "width" => 250, "height" => 300,
				  "hidden_fields" => array("user_id" => array("name" => "data[user_id]",
																   "value" => $user['User']['id'])
								),
				  "fields" => array("usr_guid" => array("name" => "data[user_guid]",
														 "spellcheck" => false,
														 "type" => "info",
														 "caption" => __("User ID"),
														 "value" => $user['User']['usr_guid']),
									"trx_id" => array("name" => "data[transaction_id]",
														   "spellcheck" => false,
														   "type" => "data",
														   'model' => 'Transaction',
														   'controller' => 'transactions', 
														   'field' => 'id',
														   'map' => $this->requestAction("/transactions/map"),
														   "caption" => __("Payment ID"),
														   "value" => "")
									)
	);
	echo $this->element('ajax_form', $vars);
	
	$vars = array("name" => "dj-activate-ticket-form", "caption" => __("Activate Ticket"),
				  "action_url" => $this->Html->url(
									array("controller" => "users", 
										  "action" => "activate")),
				  "method" => "POST", "width" => 250, "height" => 250,
				  "hidden_fields" => array("user_id" => array("name" => "data[user_id]",
																   "value" => $user['User']['id'])
								),
				  "fields" => array("usr_guid" => array("name" => "data[user_guid]",
														 "spellcheck" => false,
														 "type" => "info",
														 "caption" => __("User ID"),
														 "value" => $user['User']['usr_guid']),
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
<h2><?php  echo __('User') . ': ' . $user['User']['toString'];?></h2>
<div id="tabs">
    <ul>
        <li><a href="#tabs-1"><?php  echo __('User Details');?></a></li>
    </ul>

    <div id="tabs-1">
        <div class="users view">
            <dl>
            			<dt><?php echo __('Picture'); ?></dt>
				<dd>
					<?php echo $this->Format->image($user['User']['usr_pict_id'], 213, 236); ?>
				&nbsp;
				</dd>
            			<dt><?php echo __('Id'); ?></dt>
				<dd>
					<?php echo h($user['User']['id']); ?>
				&nbsp;
				</dd>
				<dt><?php echo __('User ID'); ?></dt>
				<dd>
					<?php echo h($user['User']['usr_guid']); ?>
				&nbsp;
				</dd>
				<dt><?php echo __('Valid Till'); ?></dt>
				<dd>
					<?php echo $valid_till ; ?>
				&nbsp;
				</dd>
				<dt><?php echo __('First Name'); ?></dt>
				<dd>
					<?php echo h($user['User']['usr_first_name']); ?>
				&nbsp;
				</dd>
				<dt><?php echo __('LastName'); ?></dt>
				<dd>
					<?php echo h($user['User']['usr_last_name']); ?>
				&nbsp;
				</dd>
				<dt><?php echo __('Email'); ?></dt>
				<dd>
					<?php echo h($user['User']['usr_email']); ?>
				&nbsp;
				</dd>
				<dt><?php echo __('Birth Date'); ?></dt>
				<dd>
					<?php echo $this->Format->date($user['User']['usr_birth_date']); ?>
				&nbsp;
				</dd>
				<dt><?php echo __('Country'); ?></dt>
				<dd>
					<?php echo h($user['User']['usr_country']); ?>
				&nbsp;
				</dd>
				<dt><?php echo __('Region'); ?></dt>
				<dd>
					<?php echo h($user['User']['usr_region']); ?>
				&nbsp;
				</dd>
            	</dl>
        </div>
        <div class="actions">
            <h3><?php echo __('Actions'); ?></h3>
            <ul>
        			<li><?php echo $this->Html->link(__('Edit User'), array('action' => 'edit', $user['User']['id'])); ?> </li>
				<li><?php echo $this->Html->link(__('Renew Membership'), array( 'action' => 'renew', $user['User']['id']),
												array('class' => 'dj-membership-form-show')
											); ?> </li>
				<li><?php echo $this->Html->link(__('Activate Ticket'), array('action' => 'activate', $user['User']['id']),
									array('class' => 'dj-activate-ticket-form-show')
								); ?> </li>
				<li><?php echo $this->Html->link(__('New User'), array('action' => 'add')); ?> </li>
            </ul>
        </div>
    </div>

</div>
<script type="text/javascript">
    $(function() {
        $( "#tabs" ).tabs();
    });
</script>