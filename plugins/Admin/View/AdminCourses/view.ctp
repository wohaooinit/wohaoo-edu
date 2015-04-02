<h2><?php  echo __('Course') . ': ' . $course['Course']['toString'];?></h2>
<div id="tabs">
    <ul>
        <li><a href="#tabs-1"><?php  echo __('Details');?></a></li>
        <li><a href="#tabs-2"><?php  echo __('Modules');?></a></li>
    </ul>
    <div id="tabs-1">
        <div class="courses view">
            <dl>
            				<dt><?php echo __('Id'); ?></dt>
				<dd>
			<?php echo h($course['Course']['id']); ?>
			&nbsp;
		</dd>
				<dt><?php echo __('Curriculum'); ?></dt>
				<dd>
			<?php echo h($course['CouCurriculum']['cur_code']); ?>
			&nbsp;
		</dd>
				<dt><?php echo __('Payment Id'); ?></dt>
				<dd>
			<?php echo h($course['CouPayment']['pay_transaction_id']); ?>
			&nbsp;
		</dd>
				<dt><?php echo __('Created'); ?></dt>
				<dd>
			<?php echo  $this->Format->datetime($course['Course']['cou_created']); ?>
			&nbsp;
		</dd>
            	</dl>
        </div>
        <div class="actions">
            <h3><?php echo __('Actions'); ?></h3>
            <ul>
        			<li><?php echo $this->Form->postLink(__('Delete Course'), array('action' => 'delete', $course['Course']['id']), null, __('Are you sure you want to delete # %s?', $course['Course']['toString'])); ?> </li>
				<li><?php echo $this->Html->link(__('List Courses'), array('action' => 'index')); ?> </li>
            </ul>
        </div>
    </div>
    <div id="tabs-2">
        <div class="related">
        	<h3><?php echo __("Course's Courses");?></h3>
            <div class="assessmentModules table">
            <?php 
                if(isset($course_modules)){
                	 echo $this->element('../AdminCourseModules/table');
            	}
            ?>
            </div>
            <div class="actions">
                <ul></ul>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function() {
        $( "#tabs" ).tabs();
    });
</script>