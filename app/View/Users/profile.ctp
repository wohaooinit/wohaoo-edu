<?php
?>
<h2><?php  echo __('User') . ': ' . $user['UsrPerson']['per_first_name'] . ' ' . $user['UsrPerson']['per_last_name'];?></h2>
<div id="tabs">
    <ul>
        <li><a href="#tabs-1"><?php  echo __('User Profile');?></a></li>
        <li><a href="#tabs-2"><?php  echo __('Personal Info');?></a></li>
    </ul>

    <div id="tabs-1">
        <div class="attributes view">
            <?php echo $this->element('../Attributes/table');?>
        </div>
    </div>
    <div id="tabs-2">
        <div class="personals view">
            <table cellpadding="0" cellspacing="0">
		<tr>
			<td><?php echo  __("Password"); ?>&nbsp;</td>
			<td>************</td>
			<td class="actions">
				<?php
					$user_id = $this->Security->user_id();
					echo $this->Html2->link(__('Change'), array('controller' => 'users', 
															'action' => 'password_change', $user_id),
														array('disabled' => (!$this->Security->has("admin") && ($user_id !== $user['User']['id'])))); 
				?>
			</td>
		</tr>
		<tr>
			<td><?php echo  __("Username"); ?>&nbsp;</td>
			<td><?php echo $this->Format->username($user['User']['id']);?></td>
			<td class="actions">
				<?php
					$user_id = $this->Security->user_id();
					echo $this->Html2->link(__('Change'), array('controller' => 'users', 
															'action' => 'username_change', $user_id),
														array('disabled' => (!$this->Security->has("admin") && ($user_id !== $user['User']['id'])))); 
				?>
			</td>
		</tr>
		
	  </table>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function() {
        $( "#tabs" ).tabs();
    });
</script>