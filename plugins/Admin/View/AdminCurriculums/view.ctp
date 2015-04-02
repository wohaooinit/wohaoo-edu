<h2><?php  echo __('Curriculum') . ': ' . $curriculum['Curriculum']['toString'];?></h2>
<div id="tabs">
    <ul>
        <li><a href="#tabs-1"><?php  echo __('Curriculum Details');?></a></li>
		<li><a href="#tabs-2"><?php echo __('Curriculum Modules');?></a></li>
		<li><a href="#tabs-3"><?php echo __('Curriculum Resources');?></a></li>
		<li><a href="#tabs-4"><?php echo __('Curriculum Presentation');?></a></li>
    </ul>

    <div id="tabs-1">
        <div class="curriculums view">
            <dl>
            				<dt><?php echo __('Id'); ?></dt>
				<dd>
			<?php echo h($curriculum['Curriculum']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Image'); ?></dt>
				<dd>
			<?php echo $this->Format->image($curriculum['Curriculum']['cur_img_id'], 128, 128);  ?>
			&nbsp;
		</dd>
				<dt><?php echo __('Code'); ?></dt>
				<dd>
			<?php echo h($curriculum['Curriculum']['cur_code']); ?>
			&nbsp;
		</dd>
				<dt><?php echo __('Short Name'); ?></dt>
				<dd>
			<?php echo h($curriculum['Curriculum']['cur_short_name']); ?>
			&nbsp;
		</dd>
				<dt><?php echo __('Title'); ?></dt>
				<dd>
			<?php echo h($curriculum['Curriculum']['cur_name']); ?>
			&nbsp;
		</dd>
				<dt><?php echo __('Description'); ?></dt>
				<dd>
			<?php echo $curriculum['Curriculum']['cur_description']; ?>
			&nbsp;
		</dd>
				<dt><?php echo __('Copyright'); ?></dt>
				<dd>
			<?php echo $curriculum['Curriculum']['cur_copyright']; ?>
			&nbsp;
		</dd>
				<dt><?php echo __('Created'); ?></dt>
				<dd>
			<?php echo $this->Format->date($curriculum['Curriculum']['cur_created']); ?>
			&nbsp;
		</dd>
				<dt><?php echo __('Modified'); ?></dt>
				<dd>
			<?php echo $this->Format->date($curriculum['Curriculum']['cur_modified']); ?>
			&nbsp;
		</dd>
				<dt><?php echo __('Is Deleted'); ?></dt>
				<dd>
			<?php echo h($curriculum['Curriculum']['cur_is_deleted']); ?>
			&nbsp;
		</dd>
            	</dl>
        </div>
        <div class="actions">
            <h3><?php echo __('Actions'); ?></h3>
            <ul>
        			<li><?php echo $this->Html->link(__('Edit'), array('plugin' => 'admin', 'controller' => 'admin_curriculums', 'action' => 'edit', $curriculum['Curriculum']['id'])); ?></li>
				<li><?php echo $this->Form->postLink(__('Delete'), array('plugin' => 'admin', 'action' => 'delete', $curriculum['Curriculum']['id']), null, __('Are you sure you want to delete # %s?', $curriculum['Curriculum']['toString'])); ?> </li>
				<li><?php echo $this->Html->link(__('List'), array('plugin' => 'admin', 'action' => 'index')); ?> </li>
				<li><?php echo $this->Html->link(__('New'), array('plugin' => 'admin', 'action' => 'add')); ?> </li>
            </ul>
        </div>
    </div>

    <div id="tabs-2">
        <div class="related">
        	<h3><?php echo __('Related Curriculum Modules');?></h3>
            <div class="curriculumModules table">
            <?php echo $this->element('../AdminModules/table');?>
            </div>
            <div class="actions">
                <ul>
                    <li><?php echo $this->Html->link(__('New Module'), array('plugin' => 'admin', 'controller' => 'admin_modules', 'action' => 'add', $curriculum['Curriculum']['id']));?> </li>
                </ul>
            </div>
        </div>
    </div>
    
     <div id="tabs-3">
        <div class="related">
        	<h3><?php echo __('Related Resources');?></h3>
            <div class="resources table">
            <?php echo $this->element('../AdminResources/table');?>
            </div>
            <div class="actions">
                <ul>
                    <li><?php echo $this->Html->link(__('New Resource'), array('plugin' => 'admin', 
                    		'controller' => 'admin_resources', 'action' => 'add', 'Curriculum', $curriculum['Curriculum']['id']));?> </li>
                </ul>
            </div>
        </div>
    </div>
    
    <div id="tabs-4">
        <div class="related">
        	<h3><?php echo __('Presentation');?></h3>
            <div class="curriculumPresentation table">
            <?php 
                if(isset($curriculum['Curriculum']['cur_presentation_file_id'])){
                	$pdf_file_id = $curriculum['Curriculum']['cur_presentation_file_id'];
    	     ?>
    	     		<iframe id="viewer" src ="/ViewerJS/#../documents/<?php echo $pdf_file_id;?>/presentation.pdf" 
    	     			width='100%' height='600' 
    	     			allowfullscreen webkitallowfullscreen></iframe>
    	     <?php
            	}
            ?>
            </div>
            <div class="actions">
                <ul>
                    <li><?php echo $this->Html->link(__('Edit Curriculum'), array('action' => 'edit', $curriculum['Curriculum']['id'])); ?> </li>
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