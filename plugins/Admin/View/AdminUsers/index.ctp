<h2><?php echo __('Users');?></h2>

<div id="tabs">
     <ul>
         <li><a href="#tabs-1"><?php  echo __('Users List');?></a></li>
     </ul>
    <div id="tabs-1">

        <div class="users index table">
            <?php echo $this->element('../AdminUsers/table');?>
        </div>
        <div class="actions">
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function() {
        $( "#tabs" ).tabs();
    });
</script>