<h2><?php  echo __('Option') . ': ' . $option['Option']['toString'];?></h2>
<div id="tabs">
    <ul>
        <li><a href="#tabs-1"><?php  echo __('Details');?></a></li>
    </ul>
    <div id="tabs-1">
        <div class="options view">
            <dl>
            				<dt><?php echo __('Id'); ?></dt>
				<dd>
			<?php echo h($option['Option']['id']); ?>
			&nbsp;
		</dd>
				<dt><?php echo __('Code'); ?></dt>
				<dd>
			<?php echo h($option['Option']['opt_code']); ?>
			&nbsp;
		</dd>
				<dt><?php echo __('Text'); ?></dt>
				<dd>
			<?php echo h($option['Option']['opt_display_text']); ?>
			&nbsp;
		</dd>
            	</dl>
        </div>
        <div class="actions">
            <h3><?php echo __('Actions'); ?></h3>
            <ul>
        			<li><?php echo $this->Html->link(__('Edit Option'), array('action' => 'edit', $option['Option']['id'])); ?> </li>
				<li><?php echo $this->Form->postLink(__('Delete Option'), array('action' => 'delete', $option['Option']['id']), null, __('Are you sure you want to delete # %s?', $option['Option']['toString'])); ?> </li>
				<li><?php echo $this->Html->link(__('List Options'), array('action' => 'index')); ?> </li>
            </ul>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function() {
        $( "#tabs" ).tabs();
    });
</script>