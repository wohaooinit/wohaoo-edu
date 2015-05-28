<h2><?php echo __('Payments');?></h2>

<div id="tabs">
     <ul>
         <li><a href="#tabs-1"><?php  echo __('Payments List');?></a></li>
     </ul>
    <div id="tabs-1">
        <div class="payments index table">
            <?php echo $this->element('../AdminPayments/table');?>
        </div>
        <div class="actions">
            <h3><?php echo __('Actions'); ?></h3>
            <ul>
                <li><?php echo $this->Html->link(__('New Payment'), array('action' => 'add')); ?></li>
            </ul>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function() {
        $( "#tabs" ).tabs();
    });
</script>