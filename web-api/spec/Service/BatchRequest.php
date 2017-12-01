<?php
namespace Daytalytics\Spec\Service;
        
/**
 * @SWG\Tag(
 *   name="BatchRequest",
 *   description="BatchRequest"
 * )
 */
/**
 * @SWG\Get(
 *     path="/batch_request",
 *     tags={"BatchRequest"},
 *     summary="BatchRequest",
 *     description="BatchRequest",
 *     produces={"application/xml", "application/json", "text/html", "application/vnd.php-object"},
 *     @SWG\Parameter(
 *         name="requests",
 *         in="query",
 *         description="",
 *         required=true,
 *         type="array",
 *         default="",
 * items={"module":{"type":"string","required":true,"description":"Module for each batch operation (all must be for the same module)"},"type":{"type":"string","required":false,"description":"Module service type for each batch operation"},"_n":{"description":"Inidivual input param(s) for the service"}},
 *     ),

 *     @SWG\Response(response="200", description="An example resource"),
 *     @SWG\Response(response="204", description="An empty resource"),
 *     @SWG\Response(response="400", description="Request error"),
 *     @SWG\Response(response="401", description="Unauthorized error"),
 *     @SWG\Response(response="403", description="Unauthenticated error"),
 *     @SWG\Response(response="500", description="Server error")
 * )
 */

    
class BatchRequest {

    public static $name = "BatchRequest";
    
    public static $services = array (
		'default' => 
		array (
		  'name' => 'BatchRequest',
		  'description' => '',
		  'method' => 'get',
		  'parameters' => 
		  array (
		    'requests' => 
		    array (
		      'description' => '',
		      'type' => 'array',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => true,
		      'descriptiopn' => 'Requests array for batch operations, e.g. &requests[0][module]=google_search&requests[0][type]=ns&requests[0][keyword]=ipod',
		      'items' => 
		      array (
		        'module' => 
		        array (
		          'type' => 'string',
		          'required' => true,
		          'description' => 'Module for each batch operation (all must be for the same module)',
		        ),
		        'type' => 
		        array (
		          'type' => 'string',
		          'required' => false,
		          'description' => 'Module service type for each batch operation',
		        ),
		        '_n' => 
		        array (
		          'description' => 'Inidivual input param(s) for the service',
		        ),
		      ),
		    ),
		  ),
		),
	);

}
