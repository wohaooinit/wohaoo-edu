<h2><?php  echo __('Student') . ': ' . $student['Student']['toString'];?></h2>
<div id="tabs">
    <ul>
        <li><a href="#tabs-1"><?php  echo __('Details');?></a></li>
        <li><a href="#tabs-2"><?php  echo __('Courses');?></a></li>
    </ul>
    <div id="tabs-1">
        <div class="students view">
            <dl>
            				<dt><?php echo __('Id'); ?></dt>
				<dd>
			<?php echo h($student['Student']['id']); ?>
			&nbsp;
		</dd>
				<dt><?php echo __('Code'); ?></dt>
				<dd>
			<?php echo h($student['Student']['stu_code']); ?>
			&nbsp;
		</dd>
				<dt><?php echo __('First Name'); ?></dt>
				<dd>
			<?php echo h($student['StuPerson']['per_first_name']); ?>
			&nbsp;
		</dd>
				<dt><?php echo __('Last Name'); ?></dt>
				<dd>
			<?php echo h($student['StuPerson']['per_first_name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Country'); ?></dt>
				<dd>
			<?php echo $this->Format->data("Country", "id", $student['StuPerson']['per_country_id'], false, "con_code"); ?>
			&nbsp;
		</dd>
            	</dl>
        </div>
        <div class="actions">
            <h3><?php echo __('Actions'); ?></h3>
            <ul>
        			<li><?php echo $this->Form->postLink(__('Delete Student'), array('action' => 'delete', $student['Student']['id']), null, __('Are you sure you want to delete # %s?', $student['Student']['toString'])); ?> </li>
				<li><?php echo $this->Html->link(__('List Students'), array('action' => 'index')); ?> </li>
            </ul>
        </div>
    </div>
    <div id="tabs-2">
        <div class="related">
        	<h3><?php echo __("Student's Courses");?></h3>
            <div class="assessmentCourses table">
            <?php 
                if(isset($courses)){
                	 echo $this->element('../AdminCourses/table');
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