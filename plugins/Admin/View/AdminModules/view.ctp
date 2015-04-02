<h2><?php  echo __('Module') . ': ' . $module['Module']['toString'];?></h2>
<div id="tabs">
    <ul>
        <li><a href="#tabs-1"><?php  echo __('Details');?></a></li>
        <li><a href="#tabs-2"><?php echo __("Presentation");?></a></li>
	<li><a href="#tabs-3"><?php echo __("Assessments");?></a></li>
	<li><a href="#tabs-4"><?php echo __("Resources");?></a></li>
    </ul>
    <div id="tabs-1">
        <div class="modules view">
            <dl>
            				<dt><?php echo __('Id'); ?></dt>
				<dd>
			<?php echo h($module['Module']['id']); ?>
			&nbsp;
		</dd>
				<dt><?php echo __('Code'); ?></dt>
				<dd>
			<?php echo h($module['Module']['mod_code']); ?>
			&nbsp;
		</dd>
				<dt><?php echo __('Title'); ?></dt>
				<dd>
			<?php echo h($module['Module']['mod_name']); ?>
			&nbsp;
		</dd>
				<dt><?php echo __('Description'); ?></dt>
				<dd>
			<?php echo h($module['Module']['mod_description']); ?>
			&nbsp;
		</dd>
				<dt><?php echo __('mod_created'); ?></dt>
				<dd>
			<?php echo $this->Format->date($module['Module']['mod_created']); ?>
			&nbsp;
		</dd>
				<dt><?php echo __('Modified'); ?></dt>
				<dd>
			<?php echo $this->Format->date($module['Module']['mod_modified']); ?>
			&nbsp;
		</dd>
				<dt><?php echo __('Curriculum'); ?></dt>
				<dd>
			<?php echo h($module['ModCurriculum']['cur_name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Next Module'); ?></dt>
				<dd>
			<?php echo h($module['NextModule']['mod_name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Previous Module'); ?></dt>
				<dd>
			<?php echo h($module['PrevModule']['mod_name']); ?>
			&nbsp;
		</dd>
            	</dl>
        </div>
        <div class="actions">
            <h3><?php echo __('Actions'); ?></h3>
            <ul>
        			<li><?php echo $this->Html->link(__('Edit Module'), array('action' => 'edit', $module['Module']['id'])); ?> </li>
				<li><?php echo $this->Form->postLink(__('Delete Module'), array('action' => 'delete', $module['Module']['id']), null, 
								__('Are you sure you want to delete # %s?', $module['Module']['toString'])); ?> </li>
				<li><?php echo $this->Html->link(__('Back To Curriculum'), array('controller' => 'curriculums',
												'action' => 'view', 
												'#' => 'tabs-2',
												$module['Module']['mod_curriculum_id'])); ?> </li>
            </ul>
        </div>
    </div>
    
    <div id="tabs-2">
        <div class="related">
        	<h3><?php echo __('Presentation');?></h3>
            <div class="modulePresentation table">
            <?php 
                if(isset($module['Module']['mod_presentation_file_id'])){
                	$pdf_file_id = $module['Module']['mod_presentation_file_id'];
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
                    <li><?php echo $this->Html->link(__('Edit Module'), array('action' => 'edit', $module['Module']['id'])); ?> </li>
                </ul>
            </div>
        </div>
    </div>

    <div id="tabs-3">
        <div class="related">
        	<h3><?php echo __('Assessments');?></h3>
            <div class="assessments table">
            <?php echo $this->element('../AdminAssessments/table');?>
            </div>
            <div class="actions">
                <ul>
                    <li><?php echo $this->Html->link(__('New Assessment'), array('plugin' => 'admin', 'controller' => 'admin_assessments', 'action' => 'add', $module['Module']['id']));?> </li>
                </ul>
            </div>
        </div>
    </div>
    
    <div id="tabs-4">
        <div class="related">
        	<h3><?php echo __('Related Resources');?></h3>
            <div class="resources table">
            <?php echo $this->element('../AdminResources/table');?>
            </div>
            <div class="actions">
                <ul>
                    <li><?php echo $this->Html->link(__('New Resource'), array('plugin' => 'admin', 
                    		'controller' => 'admin_resources', 'action' => 'add', 'Module', $module['Module']['id']));?> </li>
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