<h2><?php echo __('Course Modules');?></h2>

<div id="tabs">
     <ul>
         <li><a href="#tabs-1"><?php  echo __('Course Modules List');?></a></li>
     </ul>
    <div id="tabs-1">
        <div class="course_modules index table">
            <?php echo $this->element('../AdminCourseModules/table');?>
        </div>
        <div class="actions">
            <h3><?php echo __('Actions'); ?></h3>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function() {
        $( "#tabs" ).tabs();
    });
</script>