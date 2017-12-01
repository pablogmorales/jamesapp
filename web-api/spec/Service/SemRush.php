<?php
namespace Daytalytics\Spec\Service;
        
/**
 * @SWG\Tag(
 *   name="SemRush",
 *   description="SemRush"
 * )
 */
/**
 * @SWG\Get(
 *     path="/sem_rush/keyword_report",
 *     tags={"SemRush"},
 *     summary="SemRush",
 *     description="SemRush",
 *     produces={"application/xml", "application/json", "text/html", "application/vnd.php-object"},
 *     @SWG\Parameter(
 *         name="database",
 *         in="query",
 *         description="Keyword database to use [Options: 'us', 'uk', 'ru', 'de', 'fr', 'es', 'it', 'br', 'ca', 'au']",
 *         required=true,
 *         type="string",
 *         default="",
 *     ),
 *     @SWG\Parameter(
 *         name="keyword",
 *         in="query",
 *         description="Keyword search term",
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
 *     path="/sem_rush/related_keywords",
 *     tags={"SemRush"},
 *     summary="SemRush",
 *     description="SemRush",
 *     produces={"application/xml", "application/json", "text/html", "application/vnd.php-object"},
 *     @SWG\Parameter(
 *         name="database",
 *         in="query",
 *         description="Keyword database to use [Options: 'us', 'uk', 'ru', 'de', 'fr', 'es', 'it', 'br', 'ca', 'au']",
 *         required=true,
 *         type="string",
 *         default="",
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
 *         name="offset",
 *         in="query",
 *         description="Position to seek to in result list",
 *         required=false,
 *         type="number",
 *         default=0,
 *     ),
 *     @SWG\Parameter(
 *         name="limit",
 *         in="query",
 *         description="Number of results to return",
 *         required=false,
 *         type="number",
 *         default=10,
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
 *     path="/sem_rush/phrase_fullsearch",
 *     tags={"SemRush"},
 *     summary="SemRush",
 *     description="SemRush",
 *     produces={"application/xml", "application/json", "text/html", "application/vnd.php-object"},
 *     @SWG\Parameter(
 *         name="database",
 *         in="query",
 *         description="Keyword database to use [Options: 'us', 'uk', 'ru', 'de', 'fr', 'es', 'it', 'br', 'ca', 'au']",
 *         required=true,
 *         type="string",
 *         default="",
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
 *         name="offset",
 *         in="query",
 *         description="Position to seek to in result list",
 *         required=false,
 *         type="number",
 *         default=0,
 *     ),
 *     @SWG\Parameter(
 *         name="limit",
 *         in="query",
 *         description="Number of results to return",
 *         required=false,
 *         type="number",
 *         default=10,
 *     ),

 *     @SWG\Response(response="200", description="An example resource"),
 *     @SWG\Response(response="204", description="An empty resource"),
 *     @SWG\Response(response="400", description="Request error"),
 *     @SWG\Response(response="401", description="Unauthorized error"),
 *     @SWG\Response(response="403", description="Unauthenticated error"),
 *     @SWG\Response(response="500", description="Server error")
 * )
 */

    
class SemRush {

    public static $name = "SemRush";
    
    public static $services = array (
		'keyword_report' => 
		array (
		  'name' => 'keyword_report',
		  'description' => '',
		  'method' => 'get',
		  'parameters' => 
		  array (
		    'database' => 
		    array (
		      'description' => 'Keyword database to use',
		      'type' => 'string',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => true,
		      'options' => 
		      array (
		        0 => 'us',
		        1 => 'uk',
		        2 => 'ru',
		        3 => 'de',
		        4 => 'fr',
		        5 => 'es',
		        6 => 'it',
		        7 => 'br',
		        8 => 'ca',
		        9 => 'au',
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
		  ),
		),
		'related_keywords' => 
		array (
		  'name' => 'related_keywords',
		  'description' => '',
		  'method' => 'get',
		  'parameters' => 
		  array (
		    'database' => 
		    array (
		      'description' => 'Keyword database to use',
		      'type' => 'string',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => true,
		      'options' => 
		      array (
		        0 => 'us',
		        1 => 'uk',
		        2 => 'ru',
		        3 => 'de',
		        4 => 'fr',
		        5 => 'es',
		        6 => 'it',
		        7 => 'br',
		        8 => 'ca',
		        9 => 'au',
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
		    'offset' => 
		    array (
		      'description' => 'Position to seek to in result list',
		      'type' => 'number',
		      'in' => 'query',
		      'default' => 0,
		      'required' => false,
		    ),
		    'limit' => 
		    array (
		      'description' => 'Number of results to return',
		      'type' => 'number',
		      'in' => 'query',
		      'default' => 10,
		      'required' => false,
		    ),
		  ),
		),
		'phrase_fullsearch' => 
		array (
		  'name' => 'phrase_fullsearch',
		  'description' => '',
		  'method' => 'get',
		  'parameters' => 
		  array (
		    'database' => 
		    array (
		      'description' => 'Keyword database to use',
		      'type' => 'string',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => true,
		      'options' => 
		      array (
		        0 => 'us',
		        1 => 'uk',
		        2 => 'ru',
		        3 => 'de',
		        4 => 'fr',
		        5 => 'es',
		        6 => 'it',
		        7 => 'br',
		        8 => 'ca',
		        9 => 'au',
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
		    'offset' => 
		    array (
		      'description' => 'Position to seek to in result list',
		      'type' => 'number',
		      'in' => 'query',
		      'default' => 0,
		      'required' => false,
		    ),
		    'limit' => 
		    array (
		      'description' => 'Number of results to return',
		      'type' => 'number',
		      'in' => 'query',
		      'default' => 10,
		      'required' => false,
		    ),
		  ),
		),
	);

}
