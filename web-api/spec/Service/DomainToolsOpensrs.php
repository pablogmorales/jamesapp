<?php
namespace Daytalytics\Spec\Service;
        
/**
 * @SWG\Tag(
 *   name="DomainToolsOpensrs",
 *   description="DomainToolsOpensrs"
 * )
 */
/**
 * @SWG\Get(
 *     path="/domain_tools_opensrs/check",
 *     tags={"DomainToolsOpensrs"},
 *     summary="DomainToolsOpensrs",
 *     description="DomainToolsOpensrs",
 *     produces={"application/xml", "application/json", "text/html", "application/vnd.php-object"},
 *     @SWG\Parameter(
 *         name="domain",
 *         in="query",
 *         description="Domain to check, exclude tld",
 *         required=true,
 *         type="string",
 *         default="",
 *     ),
 *     @SWG\Parameter(
 *         name="tld",
 *         in="query",
 *         description="tld to check. true means taken.",
 *         required=true,
 *         type="array",
 *         default="",
 * items={"type":"string"},
 * minItems=1,
 * maxItems=50,
 * collectionFormat="multi",
 *     ),

 *     @SWG\Response(response="200", description="An example resource"),
 *     @SWG\Response(response="204", description="An empty resource"),
 *     @SWG\Response(response="400", description="Request error"),
 *     @SWG\Response(response="401", description="Unauthorized error"),
 *     @SWG\Response(response="403", description="Unauthenticated error"),
 *     @SWG\Response(response="500", description="Server error")
 * )
 */

    
class DomainToolsOpensrs {

    public static $name = "DomainToolsOpensrs";
    
    public static $services = array (
		'check' => 
		array (
		  'name' => 'check',
		  'description' => '',
		  'method' => 'get',
		  'parameters' => 
		  array (
		    'domain' => 
		    array (
		      'description' => 'Domain to check, exclude tld',
		      'type' => 'string',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => true,
		    ),
		    'tld' => 
		    array (
		      'description' => 'tld to check. true means taken.',
		      'type' => 'array',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => true,
		      'collectionFormat' => 'multi',
		      'maxItems' => 50,
		      'minItems' => 1,
		      'items' => 
		      array (
		        'type' => 'string',
		      ),
		    ),
		  ),
		),
	);

}
