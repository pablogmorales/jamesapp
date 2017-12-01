<?php
namespace Daytalytics\Spec\Service;
        
/**
 * @SWG\Tag(
 *   name="GoogleSearch",
 *   description="GoogleSearch"
 * )
 */
/**
 * @SWG\Get(
 *     path="/google_search/ns",
 *     tags={"GoogleSearch"},
 *     summary="GoogleSearch",
 *     description="GoogleSearch",
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
 *         name="limit",
 *         in="query",
 *         description="Maximum number of search results",
 *         required=false,
 *         type="number",
 *         default=10,
 * format="integer",
 * maximum=200,
 * minimum=1,
 *     ),
 *     @SWG\Parameter(
 *         name="start",
 *         in="query",
 *         description="Start position of results",
 *         required=false,
 *         type="number",
 *         default=0,
 * format="integer",
 *     ),
 *     @SWG\Parameter(
 *         name="loc",
 *         in="query",
 *         description="Search location [Options: 'us' (USA - United States), 'be' (Belgium), 'da' (Denmark), 'de' (Germany), 'au' (Australia), 'ca' (Canada), 'uk' (United Kingdom), 'ie' (Ireland), 'in' (India), 'nz' (New Zealand), 'za' (South Africa), 'es' (Spain), 'mx' (Mexico), 'fi' (Finland), 'fr' (France), 'jp' (Japan), 'pl' (Poland), 'br' (Brazil), 'si' (Slovenia), 'se' (Sweden), 'cn' (China)]",
 *         required=false,
 *         type="string",
 *         default="us",
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
 *     path="/google_search/pl",
 *     tags={"GoogleSearch"},
 *     summary="GoogleSearch",
 *     description="GoogleSearch",
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
 *         name="limit",
 *         in="query",
 *         description="Maximum number of search results",
 *         required=false,
 *         type="number",
 *         default=10,
 * format="integer",
 * maximum=200,
 * minimum=1,
 *     ),
 *     @SWG\Parameter(
 *         name="start",
 *         in="query",
 *         description="Start position of results",
 *         required=false,
 *         type="number",
 *         default=0,
 * format="integer",
 *     ),
 *     @SWG\Parameter(
 *         name="loc",
 *         in="query",
 *         description="Search location [Options: 'us' (USA - United States), 'be' (Belgium), 'da' (Denmark), 'de' (Germany), 'au' (Australia), 'ca' (Canada), 'uk' (United Kingdom), 'ie' (Ireland), 'in' (India), 'nz' (New Zealand), 'za' (South Africa), 'es' (Spain), 'mx' (Mexico), 'fi' (Finland), 'fr' (France), 'jp' (Japan), 'pl' (Poland), 'br' (Brazil), 'si' (Slovenia), 'se' (Sweden), 'cn' (China)]",
 *         required=false,
 *         type="string",
 *         default="us",
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
 *     path="/google_search/news",
 *     tags={"GoogleSearch"},
 *     summary="GoogleSearch",
 *     description="GoogleSearch",
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
 *         name="limit",
 *         in="query",
 *         description="Maximum number of search results",
 *         required=false,
 *         type="number",
 *         default=10,
 * format="integer",
 * maximum=200,
 * minimum=1,
 *     ),
 *     @SWG\Parameter(
 *         name="start",
 *         in="query",
 *         description="Start position of results",
 *         required=false,
 *         type="number",
 *         default=0,
 * format="integer",
 *     ),
 *     @SWG\Parameter(
 *         name="loc",
 *         in="query",
 *         description="Search location [Options: 'us' (USA - United States), 'be' (Belgium), 'da' (Denmark), 'de' (Germany), 'au' (Australia), 'ca' (Canada), 'uk' (United Kingdom), 'ie' (Ireland), 'in' (India), 'nz' (New Zealand), 'za' (South Africa), 'es' (Spain), 'mx' (Mexico), 'fi' (Finland), 'fr' (France), 'jp' (Japan), 'pl' (Poland), 'br' (Brazil), 'si' (Slovenia), 'se' (Sweden), 'cn' (China)]",
 *         required=false,
 *         type="string",
 *         default="us",
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
 *     path="/google_search/ps",
 *     tags={"GoogleSearch"},
 *     summary="GoogleSearch",
 *     description="GoogleSearch",
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
 *         name="limit",
 *         in="query",
 *         description="Maximum number of search results",
 *         required=false,
 *         type="number",
 *         default=10,
 * format="integer",
 * maximum=200,
 * minimum=1,
 *     ),

 *     @SWG\Response(response="200", description="An example resource"),
 *     @SWG\Response(response="204", description="An empty resource"),
 *     @SWG\Response(response="400", description="Request error"),
 *     @SWG\Response(response="401", description="Unauthorized error"),
 *     @SWG\Response(response="403", description="Unauthenticated error"),
 *     @SWG\Response(response="500", description="Server error")
 * )
 */

    
class GoogleSearch {

    public static $name = "GoogleSearch";
    
