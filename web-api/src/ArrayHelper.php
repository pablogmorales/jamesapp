<?php

namespace Daytalytics;


class ArrayHelper {

	/**
	 *
	 * @param array $set - an array. each item is another array of field name/value pairs.
	 * @param mixed fields - a keyword, or an array of field names to keep.
	 * @param array - an array of sets to satisfy keywords passed in $fields OR
	 *                an array field names to satisfy the 'defaults' set.
	 *
	 * $results = array(
	 * 	'fieldNameOne' => 'value one',
	 * 	'fieldNameTwo' => 'value two',
	 * 	'fieldNameThree' => 'value three',
	 * 	'fieldNameFour' => 'value four',
	 * );
	 * 
	 * filter_fields($results, 'defaults', array(
	 * 	'fieldNameOne',
	 * 	'fieldNameTwo',
	 * 	'fieldNameThree'
	 * )); // value one, value two, value three
	 *
	 * filter_fields($results, 'small', array(
	 * 	'small' => array('fieldNameOne, 'fieldNameFour'),
	 * )); // value one, value four
	 * 
	 * filter_fields($results, 'all', array(
	 * 	'small' => array('fieldNameOne, 'fieldNameFour'),
	 * )); // value one, value two, value three, value four
	 * 
	 * filter_fields($results, 'defaults', array(
	 * 	'small' => array('fieldNameOne, 'fieldNameFour'),
	 * )); // value one, value two, value three, value four
	 */
	public static function filter_fields($set, $fields, $fieldSets = null) {
		// If 1 field set is passed direct, call it 'defaults'.
		if (is_array($fieldSets) && !is_array(reset($fieldSets))) {
			$fieldSets = array('defaults' => $fieldSets);
		}
	
		if (!is_array($fields) && !array_key_exists($fields, $fieldSets)) {
			$fields = 'all';
		}
		elseif (!is_array($fields)) {
			$fields = $fieldSets[$fields];
		}
		
		if ($fields === 'all') {
			return $set;
		}
		
		foreach ($set as &$item) {
			$item = array_intersect_key($item, array_fill_keys($fields, null));
		}
		unset($item);
				
		return $set;
	}
}