<?php
namespace Daytalytics\Spec\Service;
        
/**
 * @SWG\Tag(
 *   name="Bbb",
 *   description="Bbb"
 * )
 */
/**
 * @SWG\Get(
 *     path="/bbb/organizationalsearchbydomain",
 *     tags={"Bbb"},
 *     summary="Bbb",
 *     description="Bbb",
 *     produces={"application/xml", "application/json", "text/html", "application/vnd.php-object"},
 *     @SWG\Parameter(
 *         name="domain",
 *         in="query",
 *         description="Domain name e.g. example-domain.com",
 *         required=true,
 *         type="string",
 *         default="",
 *     ),

 *     @SWG\Response(response="200", description="An example resource"),
 *     @SWG\Response(response="204", description="An empty resource"),
 *     @SWG\Response(response="400", description="Request error"),
 *     @SWG\Response(response="401", description="Unauthorized error"),
 *     @SWG\Response(response="403", description="Unauthenticated error"),
 *     @SWG\Response(response="500", description="Server error")
 * )
 */

    
class Bbb {

    public static $name = "Bbb";
    
    public static $services = array (
		'organizationalsearchbydomain' => 
		array (
		  'name' => 'organizationalsearchbydomain',
		  'description' => '',
		  'method' => 'get',
		  'parameters' => 
		  array (
		    'domain' => 
		    array (
		      'description' => 'Domain name e.g. example-domain.com',
		      'type' => 'string',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => true,
		    ),
		  ),
		),
	);

}
