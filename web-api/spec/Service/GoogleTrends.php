<?php
namespace Daytalytics\Spec\Service;
        
/**
 * @SWG\Tag(
 *   name="GoogleTrends",
 *   description="GoogleTrends"
 * )
 */
/**
 * @SWG\Get(
 *     path="/google_trends/keywordtrenddata",
 *     tags={"GoogleTrends"},
 *     summary="GoogleTrends",
 *     description="GoogleTrends",
 *     produces={"application/xml", "application/json", "text/html", "application/vnd.php-object"},
 *     @SWG\Parameter(
 *         name="keyword",
 *         in="query",
 *         description="Keyword search term",
 *         required=true,
 *         type="string",
 *         default="",
 *     ),
 *     @SWG\Parameter(
 *         name="start",
 *         in="query",
 *         description="Start time",
 *         required=false,
 *         type="string",
 *         default="",
 * format="dateTime",
 *     ),
 *     @SWG\Parameter(
 *         name="end",
 *         in="query",
 *         description="End time",
 *         required=false,
 *         type="string",
 *         default="",
 * format="dateTime",
 *     ),

 *     @SWG\Response(response="200", description="An example resource"),
 *     @SWG\Response(response="204", description="An empty resource"),
 *     @SWG\Response(response="400", description="Request error"),
 *     @SWG\Response(response="401", description="Unauthorized error"),
 *     @SWG\Response(response="403", description="Unauthenticated error"),
 *     @SWG\Response(response="500", description="Server error")
 * )
 */

    
class GoogleTrends {

    public static $name = "GoogleTrends";
    
    public static $services = array (
		'keywordtrenddata' => 
		array (
		  'name' => 'keywordtrenddata',
		  'description' => '',
		  'method' => 'get',
		  'parameters' => 
		  array (
		    'keyword' => 
		    array (
		      'description' => 'Keyword search term',
		      'type' => 'string',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => true,
		    ),
		    'start' => 
		    array (
		      'description' => 'Start time',
		      'type' => 'string',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => false,
		      'format' => 'dateTime',
		    ),
		    'end' => 
		    array (
		      'description' => 'End time',
		      'type' => 'string',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => false,
		      'format' => 'dateTime',
		    ),
		  ),
		),
	);

}
