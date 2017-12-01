<?php
namespace Daytalytics\Spec\Service;
        
/**
 * @SWG\Tag(
 *   name="AlexaTopSites",
 *   description="AlexaTopSites"
 * )
 */
/**
 * @SWG\Get(
 *     path="/alexa_top_sites",
 *     tags={"AlexaTopSites"},
 *     summary="AlexaTopSites",
 *     description="AlexaTopSites",
 *     produces={"application/xml", "application/json", "text/html", "application/vnd.php-object"},
 *     @SWG\Parameter(
 *         name="countryCode",
 *         in="query",
 *         description="",
 *         required=true,
 *         type="string",
 *         default="",
 *     ),
 *     @SWG\Parameter(
 *         name="numReturn",
 *         in="query",
 *         description="",
 *         required=false,
 *         type="number",
 *         default=100,
 * format="integer",
 *     ),
 *     @SWG\Parameter(
 *         name="startNum",
 *         in="query",
 *         description="",
 *         required=false,
 *         type="number",
 *         default=1,
 * format="integer",
 *     ),

 *     @SWG\Response(response="200", description="An example resource"),
 *     @SWG\Response(response="204", description="An empty resource"),
 *     @SWG\Response(response="400", description="Request error"),
 *     @SWG\Response(response="401", description="Unauthorized error"),
 *     @SWG\Response(response="403", description="Unauthenticated error"),
 *     @SWG\Response(response="500", description="Server error")
 * )
 */

    
class AlexaTopSites {

    public static $name = "AlexaTopSites";
    
    public static $services = array (
		'default' => 
		array (
		  'name' => 'AlexaTopSites',
		  'description' => '',
		  'method' => 'get',
		  'parameters' => 
		  array (
		    'countryCode' => 
		    array (
		      'description' => '',
		      'type' => 'string',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => true,
		    ),
		    'numReturn' => 
		    array (
		      'description' => '',
		      'type' => 'number',
		      'in' => 'query',
		      'default' => 100,
		      'required' => false,
		      'format' => 'integer',
		    ),
		    'startNum' => 
		    array (
		      'description' => '',
		      'type' => 'number',
		      'in' => 'query',
		      'default' => 1,
		      'required' => false,
		      'format' => 'integer',
		    ),
		  ),
		),
	);

}
