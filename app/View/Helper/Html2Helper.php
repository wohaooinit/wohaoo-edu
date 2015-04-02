<?php

App::uses('HtmlHelper', 'View/Helper');

/**
 *Html extended helped
 *-handles 'disabled' attributes for links
 */
class Html2Helper extends HtmlHelper{
	private $_myTags = array('link:disabled' => '<span  %s>%s</span>');
	
	private $_myLessPrefix = "less";
	private $_myLessSuffix = "less";
	
	public function less($path, $rel = null, $options = array()) {
		$path = DS . $this->_myLessPrefix . $path . "." . $this->_myLessSuffix;
		$options += array('block' => null, 'inline' => true);
		if (!$options['inline'] && empty($options['block'])) {
			$options['block'] = __FUNCTION__;
		}
		unset($options['inline']);

		if (is_array($path)) {
			$out = '';
			foreach ($path as $i) {
				$out .= "\n\t" . $this->less($i, $rel, $options);
			}
			if (empty($options['block'])) {
				return $out . "\n";
			}
			return;
		}

		if (strpos($path, '//') !== false) {
			$url = $path;
		} else {
			$url = $this->assetUrl($path, $options + array('pathPrefix' => CSS_URL, 'ext' => '.less'));
			$options = array_diff_key($options, array('fullBase' => null));

			if (Configure::read('Asset.filter.css')) {
				$pos = strpos($url, CSS_URL);
				if ($pos !== false) {
					$url = substr($url, 0, $pos) . 'ccss/' . substr($url, $pos + strlen(CSS_URL));
				}
			}
		}

		if ($rel === 'import') {
			$out = sprintf($this->_tags['style'], $this->_parseAttributes($options, array('inline', 'block'), '', ' '), '@import url(' . $url . ');');
		} else {
			if (!$rel) {
				$rel = 'stylesheet';
			}
			$out = sprintf($this->_tags['css'], $rel, $url, $this->_parseAttributes($options, array('inline', 'block'), '', ' '));
		}

		if (empty($options['block'])) {
			return $out;
		} else {
			$this->_View->append($options['block'], $out);
		}
	}
	
	public function link($title = "", $url = false, $options = array(), $cfm = false){
		$escapeTitle = true;
		if ($url !== null) {
			$url = $this->url($url);
		} else {
			$url = $this->url($title);
			$title = htmlspecialchars_decode($url, ENT_QUOTES);
			$title = h(urldecode($title));
			$escapeTitle = false;
		}

		if (isset($options['escape'])) {
			$escapeTitle = $options['escape'];
		}

		if ($escapeTitle === true) {
			$title = h($title);
		} elseif (is_string($escapeTitle)) {
			$title = htmlentities($title, ENT_QUOTES, $escapeTitle);
		}

		if(isset($options['disabled']) && $options['disabled']){
			return sprintf($this->_myTags['link:disabled'], $this->_parseAttributes($options), $title);
		}
		return parent::link($title, $url, $options, $cfm);
	}

}