<?php

namespace Daytalytics;

use DOMNode;
use SimpleXMLElement;
use RuntimeException;

/**
 * XML handling based from CakePHP.
 *
 */
class Xml {
	
	public static function to_xml($array, $depth, $parent_key = "") {
		$xml = '';
		if ($depth <= 0) {
			return '';
		}
		foreach($array as $key => $value) {
			if (is_numeric($key)) {
				if ($parent_key) {
					$key_htmlentities = htmlentities($parent_key . "_item", ENT_QUOTES, 'UTF-8');
				}
				else {
					$key_htmlentities = "array_item";
				}
			}
			else {
				$key_htmlentities = htmlentities($key);
			}
	
			if (is_numeric($value) || is_string($value)) {
				$xml .= "<$key_htmlentities>";
				$xml .= htmlentities($value, ENT_QUOTES, 'UTF-8');
				$xml .= "</$key_htmlentities>\n";
			}
			elseif (is_bool($value)) {
				$xml .= "<$key_htmlentities>";
				if ($value) {
					$xml .= "1";
				}
				else {
					$xml .= "0";
				}
				$xml .= "</$key_htmlentities>\n";
			}
			elseif (is_array($value)) {
				$xml .= "<$key_htmlentities>";
				if ($depth > 1) {
					$xml .= "\n";
					$xml .= static::tabify(self::to_xml($value, $depth-1, $key));
				}
				else {
					$xml .= "Array";
				}
				$xml .= "</$key_htmlentities>\n";
			}
			else {
				$xml .= "<$key_htmlentities failed />\n";
				continue;
			}
		}
		return $xml;
	}
	
	
	/**
	 * @name tabify()
	 * @usage string tabify(string $xml)
	 * @description indents every line of the xml by 1 tab
	 */
	protected static function tabify($xml) {
		$starting_newline = (strlen($xml) > 0 && $xml{0} == "\n" ? "\n" : "");
		$ending_newline = (strlen($xml) > 1 && $xml{strlen($xml)-1} == "\n" ? "\n" : "");
		$xml = trim($xml);
		$xml = "\t" . implode("\n\t", explode("\n", $xml));
		$xml = $starting_newline . $xml . $ending_newline;
		return $xml;
	}
	

	/**
	 * Returns this XML structure as an array.
	 *
	 * @param SimpleXMLElement|DOMDocument|DOMNode $obj SimpleXMLElement, DOMDocument or DOMNode instance
	 * @return array Array representation of the XML structure.
	 * @throws XmlException
	 */
	public static function toArray($obj) {
		if ($obj instanceof DOMNode) {
			$obj = simplexml_import_dom($obj);
		}
		if (!($obj instanceof SimpleXMLElement)) {
			throw new XmlException('The input is not instance of SimpleXMLElement, DOMDocument or DOMNode.');
		}
		$result = array();
		$namespaces = array_merge(array('' => ''), $obj->getNamespaces(true));
		self::_toArray($obj, $result, '', array_keys($namespaces));
		return $result;
	}

	/**
	 * Recursive method to toArray
	 *
	 * @param SimpleXMLElement $xml SimpleXMLElement object
	 * @param array &$parentData Parent array with data
	 * @param string $ns Namespace of current child
	 * @param array $namespaces List of namespaces in XML
	 * @return void
	 */
	protected static function _toArray($xml, &$parentData, $ns, $namespaces) {
		$data = array();

		foreach ($namespaces as $namespace) {
			foreach ($xml->attributes($namespace, true) as $key => $value) {
				if (!empty($namespace)) {
					$key = $namespace . ':' . $key;
				}
				$data['@' . $key] = (string)$value;
			}

			foreach ($xml->children($namespace, true) as $child) {
				self::_toArray($child, $data, $namespace, $namespaces);
			}
		}

		$asString = trim((string)$xml);
		if (empty($data)) {
			$data = $asString;
		} elseif (strlen($asString) > 0) {
			$data['@'] = $asString;
		}

		if (!empty($ns)) {
			$ns .= ':';
		}
		$name = $ns . $xml->getName();
		if (isset($parentData[$name])) {
			if (!is_array($parentData[$name]) || !isset($parentData[$name][0])) {
				$parentData[$name] = array($parentData[$name]);
			}
			$parentData[$name][] = $data;
		} else {
			$parentData[$name] = $data;
		}
	}

}


class XmlException extends RuntimeException {}


