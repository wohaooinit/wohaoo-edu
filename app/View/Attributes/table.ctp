<?php
?>
<table cellpadding="0" cellspacing="0">
<tr>
	<th></th>
	<th><?php echo __('Display Name');?></th>
	<th><?php echo __('Current Value');?></th>
	<th><?php echo __('Created');?></th>
    	<th class="actions"><?php echo __('Actions');?></th>
</tr>
<?php
foreach ($attributes as $attribute): ?>
	<tr id="<?php echo $attribute['Attribute']['att_property_name']?>">
		<td><?php echo h($attribute['Definition']['atd_index']); ?>&nbsp;</td>
		<td><?php echo h($attribute['Definition']['atd_display_name']); ?>&nbsp;</td>
		<td><?php
			$is_image = $attribute['Definition']['atd_is_image'];
			$is_password = $attribute['Definition']['atd_is_password'];
			$is_data = $attribute['Definition']['atd_is_data'];
			$is_date = $attribute['Definition']['atd_is_date'];
			$is_number = $attribute['Definition']['atd_is_number'];
			$is_editable = $attribute['Definition']['atd_is_editable'];
			if($is_image){
				echo $this->Format->image($attribute['Attribute']['att_content'], 42, 38);
			}else
			if($is_password){
				echo $this->Format->password($attribute['Attribute']['att_content']);
			}else
			if($is_data){
				$model = $attribute['Definition']['atd_data_model'];
				$data_key = $attribute['Definition']['atd_data_key'];
				echo $this->Format->data($model, $data_key, $attribute['Attribute']['att_content']);
			}else
			if($is_date){
				//debug("date");
				echo $this->Format->date($attribute['Attribute']['att_content']);
			}else
			if($is_number){
				echo $this->Number->precision($attribute['Attribute']['att_content'], 2);
			}else
			 	echo h($attribute['Attribute']['att_content']); 
		?>&nbsp;</td>
		<td><?php echo h($this->Format->date($attribute['Attribute']['att_created'])); ?>&nbsp;</td>
		<td class="actions">
			<?php 
				if($is_editable)
					echo $this->Html2->link(__('Edit'), array('controller' => 'attributes', 
														'action' => 'edit', $attribute['Attribute']['id'])); 
			?>
		</td>
	</tr>
<?php endforeach; ?>
</table>

<?php
      echo $this->Js->writeBuffer();
?>