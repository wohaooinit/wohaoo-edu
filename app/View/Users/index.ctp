<h2><?php echo __('Users');?></h2>

<div id="tabs">
     <ul>
         <li><a href="#tabs-1"><?php  echo __('Users List');?></a></li>
     </ul>
    <div id="tabs-1">

        <div class="users index table">
            <?php echo $this->element('../Users/table');?>
        </div>
        <div class="actions">
            <h3><?php echo __('Actions'); ?></h3>
            <ul>
                <li><?php echo $this->Html->link(__('New User'), array('action' => 'add')); ?></li>
            </ul>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function() {
        $( "#tabs" ).tabs();
    });
</script>