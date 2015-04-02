<?php
if(!isset($name))
	$name = "gn-menu";
if(isset($items)){
	$this->log("items is defined", 'debug');
?>
<ul id="<?php echo $name;?>" class="gn-menu-main">
	<li class="gn-trigger">
		<a class="gn-icon gn-icon-menu"><span><?php echo __("Menu");?></span></a>
		<nav class="gn-menu-wrapper">
			<div class="gn-scroller">
				<ul class="gn-menu">
					<?php
					foreach($items as $itemName => $item){
						$itemType = isset($item['type'])?$item['type']: '';
						$iconSet = isset($item['icon_set'])?$item['icon_set']:"";
						$icon = isset($item['icon'])?$item['icon']:"";
						$subItems = isset($item['items']) && is_array($item['items'])? $item['items']: array();
						$class = isset($item['class'])?$item['class']:"";
						$target = isset($item['target'])?$item['target']:"#";
					?>
					<li class="<?php echo $class;?>">
						<?php
							if($itemType === "search"){
						?>
						<input placeholder="<?php echo $itemName;?>" type="search" class="gn-search">
						<?php
							}
						?>
						<a  href="<?php echo $target;?>" class="<?php echo $iconSet;?> <?php echo $icon;?>">
							<span><?php echo $itemName;?></span>
						</a>
					</li>
					<?php
					}
					?>
				</ul>
			</div><!-- /gn-scroller -->
		</nav>
	</li>
</ul>
<script>
	new gnMenu( document.getElementById( '<?php echo $name;?>' ) );
</script>
<?php
}
?>