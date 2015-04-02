<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $title_for_layout; ?>
	</title>
	<!-- Configure Dojo first -->
	<script>
		dojoConfig = {
			async: true,
			packs: [
				// Any references to a "demo" resource should load modules locally, *not* from CDN
				{
					name: "fn",
					location: "/js/dojo-src/fn"
				}
			],
			locale: 'en',
			packages: [
				// Any references to a "demo" resource should load modules locally, *not* from CDN
				{
					name: "fn",
					location: "/js/dojo-src/fn"
				},
				'dgrid',
				'put-selector',
				'xstyle',
				'dbootstrap'
			],
			parseOnLoad: false
		};
	</script>
	<?php
		
	echo $this->Html->meta('icon');

        echo $this->Html->css('smoothness/jquery-ui.css');
        echo $this->Html->css('dropdown/dropdown.css');
        echo $this->Html->css('autocomplete.css');
        echo $this->Html->css('dropdown/themes/default/default.css');
	echo $this->Html->css('fancybox/jquery.fancybox');
	echo $this->Html->css('Zebra/zebra_dialog');
	echo $this->Html->css('footnation/footnation.css');
	echo $this->Html2->less('/js/twitter/css/bootstrap');
	echo $this->Html2->less('/js/nexus/css/normalize');
	echo $this->Html2->less('/js/nexus/css/demo');
	echo $this->Html2->less('/js/nexus/css/component');
	echo $this->Html2->less('/css/footnation/listview'); 
	echo $this->Html->css('/js/dojo-src/dbootstrap/theme/dbootstrap/dbootstrap.css'); 
	echo $this->Html2->less('/css/footnation/page'); 
	echo $this->Html2->less('/css/footnation/comments'); 
	echo $this->Html2->less('/css/footnation/media'); 
	echo $this->Html2->less('/css/footnation/stats'); 
	
	echo $this->Html->script('jquery.js');
        echo $this->Html->script('jquery/jquery-ui.js');
        echo $this->Html->script('jquery.mockjax.js');
        echo $this->Html->script('autocomplete/jquery.autocomplete.js');
        echo $this->Html->script('ckeditor/ckeditor.js');
        echo $this->Html->script('ckfinder/ckfinder.js');
        echo $this->Html->script('image_uploader/swfupload.js');
        echo $this->Html->script('image_uploader/image_uploader.js');
        echo $this->Html->script('fancybox/jquery.fancybox.js');
        echo $this->Html->script('Zebra/zebra_dialog.src');
        echo $this->Html->script('jwplayer/jwplayer');
        echo $this->Html->script('twitter/js/bootstrap');
        echo $this->Html->script('nexus/js/classie');
        echo $this->Html->script('nexus/js/gnmenu');
        echo $this->Html->script('nexus/js/modernizr.custom');

        echo $this->Html->script('footnation.js?v=2');

	echo $this->fetch('meta');
	echo $this->fetch('css');
	echo $this->fetch('script');
	?>
</head>
<body class="dbootstrap">
	<div id="container">
		<div id="header">
			<?php
				$sidebar = $this->fetch("sidebar");
				$this->log("sidebar=" . $sidebar, 'debug');
				if(!empty($menuItems)) echo $this->element('menu', 
						array('menuItems' => $menuItems,
							   'sidebar' => $sidebar));
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
	<?php echo $this->Html->script('dojo-src/dojo/dojo.js');?>
	<script type="text/javascript">
		require(["dojo/parser", "dojo/query!css3", "fn/listview/ListView", "fn/listview/FilterButton", "fn/listview/MoveToFolderButton",
			"fn/listview/FolderButton", "dojo/domReady!"], function(parser, dojoQuery, ListView){
			try{
				window.dojoQuery = dojoQuery;
				parser.parse();
			}catch(Err){
			
			}
		});
	</script>
</body>
</html>
