<?php
namespace Daytalytics\Spec\Service;
        
/**
 * @SWG\Tag(
 *   name="Search",
 *   description="Search"
 * )
 */
/**
 * @SWG\Get(
 *     path="/search/ns",
 *     tags={"Search"},
 *     summary="Search",
 *     description="Search",
 *     produces={"application/xml", "application/json", "text/html", "application/vnd.php-object"},
 *     @SWG\Parameter(
 *         name="engines",
 *         in="query",
 *         description="Search engine to query [Options: 'google', 'bing']",
 *         required=false,
 *         type="array",
 *         default="",
 * items={"type":"string"},
 * collectionFormat="csv",
 *     ),
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
/**
 * @SWG\Get(
 *     path="/search/pl",
 *     tags={"Search"},
 *     summary="Search",
 *     description="Search",
 *     produces={"application/xml", "application/json", "text/html", "application/vnd.php-object"},
 *     @SWG\Parameter(
 *         name="engines",
 *         in="query",
 *         description="Search engine to query [Options: 'google', 'bing']",
 *         required=false,
 *         type="array",
 *         default="",
 * items={"type":"string"},
 * collectionFormat="csv",
 *     ),
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
/**
 * @SWG\Get(
 *     path="/search/ps",
 *     tags={"Search"},
 *     summary="Search",
 *     description="Search",
 *     produces={"application/xml", "application/json", "text/html", "application/vnd.php-object"},
 *     @SWG\Parameter(
 *         name="engines",
 *         in="query",
 *         description="Search engine to query [Options: 'google', 'bing']",
 *         required=false,
 *         type="array",
 *         default="",
 * items={"type":"string"},
 * collectionFormat="csv",
 *     ),
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

    
class Search {

    public static $name = "Search";
    
    public static $services = array (
		'ns' => 
		array (
		  'name' => 'ns',
		  'description' => '',
		  'method' => 'get',
		  'parameters' => 
		  array (
		    'engines' => 
		    array (
		      'description' => 'Search engine to query',
		      'type' => 'array',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => false,
		      'collectionFormat' => 'csv',
		      'options' => 
		      array (
		        0 => 'google',
		        1 => 'bing',
		      ),
		      'items' => 
		      array (
		        'type' => 'string',
		      ),
		    ),
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
		'pl' => 
		array (
		  'name' => 'pl',
		  'description' => '',
		  'method' => 'get',
		  'parameters' => 
		  array (
		    'engines' => 
		    array (
		      'description' => 'Search engine to query',
		      'type' => 'array',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => false,
		      'collectionFormat' => 'csv',
		      'options' => 
		      array (
		        0 => 'google',
		        1 => 'bing',
		      ),
		      'items' => 
		      array (
		        'type' => 'string',
		      ),
		    ),
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
		'ps' => 
		array (
		  'name' => 'ps',
		  'description' => '',
		  'method' => 'get',
		  'parameters' => 
		  array (
		    'engines' => 
		    array (
		      'description' => 'Search engine to query',
		      'type' => 'array',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => false,
		      'collectionFormat' => 'csv',
		      'options' => 
		      array (
		        0 => 'google',
		        1 => 'bing',
		      ),
		      'items' => 
		      array (
		        'type' => 'string',
		      ),
		    ),
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
