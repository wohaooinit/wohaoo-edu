<h2><?php  echo __('Resource') . ': ' . $resource['Resource']['toString'];?></h2>
<div id="tabs">
    <ul>
        <li><a href="#tabs-1"><?php  echo __('Details');?></a></li>
    </ul>
    <div id="tabs-1">
        <div class="resources view">
            <dl>
            				<dt><?php echo __('Id'); ?></dt>
				<dd>
			<?php echo h($resource['Resource']['id']); ?>
			&nbsp;
		</dd>
					<dt><?php echo __('Model'); ?></dt>
				<dd>
			<?php echo h($resource['Resource']['res_model']); ?>
			&nbsp;
		</dd>
		
				<dt><?php echo __('Model ID'); ?></dt>
				<dd>
			<?php echo h($resource['Resource']['res_model_id']); ?>
			&nbsp;
		</dd>
		
				<dt><?php echo __('Video/Audio/Document'); ?></dt>
				<dd>
				<?php
			if($resource['Resource']['res_type'] === 'video'){
				echo $this->Format->video(array('mp4' => $resource['Resource']['res_mp4'], 
											'ogv' => $resource['Resource']['res_ogv'], 
											'webm' => $resource['Resource']['res_webm'], 
											'embed' => $resource['Resource']['res_embed'])); 
			}else 
			if($resource['Resource']['res_type'] === 'audio'){
					echo $this->Format->audio(array('mp3' => $resource['Resource']['res_mp3'], 
											'ogg' => $resource['Resource']['res_ogg'], 
											'wav' => $resource['Resource']['res_wav'],
											'embed' => $resource['Resource']['res_embed'])); 
				}else 
			if($resource['Resource']['res_type'] === 'document'){
					echo $this->Format->document(array('pdf' => $resource['Resource']['res_pdf'],
											'embed' => $resource['Resource']['res_embed'])); 
				}
			?>
			&nbsp;
		</dd>
            	</dl>
        </div>
        <div class="actions">
            <h3><?php echo __('Actions'); ?></h3>
            <ul>
        			<li><?php echo $this->Html->link(__('Edit Resource'), array('action' => 'edit', $resource['Resource']['id'])); ?> </li>
				<li><?php echo $this->Form->postLink(__('Delete Resource'), array('action' => 'delete', $resource['Resource']['id']), null, __('Are you sure you want to delete # %s?', $resource['Resource']['toString'])); ?> </li>
				<li><?php echo $this->Html->link(__('Back'),  
					$this->Format->url($resource['Resource']['res_model'], 'view', $resource['Resource']['res_model_id'], 'admin')); ?> </li>
				
            </ul>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function() {
        $( "#tabs" ).tabs();
    });
</script>