<?php
namespace Daytalytics\Spec\Service;
        
/**
 * @SWG\Tag(
 *   name="NortonWebsafe",
 *   description="NortonWebsafe"
 * )
 */
/**
 * @SWG\Get(
 *     path="/norton_websafe/safetyratingbydomain",
 *     tags={"NortonWebsafe"},
 *     summary="NortonWebsafe",
 *     description="NortonWebsafe",
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

    
class NortonWebsafe {

    public static $name = "NortonWebsafe";
    
    public static $services = array (
		'safetyratingbydomain' => 
		array (
		  'name' => 'safetyratingbydomain',
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
