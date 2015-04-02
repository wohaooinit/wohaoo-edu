<h2><?php  echo __('Assessment') . ': ' . $assessment['Assessment']['toString'];?></h2>
<div id="tabs">
    <ul>
        <li><a href="#tabs-1"><?php  echo __('Details');?></a></li>
        <li><a href="#tabs-2"><?php echo __("Answers");?></a></li>
    </ul>
    <div id="tabs-1">
        <div class="assessments view">
            <dl>
            				<dt><?php echo __('Id'); ?></dt>
				<dd>
			<?php echo h($assessment['Assessment']['id']); ?>
			&nbsp;
		</dd>
				<dt><?php echo __('Module'); ?></dt>
				<dd>
			<?php echo h($assessment['AssModule']['mod_code']); ?>
			&nbsp;
		</dd>
				<dt><?php echo __('Code'); ?></dt>
				<dd>
			<?php echo h($assessment['Assessment']['ass_code']); ?>
			&nbsp;
		</dd>
				<dt><?php echo __('Type'); ?></dt>
				<dd>
			<?php echo h($assessment['Assessment']['ass_type']); ?>
			&nbsp;
		</dd>
				<dt><?php echo __('Description'); ?></dt>
				<dd>
			<?php echo ($assessment['Assessment']['ass_description']); ?>
			&nbsp;
		</dd>
				<dt><?php echo __('Created'); ?></dt>
				<dd>
			<?php echo $this->Format->date($assessment['Assessment']['ass_created']); ?>
			&nbsp;
		</dd>
				<dt><?php echo __('Serial Number'); ?></dt>
				<dd>
			<?php echo $this->Format->date($assessment['Assessment']['ass_serial']); ?>
			&nbsp;
		</dd>
            	</dl>
        </div>
        <div class="actions">
            <h3><?php echo __('Actions'); ?></h3>
            <ul>
            			 <li><?php echo $this->Html->link(__('New Answer'), array('plugin' => 'admin', 'controller' => 'admin_options', 'action' => 'add', $assessment['Assessment']['id']));?> </li>
        			<li><?php echo $this->Html->link(__('Edit Assessment'), array('action' => 'edit', $assessment['Assessment']['id'])); ?> </li>
				<li><?php echo $this->Form->postLink(__('Delete Assessment'), array('action' => 'delete', $assessment['Assessment']['id']), null, __('Are you sure you want to delete # %s?', $assessment['Assessment']['toString'])); ?> </li>
				<li><?php echo $this->Html->link(__('Back To Module'), array('controller' => 'modules',
												'action' => 'view', 
												'#' => 'tabs-3',
												$assessment['Assessment']['ass_module_id'])); ?> </li>
            </ul>
        </div>
    </div>
    
    <div id="tabs-2">
        <div class="related">
        	<h3><?php echo __('Answers');?></h3>
            <div class="assessmentAnswers table">
            <?php 
                if(isset($options)){
                	 echo $this->element('../AdminOptions/table');
            	}
            ?>
            </div>
            <div class="actions">
                <ul>
                    <li><?php echo $this->Html->link(__('New Answer'), array('plugin' => 'admin', 'controller' => 'admin_options', 'action' => 'add', $assessment['Assessment']['id']));?> </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function() {
        $( "#tabs" ).tabs();
    });
</script>