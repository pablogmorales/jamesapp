<?php
namespace Daytalytics\Spec\Service;
        
/**
 * @SWG\Tag(
 *   name="Wot",
 *   description="Wot"
 * )
 */
/**
 * @SWG\Get(
 *     path="/wot/domaintrustworthinessmetric",
 *     tags={"Wot"},
 *     summary="Wot",
 *     description="Wot",
 *     produces={"application/xml", "application/json", "text/html", "application/vnd.php-object"},
 *     @SWG\Parameter(
 *         name="domain",
 *         in="query",
 *         description="Domain to check e.g. example.com",
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

    
class Wot {

    public static $name = "Wot";
    
    public static $services = array (
		'domaintrustworthinessmetric' => 
		array (
		  'name' => 'domaintrustworthinessmetric',
		  'description' => '',
		  'method' => 'get',
		  'parameters' => 
		  array (
		    'domain' => 
		    array (
		      'description' => 'Domain to check e.g. example.com',
		      'type' => 'string',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => true,
		    ),
		  ),
		),
	);

}
