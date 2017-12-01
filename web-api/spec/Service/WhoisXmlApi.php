<?php
namespace Daytalytics\Spec\Service;
        
/**
 * @SWG\Tag(
 *   name="WhoisXmlApi",
 *   description="WhoisXmlApi"
 * )
 */
/**
 * @SWG\Get(
 *     path="/whois_xml_api",
 *     tags={"WhoisXmlApi"},
 *     summary="WhoisXmlApi",
 *     description="WhoisXmlApi",
 *     produces={"application/xml", "application/json", "text/html", "application/vnd.php-object"},
 *     @SWG\Parameter(
 *         name="domain",
 *         in="query",
 *         description="The domain to get whois data for, e.g. example.com",
 *         required=true,
 *         type="string",
 *         default="",
 *     ),
 *     @SWG\Parameter(
 *         name="outputFormat",
 *         in="query",
 *         description="Return whois results in xml, json or raw format [Options: 'xml', 'json', 'raw']",
 *         required=false,
 *         type="string",
 *         default="raw",
 *     ),

 *     @SWG\Response(response="200", description="An example resource"),
 *     @SWG\Response(response="204", description="An empty resource"),
 *     @SWG\Response(response="400", description="Request error"),
 *     @SWG\Response(response="401", description="Unauthorized error"),
 *     @SWG\Response(response="403", description="Unauthenticated error"),
 *     @SWG\Response(response="500", description="Server error")
 * )
 */

    
class WhoisXmlApi {

    public static $name = "WhoisXmlApi";
    
    public static $services = array (
		'default' => 
		array (
		  'name' => 'WhoisXmlApi',
		  'description' => '',
		  'method' => 'get',
		  'parameters' => 
		  array (
		    'domain' => 
		    array (
		      'description' => 'The domain to get whois data for, e.g. example.com',
		      'type' => 'string',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => true,
		    ),
		    'outputFormat' => 
		    array (
		      'description' => 'Return whois results in xml, json or raw format',
		      'type' => 'string',
		      'in' => 'query',
		      'default' => 'raw',
		      'required' => false,
		      'options' => 
		      array (
		        0 => 'xml',
		        1 => 'json',
		        2 => 'raw',
		      ),
		    ),
		  ),
		),
	);

}