    public static $services = array (
		'ns' => 
		array (
		  'name' => 'ns',
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
		    'limit' => 
		    array (
		      'description' => 'Maximum number of search results',
		      'type' => 'number',
		      'in' => 'query',
		      'default' => 10,
		      'required' => false,
		      'minimum' => 1,
		      'maximum' => 200,
		      'format' => 'integer',
		    ),
		    'start' => 
		    array (
		      'description' => 'Start position of results',
		      'type' => 'number',
		      'in' => 'query',
		      'default' => 0,
		      'required' => false,
		      'format' => 'integer',
		    ),
		    'loc' => 
		    array (
		      'description' => 'Search location',
		      'type' => 'string',
		      'in' => 'query',
		      'default' => 'us',
		      'required' => false,
		      'options' => 
		      array (
		        'us' => 'USA - United States',
		        'be' => 'Belgium',
		        'da' => 'Denmark',
		        'de' => 'Germany',
		        'au' => 'Australia',
		        'ca' => 'Canada',
		        'uk' => 'United Kingdom',
		        'ie' => 'Ireland',
		        'in' => 'India',
		        'nz' => 'New Zealand',
		        'za' => 'South Africa',
		        'es' => 'Spain',
		        'mx' => 'Mexico',
		        'fi' => 'Finland',
		        'fr' => 'France',
		        'jp' => 'Japan',
		        'pl' => 'Poland',
		        'br' => 'Brazil',
		        'si' => 'Slovenia',
		        'se' => 'Sweden',
		        'cn' => 'China',
		      ),
		    ),
		  ),
		),
		'pl' => 
		array (
		  'name' => 'pl',
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
		    'limit' => 
		    array (
		      'description' => 'Maximum number of search results',
		      'type' => 'number',
		      'in' => 'query',
		      'default' => 10,
		      'required' => false,
		      'minimum' => 1,
		      'maximum' => 200,
		      'format' => 'integer',
		    ),
		    'start' => 
		    array (
		      'description' => 'Start position of results',
		      'type' => 'number',
		      'in' => 'query',
		      'default' => 0,
		      'required' => false,
		      'format' => 'integer',
		    ),
		    'loc' => 
		    array (
		      'description' => 'Search location',
		      'type' => 'string',
		      'in' => 'query',
		      'default' => 'us',
		      'required' => false,
		      'options' => 
		      array (
		        'us' => 'USA - United States',
		        'be' => 'Belgium',
		        'da' => 'Denmark',
		        'de' => 'Germany',
		        'au' => 'Australia',
		        'ca' => 'Canada',
		        'uk' => 'United Kingdom',
		        'ie' => 'Ireland',
		        'in' => 'India',
		        'nz' => 'New Zealand',
		        'za' => 'South Africa',
		        'es' => 'Spain',
		        'mx' => 'Mexico',
		        'fi' => 'Finland',
		        'fr' => 'France',
		        'jp' => 'Japan',
		        'pl' => 'Poland',
		        'br' => 'Brazil',
		        'si' => 'Slovenia',
		        'se' => 'Sweden',
		        'cn' => 'China',
		      ),
		    ),
		  ),
		),
		'news' => 
		array (
		  'name' => 'news',
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
		    'limit' => 
		    array (
		      'description' => 'Maximum number of search results',
		      'type' => 'number',
		      'in' => 'query',
		      'default' => 10,
		      'required' => false,
		      'minimum' => 1,
		      'maximum' => 200,
		      'format' => 'integer',
		    ),
		    'start' => 
		    array (
		      'description' => 'Start position of results',
		      'type' => 'number',
		      'in' => 'query',
		      'default' => 0,
		      'required' => false,
		      'format' => 'integer',
		    ),
		    'loc' => 
		    array (
		      'description' => 'Search location',
		      'type' => 'string',
		      'in' => 'query',
		      'default' => 'us',
		      'required' => false,
		      'options' => 
		      array (
		        'us' => 'USA - United States',
		        'be' => 'Belgium',
		        'da' => 'Denmark',
		        'de' => 'Germany',
		        'au' => 'Australia',
		        'ca' => 'Canada',
		        'uk' => 'United Kingdom',
		        'ie' => 'Ireland',
		        'in' => 'India',
		        'nz' => 'New Zealand',
		        'za' => 'South Africa',
		        'es' => 'Spain',
		        'mx' => 'Mexico',
		        'fi' => 'Finland',
		        'fr' => 'France',
		        'jp' => 'Japan',
		        'pl' => 'Poland',
		        'br' => 'Brazil',
		        'si' => 'Slovenia',
		        'se' => 'Sweden',
		        'cn' => 'China',
		      ),
		    ),
		  ),
		),
		'ps' => 
		array (
		  'name' => 'ps',
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
		    'limit' => 
		    array (
		      'description' => 'Maximum number of search results',
		      'type' => 'number',
		      'in' => 'query',
		      'default' => 10,
		      'required' => false,
		      'minimum' => 1,
		      'maximum' => 200,
		      'format' => 'integer',
		    ),
		  ),
		),
	);

}
