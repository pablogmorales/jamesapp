<?php
namespace Daytalytics;

/**
 * InputFormatter
 * 
 * Applies normalization formatting to module input parameters
 *
 */
class InputFormatter {
    
    /**
     * Format/normalize an input parameter value based on the
     * parameter definition.
     *
     * @param mixed $input
     * @param array $definition module parameter definition
     * @param array $options formatter options
     * @return mixed
     */
    public static function format($input, array $definition = []) {
        $options = @$definition['formatterOptions'] ?: [];
        //explicit formatter
        if (isset($definition['formatter'])) {
            $formatter = $definition['formatter'];
            if (is_callable($formatter)) {
                return $formatter($input, $definition, $options);
            }
            if (method_exists(get_called_class(), $formatter)) {
                return static::$formatter($input, $definition, $options);
            }
        }
        //format based format (http://swagger.io/specification/#dataTypeFormat)
        if (isset($definition['format'])) {
            $formatter = 'format' . ucfirst($definition['format']);
            if (method_exists(get_called_class(), $formatter)) {
                return static::$formatter($input, $definition, $options);
            }
        }
        //type based format (http://swagger.io/specification/#parameterObject)
        if (isset($definition['type']) && is_string($definition['type'])) {
            $formatter = 'format' . ucfirst($definition['type']);
            if (method_exists(get_called_class(), $formatter)) {
                return static::$formatter($input, $definition, $options);
            }
        }
        return $input;
    }
    
    /**
     * Transforms array type inputs into actual arrays
     * 
     * @param mixed $input
     * @param array $definition module parameter definition
     * @param array $options formatter options
     * @return mixed
     */
    public static function formatArray($input, array $definition = [], array $options = []) {
        $options += ['filter' => [''], 'items' => true];
        if (!is_array($input)) {
            $collectionFormat = @$definition['collectionFormat'];
            switch ($collectionFormat) {
                case 'ssv':
                    $input = explode(' ', $input);
                    break;
                case 'tsv':
                    $input = explode("\t", $input);
                    break;
                case 'pipes':
                    $input = explode('|', $input);
                    break;
                case 'multi': 
                case 'csv':
                default:
                    $input = explode(',', $input);
                    break;
            }
        }
        if (!empty($options['filter'])) {
            $input = static::arrayFilter($input, $options['filter']);
        }
        if (!empty($options['items']) && !empty($definition['items'])) {
            $input = static::format($input, $definition['items']);
        }
        return $input;
    }
    
    public static function arrayFilter(array $array, $filter) {
        if (is_string($filter)) {
            $filter = [$filter];
        }
        return array_filter($array, function($value) use ($filter) {
              if (is_array($filter)) {
                  return !in_array($value, $filter);
              }
              return $value != $filter;
        });
    }
}