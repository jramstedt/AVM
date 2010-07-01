<?php
class Util {
	static function objToXml(stdClass $obj, DOMElement &$target, $tagName = null) {
		$classDoc = $target->ownerDocument;

		$classNode = $classDoc->createElement(empty($tagName)?get_class($obj):$tagName);
		$classDoc->appendChild($classNode);

		foreach($obj as $sKey => $sValue) {
			if(is_object($sValue)) {
				Util::objToXml($sValue, $classNode);
			} else {
				if(is_array($sValue))
				$sValue = implode(',', $sValue);

				$tmpNode = $classDoc->createElement(Util::xmlTagEncode($sKey), $sValue);
				$classNode->appendChild($tmpNode);
			}
		}

		$target->appendChild($classNode);

		return $classNode;
	}

	static function valueToXml($name, $value, DOMElement &$target) {
		$ownerDoc = $target->ownerDocument;

		$tmpNode = $ownerDoc->createElement(Util::xmlTagEncode($name), $value);
		$target->appendChild($tmpNode);

		return $tmpNode;
	}
	
	static function tagToXml($name, DOMElement &$target) {
		$ownerDoc = $target->ownerDocument;
		
		$tmpNode = $ownerDoc->createElement(Util::xmlTagEncode($name));
		$target->appendChild($tmpNode);
		
		return $tmpNode;
	}
	
	static function rootTagToXml($name, DOMDocument &$target) {
		$tmpNode = $target->createElement(Util::xmlTagEncode($name));
		$target->appendChild($tmpNode);
		
		return $tmpNode;
	}

	static function xmlTagEncode($tagName) {
		$tagName = rawurlencode($tagName);

		$illegal = array('%', '-', '.');
		$ok = array('Percent', 'Line', 'Dot');
		$tagName = str_replace($illegal, $ok, $tagName);

		if(is_numeric($tagName{0})) {
			$tagName = '#'.$tagName;
		}

		return $tagName;
	}

	static function xmlTagDecode($tagName) {
		if($tagName{0} == '#')
		$tagName = mb_substr($tagName,1);

		$illegal = array('%', '-', '.');
		$ok = array('Percent', 'Line', 'Dot');
		$tagName = str_replace($ok, $illegal, $tagName);

		$tagName = rawurldecode($txt);

		return $tagName;
	}
	
	static function isAjaxRequest() {
		return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
	}
}
?>