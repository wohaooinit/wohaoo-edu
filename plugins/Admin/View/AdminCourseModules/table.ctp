<?php

$this->Paginator->options(array(
    'url' => $course_modulesTableURL,
    'update' => '.course_modules.table',
    'evalScripts' => true
));?>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $this->Paginator->sort('com_module_id', null, array('model' => 'CourseModule'));?></th>
	<th><?php echo $this->Paginator->sort('com_student_id', null, array('model' => 'CourseModule'));?></th>
	<th><?php echo $this->Paginator->sort('com_student_id', null, array('model' => 'CourseModule'));?></th>
	<th><?php echo $this->Paginator->sort('com_created', null, array('model' => 'CourseModule'));?></th>
	<th><?php echo $this->Paginator->sort('com_is_approved', null, array('model' => 'CourseModule'));?></th>
	<th><?php echo $this->Paginator->sort('com_approved', null, array('model' => 'CourseModule'));?></th>
        <th class="actions"><?php echo __('Actions');?></th>
</tr>
<?php
foreach ($course_modules as $course_module): ?>
	<tr>
		<td><?php echo h($course_module['ComModule']['mod_name']); ?>&nbsp;</td>
		<td><?php echo $this->Format->data("Person", "id", $course_module['ComStudent']['stu_person_id'], false, "per_first_name"); ?>&nbsp;</td>
		<td><?php echo $this->Format->data("Person", "id", $course_module['ComStudent']['stu_person_id'], false, "per_last_name"); ?>&nbsp;</td>
		<td><?php echo $this->Format->datetime($course_module['CourseModule']['com_created']); ?>&nbsp;</td>
		<td><?php echo h($course_module['CourseModule']['com_is_approved']); ?>&nbsp;</td>
		<td><?php echo $this->Format->datetime($course_module['CourseModule']['com_approved']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Form->postLink(__('Delete'), array('plugin' => 'admin', 'controller' => 'admin_course_modules', 'action' => 'delete', $course_module['CourseModule']['id']), null, __('Are you sure you want to delete  CourseModule# %s?', $course_module['CourseModule']['toString'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
<p>
<?php
echo $this->Paginator->counter(array(
'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}'),
'model' => 'CourseModule'
));
?></p>

<div class="paging">
<?php
		echo $this->Paginator->prev('< ' . __('previous'), array('model' => 'CourseModule'), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => '', 'model' => 'CourseModule'));
		echo $this->Paginator->next(__('next') . ' >', array('model' => 'CourseModule'), null, array('class' => 'next disabled'));
	?>
</div>

<?php
      echo $this->Js->writeBuffer();