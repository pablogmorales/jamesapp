<?php
namespace Daytalytics\Response;

class Json extends Serialize {
    
    protected static $serializer = 'json_encode';
}