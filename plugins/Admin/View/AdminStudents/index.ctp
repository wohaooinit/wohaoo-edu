<h2><?php echo __('Students');?></h2>

<div id="tabs">
     <ul>
         <li><a href="#tabs-1"><?php  echo __('Students List');?></a></li>
     </ul>
    <div id="tabs-1">
        <div class="students index table">
            <?php echo $this->element('../AdminStudents/table');?>
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