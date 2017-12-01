<?php
namespace Daytalytics\Spec\Service;
        
/**
 * @SWG\Tag(
 *   name="MajesticSeo",
 *   description="MajesticSeo"
 * )
 */
/**
 * @SWG\Get(
 *     path="/majestic_seo/backlinks",
 *     tags={"MajesticSeo"},
 *     summary="MajesticSeo",
 *     description="MajesticSeo",
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
 *         name="datasource",
 *         in="query",
 *         description="Data source [Options: 'historic', 'fresh']",
 *         required=false,
 *         type="string",
 *         default="historic",
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
 *     path="/majestic_seo/bllist",
 *     tags={"MajesticSeo"},
 *     summary="MajesticSeo",
 *     description="MajesticSeo",
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
 *         name="datasource",
 *         in="query",
 *         description="Data source [Options: 'historic', 'fresh']",
 *         required=false,
 *         type="string",
 *         default="historic",
 *     ),
 *     @SWG\Parameter(
 *         name="count",
 *         in="query",
 *         description="Number of results",
 *         required=true,
 *         type="number",
 *         default="",
 * maximum=1000,
 * minimum=50,
 *     ),

 *     @SWG\Response(response="200", description="An example resource"),
 *     @SWG\Response(response="204", description="An empty resource"),
 *     @SWG\Response(response="400", description="Request error"),
 *     @SWG\Response(response="401", description="Unauthorized error"),
 *     @SWG\Response(response="403", description="Unauthenticated error"),
 *     @SWG\Response(response="500", description="Server error")
 * )
 */

    
class MajesticSeo {

    public static $name = "MajesticSeo";
    
    public static $services = array (
		'backlinks' => 
		array (
		  'name' => 'backlinks',
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
		    'datasource' => 
		    array (
		      'description' => 'Data source',
		      'type' => 'string',
		      'in' => 'query',
		      'default' => 'historic',
		      'required' => false,
		      'options' => 
		      array (
		        0 => 'historic',
		        1 => 'fresh',
		      ),
		    ),
		  ),
		),
		'bllist' => 
		array (
		  'name' => 'bllist',
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
		    'datasource' => 
		    array (
		      'description' => 'Data source',
		      'type' => 'string',
		      'in' => 'query',
		      'default' => 'historic',
		      'required' => false,
		      'options' => 
		      array (
		        0 => 'historic',
		        1 => 'fresh',
		      ),
		    ),
		    'count' => 
		    array (
		      'description' => 'Number of results',
		      'type' => 'number',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => true,
		      'minimum' => 50,
		      'maximum' => 1000,
		    ),
		  ),
		),
	);

}
