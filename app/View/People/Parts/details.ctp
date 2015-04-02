<div class="persons desc">
	<?php echo $person['Person']['per_description'];?>
</div>
<div class="persons view">
	<dl>
		<dt class="label" class="label"><?php echo __('Picture'); ?></dt>
		<dd class="value">
			<?php echo $this->Format->image($person['Person']['per_img_id'], 42, 38); ?>
		&nbsp;
		</dd >
		<dt class="label"><?php echo __('Name Prefix'); ?></dt>
		<dd class="value">
			<?php echo h($person['Person']['per_prefix']); ?>
		&nbsp;
		</dd>
		<dt class="label"><?php echo __('First Name'); ?></dt>
		<dd class="value">
			<?php echo h($person['Person']['per_first_name']); ?>
		&nbsp;
		</dd>
		<dt class="label"><?php echo __('Last Name'); ?></dt>
		<dd class="value">
			<?php echo h($person['Person']['per_last_name']); ?>
		&nbsp;
		</dd>
		<dt class="label"><?php echo __('Name Suffix'); ?></dt>
		<dd class="value">
			<?php echo h($person['Person']['per_suffix']); ?>
		&nbsp;
		</dd >
		<dt class="label"><?php echo __('Nick Name'); ?></dt>
		<dd class="value">
			<?php echo h($person['Person']['per_nick_name']); ?>
		&nbsp;
		</dd>
		<dt class="label"><?php echo __('Birth Date'); ?></dt>
		<dd class="value">
			<?php echo $this->Format->date($person['Person']['per_birth_date']); ?>
		&nbsp;
		</dd>
		<dt class="label"><?php echo __('Country'); ?></dt>
		<dd class="value">
			<?php echo h($person['PerCountry']['con_display_name']); ?>
		&nbsp;
		</dd>
		<dt class="label"><?php echo __('Region'); ?></dt>
		<dd class="value">
			<?php echo h($person['PerRegion']['reg_display_name']); ?>
		&nbsp;
		</dd>
		<dt class="label"><?php echo __('Town'); ?></dt>
		<dd class="value">
			<?php echo h($person['Person']['per_town']); ?>
		&nbsp;
		</dd>
		<dt class="label"><?php echo __('Street1'); ?></dt>
		<dd class="value">
			<?php echo h($person['Person']['per_street1']); ?>
		&nbsp;
		</dd>
		<dt class="label"><?php echo __('Street2'); ?></dt>
		<dd class="value">
			<?php echo h($person['Person']['per_street2']); ?>
		&nbsp;
		</dd>
		<dt class="label"><?php echo __('Post Code'); ?></dt>
		<dd class="value">
			<?php echo h($person['Person']['per_post_code']); ?>
		&nbsp;
		</dd>
		<dt class="label"><?php echo __('Office Tel'); ?></dt>
		<dd class="value">
			<?php echo h($person['Person']['per_office_tel']); ?>
		&nbsp;
		</dd>
		<dt class="label"><?php echo __('Mobile Tel'); ?></dt>
		<dd class="value">
			<?php echo h($person['Person']['per_mobile_tel']); ?>
		&nbsp;
		</dd>
	</dl>
</div>