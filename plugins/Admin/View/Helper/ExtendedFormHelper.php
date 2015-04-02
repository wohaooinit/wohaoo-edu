<?php
App::uses('FormHelper', 'View/Helper');
App::uses('FormatHelper', 'View/Helper');

class ExtendedFormHelper extends FormHelper
{
	//public $helpers = array('Format');
    public function fieldValue($fieldName){
    	$model = $this->model();
    	$model_var_name = Inflector::underscore($model);
    	$value = "";
    	$model_var = "";
    	if(isset(${$model_var}) && is_array(${$model_var})){
    		$model_var  = ${$model_var};
    	}
    	if($model_var && isset($model_var[$model]) && isset($model_var[$model][$fieldName]))
    		$value = $model_var[$model][$fieldName];
    	return $value;
    }
    
    public function input($fieldName, $options = array()){
    	//<extended_form.input fieldName=$fieldName>
    	$type = (isset($options['type']))? $options['type']: '';
    	
    	$options['id'] = $fieldName;//needed for data field with conditions
    	
    	if ($type === 'readonly'){
    		//	-handle type=label
    		return $this->label($fieldName, $options);
    	}else
    	if ($type=== 'data'){
    		//	-handle type=data, with conditions (ex.: 'country={$("#usr_country").val()}'), model, and field
    		return $this->data($fieldName, $options);
    	}else
    	if ($type === 'image'){
    		//	-handle type=image, show a  SWFUpload file input fied  with name (data[Model][fieldName])
    		return $this->image($fieldName, $options);
    	}else
    	if ($type === 'textarea'){
    		//	-handle type=textarea, show a  SWFUpload file input fied  with name (data[Model][fieldName])
    		return $this->textarea($fieldName, $options);
    	}
    	$results =  parent::input($fieldName, $options);
    	//</extended_form.input fieldName=$fieldName>
    	return $results;
    }
    
    public function info($fieldName, $options = array()){
    	$model = $this->model();
    	$fieldId = $model . Inflector::camelize($fieldName);
    	$fieldValue = isset($options['value'])? $options['value'] :  $this->value($fieldName);
    	if(isset($options['label']))
    		$fieldLabel = $options['label'];
    	$result = "<div class=\"input label\">\n". 
    		      	       "<label name=\"data[{$model}][{$fieldName}]\"  id=\"{$fieldId}\"><strong>{$fieldValue}</strong></label>\n" .
    		      	"</div>";
    	return $result;
    }
    
    public function date($fieldName, $options = array()){
    	$model = $this->model();
    	$fieldId = $model . Inflector::camelize($fieldName);
    	$fieldValue = isset($options['value'])? $options['value'] :  $this->fieldValue($fieldName);
    	$fieldLabel = Inflector::humanize($fieldName);
    	if(isset($options['label']))
    		$fieldLabel = $options['label'];
    	$result = "<div class=\"input label\">\n". 
    		       		"<label for=\"{$fieldId}\">{$fieldLabel}</label>\n" .
    		      	       "<span><input type=\"text\" name=\"data[{$model}][{$fieldName}]\"  id=\"{$fieldId}\" value=\"{$fieldValue}\">" .
    		      	       "<img onclick=\"return showCalendar('{$fieldId}', 'dd-mm-y')\" src=\"/img/calendar.gif\"></span>\n" .
    		      	"</div>";
    	return $result;
    }
    
    public function image($fieldName, $options = array()){
    	$model = $this->model();
    	$fieldId = $model . Inflector::camelize($fieldName);
    	$fieldValue = isset($options['value'])? $options['value'] : $this->value($fieldName, $options);
    	$fieldLabel = Inflector::humanize($fieldName);
    	$fieldEditable = isset($options['editable'])? $options['editable'] : true; 
    	$defaultUrl = isset($options['default'])? $options['default'] : false;
    	
    	if(isset($options['label']))
    		$fieldLabel = $options['label'];
    	$result = "<div class=\"input image\">\n" . 
    		       		"<label for=\"{$fieldId}\"><strong>{$fieldLabel}</strong></label>\n" .
    		      	       "<input type=\"hidden\" name=\"data[{$model}][{$fieldName}]\"  id=\"{$fieldId}\" value=\"{$fieldValue}\">" .
    		      	"</div>";
    	$result .= "<script type=\"text/javascript\">
				$(function(){
					$('#{$fieldId} ').imageUploader({
						width: 213,
						height: 236,
						editable: {$fieldEditable},
						default_url: '{$defaultUrl}'
					});
				});
			</script>";
    	return $result;
    }
    
