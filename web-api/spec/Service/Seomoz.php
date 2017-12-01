<?php
namespace Daytalytics\Spec\Service;
        
/**
 * @SWG\Tag(
 *   name="Seomoz",
 *   description="Seomoz"
 * )
 */
/**
 * @SWG\Get(
 *     path="/seomoz/url-metrics",
 *     tags={"Seomoz"},
 *     summary="Seomoz",
 *     description="Seomoz",
 *     produces={"application/xml", "application/json", "text/html", "application/vnd.php-object"},
 *     @SWG\Parameter(
 *         name="url",
 *         in="query",
 *         description="The subject url",
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
 *     path="/seomoz/link-data",
 *     tags={"Seomoz"},
 *     summary="Seomoz",
 *     description="Seomoz",
 *     produces={"application/xml", "application/json", "text/html", "application/vnd.php-object"},
 *     @SWG\Parameter(
 *         name="url",
 *         in="query",
 *         description="The subject url",
 *         required=true,
 *         type="string",
 *         default="",
 *     ),
 *     @SWG\Parameter(
 *         name="scope",
 *         in="query",
 *         description="Scope [Options: 'page_to_page', 'page_to_subdomain', 'page_to_domain', 'domain_to_page', 'domain_to_subdomain', 'domain_to_domain']",
 *         required=false,
 *         type="string",
 *         default="",
 *     ),
 *     @SWG\Parameter(
 *         name="sort",
 *         in="query",
 *         description="Sort [Options: 'page_authority', 'domain_authority', 'domains_linking_domain', 'domains_linking_page']",
 *         required=false,
 *         type="string",
 *         default="",
 *     ),
 *     @SWG\Parameter(
 *         name="filter",
 *         in="query",
 *         description="Filters [Options: 'internal', 'external', 'nofollow', 'follow', '301']",
 *         required=false,
 *         type="array",
 *         default="",
 * items={"type":"string"},
 * collectionFormat="csv",
 *     ),
 *     @SWG\Parameter(
 *         name="limit",
 *         in="query",
 *         description="Limit",
 *         required=false,
 *         type="number",
 *         default=50,
 * format="integer",
 * maximum=1000,
 * minimum=1,
 *     ),
 *     @SWG\Parameter(
 *         name="offset",
 *         in="query",
 *         description="Offset",
 *         required=false,
 *         type="number",
 *         default=0,
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
/**
 * @SWG\Get(
 *     path="/seomoz/anchor-text-data",
 *     tags={"Seomoz"},
 *     summary="Seomoz",
 *     description="Seomoz",
 *     produces={"application/xml", "application/json", "text/html", "application/vnd.php-object"},
 *     @SWG\Parameter(
 *         name="url",
 *         in="query",
 *         description="The subject url",
 *         required=true,
 *         type="string",
 *         default="",
 *     ),
 *     @SWG\Parameter(
 *         name="scope",
 *         in="query",
 *         description="Scope [Options: 'phrase_to_page', 'phrase_to_subdomain', 'phrase_to_domain', 'term_to_page', 'term_to_subdomain', 'term_to_domain']",
 *         required=false,
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

    
class Seomoz {

    public static $name = "Seomoz";
    
    public static $services = array (
		'url-metrics' => 
		array (
		  'name' => 'url-metrics',
		  'description' => '',
		  'method' => 'get',
		  'parameters' => 
		  array (
		    'url' => 
		    array (
		      'description' => 'The subject url',
		      'type' => 'string',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => true,
		    ),
		  ),
		),
		'link-data' => 
		array (
		  'name' => 'link-data',
		  'description' => '',
		  'method' => 'get',
		  'parameters' => 
		  array (
		    'url' => 
		    array (
		      'description' => 'The subject url',
		      'type' => 'string',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => true,
		    ),
		    'scope' => 
		    array (
		      'description' => 'Scope',
		      'type' => 'string',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => false,
		      'options' => 
		      array (
		        0 => 'page_to_page',
		        1 => 'page_to_subdomain',
		        2 => 'page_to_domain',
		        3 => 'domain_to_page',
		        4 => 'domain_to_subdomain',
		        5 => 'domain_to_domain',
		      ),
		    ),
		    'sort' => 
		    array (
		      'description' => 'Sort',
		      'type' => 'string',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => false,
		      'options' => 
		      array (
		        0 => 'page_authority',
		        1 => 'domain_authority',
		        2 => 'domains_linking_domain',
		        3 => 'domains_linking_page',
		      ),
		    ),
		    'filter' => 
		    array (
		      'description' => 'Filters',
		      'type' => 'array',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => false,
		      'options' => 
		      array (
		        0 => 'internal',
		        1 => 'external',
		        2 => 'nofollow',
		        3 => 'follow',
		        4 => '301',
		      ),
		      'collectionFormat' => 'csv',
		      'items' => 
		      array (
		        'type' => 'string',
		      ),
		    ),
		    'limit' => 
		    array (
		      'description' => 'Limit',
		      'type' => 'number',
		      'in' => 'query',
		      'default' => 50,
		      'required' => false,
		      'minimum' => 1,
		      'maximum' => 1000,
		      'format' => 'integer',
		    ),
		    'offset' => 
		    array (
		      'description' => 'Offset',
		      'type' => 'number',
		      'in' => 'query',
		      'default' => 0,
		      'required' => false,
		      'format' => 'integer',
		    ),
		  ),
		),
		'anchor-text-data' => 
		array (
		  'name' => 'anchor-text-data',
		  'description' => '',
		  'method' => 'get',
		  'parameters' => 
		  array (
		    'url' => 
		    array (
		      'description' => 'The subject url',
		      'type' => 'string',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => true,
		    ),
		    'scope' => 
		    array (
		      'description' => 'Scope',
		      'type' => 'string',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => false,
		      'options' => 
		      array (
		        0 => 'phrase_to_page',
		        1 => 'phrase_to_subdomain',
		        2 => 'phrase_to_domain',
		        3 => 'term_to_page',
		        4 => 'term_to_subdomain',
		        5 => 'term_to_domain',
		      ),
		    ),
		  ),
		),
	);

}
