<?php
namespace Daytalytics\Response;

use Daytalytics\Xml as XmlFormatter;

class Xml implements ResponseInterface {
  
    public static function format(array $result = []) {
        return XmlFormatter::to_xml(['WebInformationApi' => $result], 10);
    }
}