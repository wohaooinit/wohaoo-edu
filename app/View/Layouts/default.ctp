<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8"/>
		<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,minimum-scale=1,user-scalable=no"/>
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<meta name="apple-touch-fullscreen" content="yes" />
		<meta names="apple-mobile-web-app-status-bar-style" content="black-translucent" /> 
		
		<!-- prevent cache -->
		<meta http-equiv="cache-control" content="no-cache">
		<meta http-equiv="pragma" content="no-cache">
		
		<title><?php echo $title_for_layout; ?></title>
		<script type="text/javascript" src="/js/crypto-js/md5.js" ></script>
		<script type="text/javascript" src="/translations/database/en?langs=all&hash=1018192" ></script>
		
		
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/dojo/1.9.2/dojox/mobile/deviceTheme.js" ></script>
		<link href="https://ajax.googleapis.com/ajax/libs/dojo/1.9.2/dojox/mobile/themes/common/domButtons.css" rel="stylesheet"/>
		<link href="https://ajax.googleapis.com/ajax/libs/dojo/1.9.2/dojox/mobile/themes/common/domButtons/DomButtonSilverCircleDownArrow.css" rel="stylesheet"/>
		
		
		<?php
		/*<script type="text/javascript" src="/js/wohaooedu/lib/dojox/mobile/deviceTheme.js" ></script>
		<link href="/js/wohaooedu/lib/dojox/mobile/themes/common/domButtons.css" rel="stylesheet"/>
		<link href="/js/wohaooedu/lib/dojox/mobile/themes/common/domButtons/DomButtonSilverCircleDownArrow.css" rel="stylesheet"/>*/
		?>
		<?php/**
		<script type="text/javascript" src="/js/dojo-src/dojox/mobile/deviceTheme.js" ></script>
		<link href="/js/dojo-src/dojox/mobile/themes/common/domButtons.css" rel="stylesheet"/>
		<link href="/js/dojo-src/dojox/mobile/themes/common/domButtons/DomButtonSilverCircleDownArrow.css" rel="stylesheet"/>
		*/?>
		
		<?php
			echo $this->Html2->less('/css/mobile'); 
			$is_android = false;
			
			if($is_android){
				echo $this->Html2->less('/css/android'); 
			}
			echo $this->fetch('meta');
			echo $this->fetch('css');
			echo $this->fetch('script');
		?>
		<!-- Configure Dojo first -->
		<script>
			dojoConfig = {
				async: true,
				locale: 'fr',
				packs: [
					// Any references to a "demo" resource should load modules locally, *not* from CDN
					{
						name: "wedu",
						location: "/js/wohaooedu/lib/edu"
					}
				],
				packages: [
					// Any references to a "demo" resource should load modules locally, *not* from CDN
					{
						name: "wedu",
						location: "/js/wohaooedu/lib/edu"
					}
				],
				parseOnLoad: true
			};
		</script>
		<?php
		/*<script type="text/javascript" src="/js/wohaooedu/lib/dojo/dojo.js"></script>
		<script type="text/javascript" src="/js/wohaooedu/lib/edu/wohaoo.js"></script>*/?>
		
		<?php/**
		<script type="text/javascript" src="/js/dojo-src/dojo/dojo.js"></script>
		*/?>
	
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/dojo/1.9.2/dojo/dojo.js"></script>
		<script type="text/javascript" src="/js/wohaooedu/lib/edu/wohaoo.js">
		
		<script type="text/javascript">
			require(["dojox/mobile/parser", "dijit/registry", "dojo/query!css3", 
			"edu/wohaoo/mobile/_T9nMixin",
			"edu/wohaoo/mobile/CurriculumsView",
			"edu/wohaoo/mobile/LoginView",
			"edu/wohaoo/mobile/ModulesView",
			"dojox/mobile/IconMenu", "dojox/mobile/IconMenuItem",
			 "dojox/mobile", "dojox/mobile/TabBar",
			 "dojox/mobile/Switch",
			 "dojox/mobile/Slider",
			 "dojox/mobile/IconContainer",
			 "dojox/mobile/DatePicker", "dojo/date/stamp", "dojox/mobile/Opener",
			 "dojox/mobile/compat",
			 "dojo/domReady!"], 
			function(parser, registry, dQuery){
				window.registry = registry;
				window.show = function(dlg){
					registry.byId(dlg).show();
				}
				window.hide = function(dlg){
					registry.byId(dlg).hide();
				}
			});
		</script>
		<script type="text/javascript">
			if(typeof Sys === 'undefined'){
				Sys = function (){
				};
				Sys.user = "";
				Sys.message = function(msg){
					alert(msg);
				};
			
				Sys.setUser = function(id){
					Sys.user = id;
				};
			
				Sys.getUser = function(){
					return Sys.user;
				};
			
				Sys.getHttpScheme = function(){
					return "<?php /*echo Configure::read('Config.scheme');*/?>";
				};
				
				Sys.getHttpHost = function(){
					return "<?php /*echo Configure::read('Config.host');*/?>";
				};
				
				Sys.encrypt = function (str){
					return str;
				};
				
				Sys.getActiveItemId = function(){
					return 0;
				};
				
				Sys.getCurriculumFavorites = function(){
					return [];
				};
				
				Sys.setCurriculumFavorites = function(a){
				
				};
			}
		</script>
	</head>
	<body class="mobile" style="visibility:hidden;">
		<?php echo $this->fetch('content'); ?>
	</body>
</html>