    public function data($fieldName, $options = array()){
    	$model = $this->model();
    	$fieldId = isset($options['id'])? $options['id']: $model . Inflector::camelize($fieldName);
    	$fieldValue = isset($options['value'])? $options['value'] : $this->value($fieldName, $options);
    	$fieldLabel = Inflector::humanize($fieldName);
    	if(isset($options['label']))
    		$fieldLabel = $options['label'];
    	$conditions = isset($options['conditions'])? $options['conditions'] : '';
    	$field = isset($options['field'])? $options['field'] : '';
    	$dependents = isset($options['dependents'])? $options['dependents'] : '';
    	$dataModel = isset($options['model'])? $options['model'] : $model;
    	$dataMap= isset($options['map'])? $options['map'] : array();
    	$readonly = isset($options['readonly'])? $options['readonly'] : false;
    	//$fieldDisplayValue = isset($dataMap[$fieldValue])? $dataMap[$fieldValue]: $fieldValue;
    	$fieldDisplayValue = "";
    	
    	$controller = Inflector::underscore($dataModel);
    	$controller = Inflector::pluralize($controller);
    	$action = "autocomplete";
    	
    	$use_codes = 0;
	if($field && $field !== 'id')
		$use_codes = 1;
	$url = "/$controller/autocomplete?use_codes=${use_codes}";
	$dataMap = $this->requestAction($url);
	$fieldDisplayValue = isset($dataMap[$fieldValue])? $dataMap[$fieldValue]: $fieldValue;
	
	$result = "<div class=\"input label\">\n" .
			"<label for=\"{$fieldId}\">{$fieldLabel}</label>\n";
	if(!$readonly){
    		$result  .=  "<input type=\"text\" class=\"input-data\" data-model=\"{$dataModel}\"  data-conditions='{$conditions}' data-field=\"{$field}\"  data-target=\"{$fieldId}\" data-dependents=\"{$dependents}\" id=\"{$fieldId}_display\" value=\"{$fieldDisplayValue}\">\n" .
    		      	        "<input type=\"hidden\"  name=\"data[{$model}][{$fieldName}]\"  id=\"{$fieldId}\" value=\"{$fieldValue}\">\n" ;
    	}else{
    		$result .= "<label id=\"{$fieldId}\">{$fieldDisplayValue}</label>\n";
    	}
    	$result .=	  "</div>";
    
    	$result .= "<script type=\"text/javascript\">
					$(function(){
					$('#{$fieldId}_display').autocomplete({
						onSelect: function (suggestion) {
							debugger;
							var target  = $(this).attr('data-target');
							if(target){
								var \$target = $('#' + target);
								if(\$target.val() != suggestion.data){
									\$target.val(suggestion.data);
									var dependents = '$dependents'.split(',');
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
							var baseUrl = '/$controller/$action';
							if(!conditions)
								return baseUrl;
							conditions = conditions.replace(/(\\{[^\\}]+\\})/, function (match, capture) { 
											debugger;
									    		return eval(capture);
									      });
							return baseUrl + '?' + conditions;
						},
						zIndex: 99999
					});
				});
			</script>";
    	return $result;
    }
    
    public function text($fieldName, $options = array()) {
    	//<extended_form.text>
        $options = $this->_initInputField($fieldName, $options);
        $modelKey = $this->model();
        $fieldKey = $this->field();
        
        if(empty($options['value']))
        {
            //get value?
            if(!empty($this->request->query[$fieldKey])) $options['value'] = $this->request->query[$fieldKey];
        }

        $results = parent::text($fieldName, $options);
        //</extended_form.text>
        return $results;
    }

    public function textarea($fieldName, $options = array()) {
        $result = parent::textarea($fieldName, $options);
        if($fieldName === 'content' || $fieldName === 'contentid'){ 
        	return $result;
        }
	$options = $this->_initInputField($fieldName, $options);
	//add ckeditor
	$result .= '<script type="text/javascript">CKEDITOR.replace(\''.$options['id'].'\', {toolbar: \'Basic\', forcePasteAsPlainText: true});</script>';
	//die($options['id']);
        return $result;
    }

    public function dateTime($fieldName, $dateFormat = 'DMY', $timeFormat = '12', $attributes = array()) {
        $attributes['empty'] = '-';
        if (empty($attributes['value'])) {
            $attributes = $this->value($attributes, $fieldName);
        }
        if($attributes['value'] == '1970-01-01 01:00:00')
        {
            $attributes['value'] = '1970-00-00 00:00:00';
        }
        $result = parent::dateTime($fieldName, $dateFormat, $timeFormat, $attributes);
        return $result;
    }

    public function file($fieldName, $options = array()) {
        //remove file type param
        $fileType = 'file';
        if(!empty($options['file_type'])) {
            $fileType = $options['file_type'];
            unset($options['file_type']);
        }
        $resourceType = 'Files';
        switch($fileType) {
            case 'image':
                $resourceType = 'Images';
                break;
        }
        $result = parent::text($fieldName, $options);
        $options = $this->_initInputField($fieldName, $options);
        //add CKEditor file browser Support
        $result .= "<input type=\"button\" value=\"Browse Server\" id=\"{$options['id']}BrowseServerButton\" />
        <script type=\"text/javascript\">
        (function(){
            function setFileField(fileUrl, data) {
                console.log(fileUrl);
                document.getElementById('{$options['id']}').value = fileUrl.substr('".Router::url('/')."files/{$fileType}/'.length + 1);
            }
            function browseServer() {
                var finder = new CKFinder();
                finder.resourceType = '{$resourceType}';
                finder.selectActionFunction = setFileField;
                finder.popup();
            }
            $('#{$options['id']}BrowseServerButton').bind('click', browseServer);
        })();
        </script>";
        return $result;
    }
}
