<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $backendPluginName; ?>
		<?php echo $title_for_layout; ?>
	</title>

	<link rel="stylesheet" type="text/css" href="<?php echo $this->Html->url('/' . $backendPluginNameUnderscored . '-plugin/css/admin/admin.css');?>" />

	<?php
		echo $this->Html->meta('icon');

        echo $this->Html->css('/' . $backendPluginNameUnderscored . '-plugin/css/smoothness/jquery-ui.css');
        echo $this->Html->css('/' . $backendPluginNameUnderscored . '-plugin/css/dropdown/dropdown.css');
        echo $this->Html->css('/' . $backendPluginNameUnderscored . '-plugin/css/dropdown/themes/default/default.css');
        echo $this->Html->css('autocomplete.css');

	echo $this->Html->script('/' . $backendPluginNameUnderscored . '-plugin/js/jquery/jquery-1.8.2.min.js');
        echo $this->Html->script('/' . $backendPluginNameUnderscored . '-plugin/js/jquery/jquery-ui.js');
         echo $this->Html->script('/js/image_uploader/swfupload.js');
        echo $this->Html->script('/js/image_uploader/image_uploader.js');
         echo $this->Html->script('/js/file_uploader/file_uploader.js');
         echo $this->Html->script('/js/autocomplete/jquery.autocomplete.js');
        echo $this->Html->script('/' . $backendPluginNameUnderscored . '-plugin/js/ckeditor/ckeditor.js');
        echo $this->Html->script('/' . $backendPluginNameUnderscored . '-plugin/js/ckfinder/ckfinder.js');

        echo $this->Html->script('/' . $backendPluginNameUnderscored . '-plugin/js/admin.js?v=1');

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
</head>
<body>
	<div id="container">
		<div id="header">
			<h1><?php echo $backendPluginName; ?></h1>
            <?php
                if(!empty($menuItems)) echo $this->element('menu', array('menuItems' => $menuItems));
            ?>
		</div>
		<div id="content">

			<?php echo $this->Session->flash('bad'); ?>
            <?php echo $this->Session->flash('good'); ?>

			<?php echo $this->fetch('content'); ?>
		</div>
		<div id="footer">
		</div>
	</div>
	<?php echo $this->Js->writeBuffer(); ?>
</body>
</html>
