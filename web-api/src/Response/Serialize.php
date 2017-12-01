<?php
namespace Daytalytics\Response;

class Serialize implements ResponseInterface {
    
    protected static $serializer = 'serialize';
    
    protected static $serializerArgs = [];
    
    public static function format(array $result = []) {
        $args = static::$serializerArgs;
        if (isset($args['result'])) {
            $args['result'] = $result;
        } else {
            array_unshift($args, $result);
        }            
        $args = array_values($args);
        return call_user_func_array(static::$serializer, $args);
    }
}