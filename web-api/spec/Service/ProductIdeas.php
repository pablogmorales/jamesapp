<?php
namespace Daytalytics\Spec\Service;
        
/**
 * @SWG\Tag(
 *   name="ProductIdeas",
 *   description="ProductIdeas"
 * )
 */
/**
 * @SWG\Get(
 *     path="/product_ideas/categories",
 *     tags={"ProductIdeas"},
 *     summary="ProductIdeas",
 *     description="ProductIdeas",
 *     produces={"application/xml", "application/json", "text/html", "application/vnd.php-object"},
 *     @SWG\Parameter(
 *         name="feed",
 *         in="query",
 *         description=" [Options: 'wishedlist', 'amazon', 'ali']",
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

    
class ProductIdeas {

    public static $name = "ProductIdeas";
    
    public static $services = array (
		'categories' => 
		array (
		  'name' => 'categories',
		  'description' => '',
		  'method' => 'get',
		  'parameters' => 
		  array (
		    'feed' => 
		    array (
		      'description' => '',
		      'type' => 'string',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => true,
		      'options' => 
		      array (
		        0 => 'wishedlist',
		        1 => 'amazon',
		        2 => 'ali',
		      ),
		    ),
		  ),
		),
	);

}
