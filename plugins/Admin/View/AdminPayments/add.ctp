<div class="payments form">
<?php echo $this->ExtendedForm->create('Payment');?>
	<fieldset>
		<legend><?php echo __('Add Payment'); ?></legend>
	<?php
		echo $this->ExtendedForm->input('pay_type_id', array('type' => 'data', 
												             'model' => 'PaymentType', 
												             'field' => 'pat_display_name', 
												             'label' => __('Type'),
												             'conditions' => 'use_codes=0'));
		echo $this->ExtendedForm->input('pay_transaction_id', 
			array('type' => 'text', 'label' => __('Transaction ID')));
		echo $this->ExtendedForm->input('pay_model_name', array('label' => __('Model')));
		echo $this->ExtendedForm->input('pay_object_id', 
			array('type' => 'text', 'label' => __('Object ID')));
		echo $this->ExtendedForm->input('pay_is_approved', array('label' => __('Is Approved')));
		echo $this->ExtendedForm->input('pay_currency_id', array('type' => 'data', 
												             'model' => 'Currency', 
												             'field' => 'cur_display_name', 
												             'label' => __('Currency'),
												             'conditions' => 'use_codes=0'));
		echo $this->ExtendedForm->input('pay_amount', array('label' => __('Amount')));
		echo $this->ExtendedForm->input('pay_medium', array('label' => __('Medium')));
	?>
	</fieldset>
<?php echo $this->ExtendedForm->end(__('Save'));?>
</div>