<?php
	/**
	 * Generic Ajax Form
	 * Expecting the following variables:
	 * -name, caption, width, height, action_url, error
	 * -hidden_fields
	 * -fields{ id, name, caption, class, spellcheck, type, value}
	 */
	 
	if(!isset($method))
		$method = 'POST';
	if(!isset($style))
		$style = '';
	if(!isset($onreturn))
		$onreturn = '';
?>
<script type="text/javascript">
	$(function(){
		debugger;
		$(".<?php echo $name;?>-show").fancybox({
				'href': "#<?php echo $name;?>-box",
				'modal': false,
				'frameWidth': <?php echo $width;?>, 
				'frameHeight': <?php echo $height;?>,
				helpers: {
				  title : {
					  type : 'float'
				  }
			    }
		});
		jQuery('#<?php echo $name;?>-box').css({'width': '<?php echo $width;?>', 'height': '<?php echo $height;?>'});
	});
</script>
<div id="<?php echo $name;?>-container" style="<?php echo $style;?>" class="fn-ajax-form-container">
	<div id="<?php echo $name;?>-box" class="fn-ajax-form-box">
		<h2><?php echo $caption;?> <strong></strong></h2>
		<form id="<?php echo $name;?>" class="fn-ajax-form" action='<?php echo $action_url;?>' 
		      method="<?php echo $method;?>" onreturn="<?php echo $onreturn;?>" >
			<?php
				foreach($hidden_fields as $id => $props){
					if(!isset($props['value']))
						$props['value'] = "";
			?>
			<input class="<?php echo $id;?>_input"
			  		 type="hidden" 
			  		 name="<?php echo $props['name'];?>" 
			  		 id="<?php echo $id;?>" 
			  		 value="<?php echo $props['value'];?>">
			<?php
				}
			?>
			<label class="fn-ajax-form-error-label"><?php if(isset($error)) echo $error;?></label>
			<?php
				foreach($fields as $id => $props){
					if(!isset($props['value']))
						$props['value'] = "";
					$value = $props['value'];
					if(!isset($props['spellcheck']))
						$props['spellcheck'] = "";
					if(!isset($props['type']))
						$props['type'] = "input";
			?>
			<div class="<?php echo $id;?>-div">
			  <label for="<?php echo $id;?>" class="fn-ajax-form-input-label">
			  	<strong class="<?php echo $id;?>_label"><?php echo $props['caption'];?></strong>
			  </label>
			  <?php
			  	if($props['type'] === 'text' || $props['type'] == 'email'){
			  ?>
				  <input class="<?php echo $id;?>_input"
						 type="<?php echo $props['type'];?>" 
						 spellcheck="<?php echo $props['spellcheck'];?>" 
						 name="<?php echo $props['name'];?>" 
						 id="<?php echo $id;?>" 
						 value="<?php echo $value;?>">
			<?php
				}else
				if($props['type'] === 'info'){	
			?>
				<label name="<?php echo $props['name'];?>"  id="<?php echo $id;?>"><strong><?php echo $props['value'];?></strong></label>
			<?php
				}else
				if($props['type'] === 'readonly'){	
			?>
				<label   id="<?php echo $id;?>"><strong><?php echo $props['value'];?></strong></label>
				<input class="<?php echo $id;?>_input"
			  		 type="hidden" 
			  		 name="<?php echo $props['name'];?>" 
			  		 id="<?php echo $id;?>" 
			  		 value="<?php echo $value;?>">
			<?php
				}else
				if($props['type'] === 'data'){
					$fieldValue = isset($props['value'])? $props['value']: '';
					$dataModel = isset($props['model'])? $props['model']: '';
					$conditions = isset($props['conditions'])? $props['conditions']: '';
					$field = isset($props['field'])? $props['field']: '';
					$dependents = isset($props['dependents'])? $props['dependents']: '';
					$fieldId = $id;
					$map = isset($props['map'])? $props['map']: array();
					$fieldDisplayValue = isset($map[$fieldValue])? $map[$fieldValue]: '';
					$fieldName = isset($props['name'])? $props['name']: '';
					$controller = isset($props['controller'])? $props['controller']: '';
					$action = "autocomplete";
			?>
				<input type="text" class="input-data" 
					    data-model="<?php echo $dataModel;?>"  
					    data-conditions='<?php echo $conditions;?>' 
					    data-field="<?php echo $field;?>"  
					    data-target="<?php echo $fieldId;?>" 
					    data-dependents="<?php echo $dependents;?>" 
					    id="<?php echo $fieldId;?>_display"  
					    value="<?php echo $fieldDisplayValue;?>" >
    		      	        <input type="hidden"  name="<?php echo $fieldName;?>"  
    		      	        	   id="<?php echo $fieldId;?>"  
    		      	        	   value="<?php echo $fieldValue;?>" >
    		      	        <script type="text/javascript">
					$(function(){
						$('#<?php echo $fieldId;?>_display').autocomplete({
							onSelect: function (suggestion) {
								debugger;
								var target  = $(this).attr('data-target');
								if(target){
									var $target = $('#' + target);
									if($target.val() != suggestion.data){
										$target.val(suggestion.data);
										var dependents = '<?php echo $dependents;?>'.split(',');
										$.each(dependents, function(i,dependent){
											$('#' + dependent + '_display').val('');
											$('#' + dependent).val('');
										});
									}
								}
							},
							serviceUrl: function(element, query){
								debugger;
								var conditions = $(this).attr('data-conditions');
								var baseUrl = '/<?php echo $controller;?>/<?php echo $action;?>';
								if(!conditions)
									return baseUrl;
								conditions = conditions.replace(/(\\{[^\\}]+\\})/, function (match, capture) { 
												debugger;
													return eval(capture);
											  });
								return baseUrl + '?' + conditions;
							},
							zIndex: 10000
						});
					});
				</script>
			<?php
				}
			?>
			</div>
			<?php
				}
			?>
			<input type="submit" class="fn-ajax-form-button-submit" value="<?php echo $caption;?>">
		</form>
	</div>
</div>