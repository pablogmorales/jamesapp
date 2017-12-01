<?php
namespace Daytalytics\Response;

class Html extends Serialize {
    
    protected static $serializer = 'print_r';
    
    protected static $serializerArgs = [true];
  
}