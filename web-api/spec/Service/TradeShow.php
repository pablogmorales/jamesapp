<?php
namespace Daytalytics\Spec\Service;
        
/**
 * @SWG\Tag(
 *   name="TradeShow",
 *   description="TradeShow"
 * )
 */
/**
 * @SWG\Get(
 *     path="/trade_show/countries",
 *     tags={"TradeShow"},
 *     summary="TradeShow",
 *     description="TradeShow",
 *     produces={"application/xml", "application/json", "text/html", "application/vnd.php-object"},

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
 *     path="/trade_show/eventssummarybycountry",
 *     tags={"TradeShow"},
 *     summary="TradeShow",
 *     description="TradeShow",
 *     produces={"application/xml", "application/json", "text/html", "application/vnd.php-object"},
 *     @SWG\Parameter(
 *         name="countryCode",
 *         in="query",
 *         description="Country",
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
 *     path="/trade_show/eventlistbycountryandcity",
 *     tags={"TradeShow"},
 *     summary="TradeShow",
 *     description="TradeShow",
 *     produces={"application/xml", "application/json", "text/html", "application/vnd.php-object"},
 *     @SWG\Parameter(
 *         name="countryCode",
 *         in="query",
 *         description="Country code",
 *         required=true,
 *         type="string",
 *         default="",
 *     ),
 *     @SWG\Parameter(
 *         name="city",
 *         in="query",
 *         description="City name",
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

    
class TradeShow {

    public static $name = "TradeShow";
    
    public static $services = array (
		'countries' => 
		array (
		  'name' => 'countries',
		  'description' => '',
		  'method' => 'get',
		  'parameters' => 
		  array (
		  ),
		),
		'eventssummarybycountry' => 
		array (
		  'name' => 'eventssummarybycountry',
		  'description' => '',
		  'method' => 'get',
		  'parameters' => 
		  array (
		    'countryCode' => 
		    array (
		      'description' => 'Country',
		      'type' => 'string',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => true,
		    ),
		  ),
		),
		'eventlistbycountryandcity' => 
		array (
		  'name' => 'eventlistbycountryandcity',
		  'description' => '',
		  'method' => 'get',
		  'parameters' => 
		  array (
		    'countryCode' => 
		    array (
		      'description' => 'Country code',
		      'type' => 'string',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => true,
		    ),
		    'city' => 
		    array (
		      'description' => 'City name',
		      'type' => 'string',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => true,
		    ),
		  ),
		),
	);

}
