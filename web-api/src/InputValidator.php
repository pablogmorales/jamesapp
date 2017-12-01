<?php
namespace Daytalytics;

class InputValidator {
    
    /**
     * Validates an input parameter value based on the
     * parameter definition.
     *
     * @param mixed $input
     * @param array $definition module parameter definition
     * @param array $options validator options
     * @return mixed
     */
    public static function validate($input, array $definition = []) {
        $options = @$definition['validatorOptions'] ?: [];
        //explicit validator
        if (isset($definition['validator'])) {
            $validator = $definition['validator'];
            if (is_callable($validator)) {
                return $validator($input, $definition, $options);
            }
            if (method_exists(get_called_class(), $validator)) {
                return static::$validator($input, $definition, $options);
            }
        }
        //required validation
        $requiredValid =  static::validateRequired($input, $definition, $options);
        if ($requiredValid !== true) {
            return $requiredValid;
        }
        //empty validation
        if (!is_null($input)) {
            $emptyValid =  static::validateEmpty($input, $definition, $options);
            if ($emptyValid !== true) {
                return $emptyValid;
            }
        }
        
        //format based format (http://swagger.io/specification/#dataTypeFormat)
        if (isset($definition['format'])) {
            $validator = 'validate' . ucfirst($definition['format']);
            if (method_exists(get_called_class(), $validator)) {
                return static::$validator($input, $definition, $options);
            }
        }
        //type based format (http://swagger.io/specification/#parameterObject)
        if (isset($definition['type'])) {
            $validator = 'validate' . ucfirst($definition['type']);
            if (method_exists(get_called_class(), $validator)) {
                return static::$validator($input, $definition, $options);
            }
        }
        return true;
    }
    
    public static function validateRequired($input, array $definition = [], array $options = []) {
        if (@$definition['required'] && is_null($input)) {
            return 'Field is required';
        }
        return true;
    }
    
    public static function validateEmpty($input, array $definition = [], array $options = []) {
        if (@$definition['allowEmptyValue'] !== true) {
            if (@$definition['type'] == 'array' && is_array($input)) {
                $valid = !empty($input);
            } else {
                $valid = trim($input) !== '';
            }
            return $valid ?: 'Field cannot be empty';
        }
    }
}