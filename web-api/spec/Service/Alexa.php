<?php
namespace Daytalytics\Spec\Service;
        
/**
 * @SWG\Tag(
 *   name="Alexa",
 *   description="Alexa"
 * )
 */
/**
 * @SWG\Get(
 *     path="/alexa/rank",
 *     tags={"Alexa"},
 *     summary="Alexa",
 *     description="Alexa",
 *     produces={"application/xml", "application/json", "text/html", "application/vnd.php-object"},
 *     @SWG\Parameter(
 *         name="url",
 *         in="query",
 *         description="Website url",
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
/**
 * @SWG\Get(
 *     path="/alexa/backlinks",
 *     tags={"Alexa"},
 *     summary="Alexa",
 *     description="Alexa",
 *     produces={"application/xml", "application/json", "text/html", "application/vnd.php-object"},
 *     @SWG\Parameter(
 *         name="url",
 *         in="query",
 *         description="Website url",
 *         required=true,
 *         type="string",
 *         default="",
 *     ),
 *     @SWG\Parameter(
 *         name="startfrom",
 *         in="query",
 *         description="Return backlinks from this number",
 *         required=false,
 *         type="number",
 *         default=0,
 *     ),

 *     @SWG\Response(response="200", description="An example resource"),
 *     @SWG\Response(response="204", description="An empty resource"),
 *     @SWG\Response(response="400", description="Request error"),
 *     @SWG\Response(response="401", description="Unauthorized error"),
 *     @SWG\Response(response="403", description="Unauthenticated error"),
 *     @SWG\Response(response="500", description="Server error")
 * )
 */

    
class Alexa {

    public static $name = "Alexa";
    
    public static $services = array (
		'rank' => 
		array (
		  'name' => 'rank',
		  'description' => '',
		  'method' => 'get',
		  'parameters' => 
		  array (
		    'url' => 
		    array (
		      'description' => 'Website url',
		      'type' => 'string',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => true,
		    ),
		  ),
		),
		'backlinks' => 
		array (
		  'name' => 'backlinks',
		  'description' => '',
		  'method' => 'get',
		  'parameters' => 
		  array (
		    'url' => 
		    array (
		      'description' => 'Website url',
		      'type' => 'string',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => true,
		    ),
		    'startfrom' => 
		    array (
		      'description' => 'Return backlinks from this number',
		      'type' => 'number',
		      'in' => 'query',
		      'default' => 0,
		      'required' => false,
		    ),
		  ),
		),
	);

}
