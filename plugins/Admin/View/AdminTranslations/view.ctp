<h2><?php  echo __('Translation') . ': ' . $translation['Translation']['toString'];?></h2>
<div id="tabs">
    <ul>
        <li><a href="#tabs-1"><?php  echo __('Details');?></a></li>
    </ul>
    <div id="tabs-1">
        <div class="translations view">
            <dl>
            				<dt><?php echo __('Id'); ?></dt>
				<dd>
			<?php echo h($translation['Translation']['id']); ?>
			&nbsp;
		</dd>
				<dt><?php echo __('Orig Lang'); ?></dt>
				<dd>
			<?php echo h($translation['Translation']['t9n_orig_lang']); ?>
			&nbsp;
		</dd>
				<dt><?php echo __('Original Text'); ?></dt>
				<dd>
			<?php echo h($translation['Translation']['t9n_orig_text']); ?>
			&nbsp;
		</dd>
            	</dl>
            			<dt><?php echo __('Translation Lang'); ?></dt>
				<dd>
			<?php echo h($translation['Translation']['t9n_trans_lang']); ?>
			&nbsp;
		</dd>
				<dt><?php echo __('Translation Text'); ?></dt>
				<dd>
			<?php echo h($translation['Translation']['t9n_trans_text']); ?>
			&nbsp;
		</dd>
				<dt><?php echo __('Translator User'); ?></dt>
				<dd>
			<?php echo h($translation['Translation']['t9n_trans_text']); ?>
			&nbsp;
		</dd>
            	</dl>
        </div>
        <div class="actions">
            <h3><?php echo __('Actions'); ?></h3>
            <ul>
        			<li><?php echo $this->Html->link(__('Edit Translation'), array('action' => 'edit', $translation['Translation']['id'])); ?> </li>
				<li><?php echo $this->Form->postLink(__('Delete Translation'), array('action' => 'delete', $translation['Translation']['id']), null, __('Are you sure you want to delete # %s?', $translation['Translation']['toString'])); ?> </li>
				<li><?php echo $this->Html->link(__('List Translations'), array('action' => 'index')); ?> </li>
            </ul>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function() {
        $( "#tabs" ).tabs();
    });
</script>