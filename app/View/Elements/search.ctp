<?php
if(!isset($height)){
	$height = "29";
}
if(!strstr($height, "px")){
	$height = $height . "px";
}

if(!isset($font_size)){
	$font_size = "13px";
}

if(!strstr($font_size, "px")){
	$font_size = $font_size . "px";
}
if(!isset($show_header)){
	$show_header = false;
}

if(!isset($search_term)){
	$search_term = "";
}
?>
<form 
	<?php
	if(isset($method) && $method){
		echo "method=\"$method\"";
	}
	?> 
	id="fn-search-form" 
	<?php
	if(isset($action) && $action){
		echo "action=\"$action\"";
	}
	?> 
	onsubmit="if (document.getElementById('fn-search-term').value == '')  return false;">
		<?php
			if($show_header){
		?>
		<div id="search-box-header">
			<span id="logo-header" class="logo-header">
				<img src="/img/logo_all.png" width="600">
			</span>
		</div>
		<?php
			}
		?>
		<div id="search-box">
			<button id="fn-search-button" 
				onclick="$('#fn-search-form').submit();"
				title="<?php echo $title;?>"
				style='<?php
				if(isset($width)){
				echo "width: $width;";
				}
				echo "height: ${height};";
				?>'
				>
				<span class="icon-button">
					<?php
					if(!isset($label) || !$label){
					?>
					<img id="fn-search-loupe" src="/img/pixel.gif" alt="Search">
					<?php
					}else{
					?>
					<strong><?php echo $label;?></strong>
					<?php
					}
					?>
				</span>
			</button>
			<div id="fn-search-logo">
				<img src="/img/logo.gif" width="<?php echo $height;?>" height="<?php echo $height;?>">
			</div>
			<div id="fn-search-terms" style='height: <?php echo $height;?>'>
				<label>
					<input type="text" id="fn-search-term" 
						   name="<?php echo $name;?>" tabindex="1" 
						   spellcheck="false"
						   value="<?php echo $search_term;?>"
						   style='height: <?php echo $height;?>; font-size: <?php echo $font_size;?>;'>
				</label>
			</div>
		</div>
	</form>
	<script type="text/javascript">
		Cufon.replace('#company-name');
	</script>