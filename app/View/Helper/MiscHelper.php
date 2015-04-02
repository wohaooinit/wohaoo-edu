<?php

class MiscHelper extends AppHelper{
	public $chain = array();
	
	/**
	 *This function helps formatting text labels in plural
	 *examples: format_plural(0, "year") will return "", format_plural(1, "year") => "year", 
	 *format_plural(2, "year") => "years".
	 *@param $val, the quantity indicator
	 *@param $string, the singular version of the text
	 *@returns the text in the plural or an empty string
	 */
	public function format_plural($val, $string){
		if($val <= 0)
			return "";
		if($val == 1)
			return $string;
		return $string . 's';
	}
	
	/**
	 *This function is of interest when obtaining dotted name version
	 *from a squared name representation.
	 *ex.: data[Model][field] => data.Model.field
	 *@param $squared_name, the squared name representation of the name
	 *@returns a string
	 */
	public function getDotName($squared_name){
		$this->log('getDotName =>', 'debug');
		$this->log("squared_name=${squared_name}", 'debug');
		//returns the dotted notation of a name
		//data[A][field] => A.field
		$regex = "/^data\[([^\]]+)\]\[([^\]]+)\]$/";
		$ok = preg_match($regex, $squared_name, $matches, PREG_OFFSET_CAPTURE);
			
		$class = $matches[1][0];
		$field = $matches[2][0];
		
		$name = "$class.$field";
		$this->log("dotName=$name", 'debug');
		$this->log('getDotName <=', 'debug');
		return $name; 
	}
}
?>