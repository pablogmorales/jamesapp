<?php
namespace Daytalytics\Spec\Service;
        
/**
 * @SWG\Tag(
 *   name="WebsiteInfo",
 *   description="WebsiteInfo"
 * )
 */
/**
 * @SWG\Get(
 *     path="/website_info/extractwebsiteinfobydomain",
 *     tags={"WebsiteInfo"},
 *     summary="WebsiteInfo",
 *     description="WebsiteInfo",
 *     produces={"application/xml", "application/json", "text/html", "application/vnd.php-object"},
 *     @SWG\Parameter(
 *         name="domain",
 *         in="query",
 *         description="Domain to check e.g. example.com",
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

    
class WebsiteInfo {

    public static $name = "WebsiteInfo";
    
    public static $services = array (
		'extractwebsiteinfobydomain' => 
		array (
		  'name' => 'extractwebsiteinfobydomain',
		  'description' => '',
		  'method' => 'get',
		  'parameters' => 
		  array (
		    'domain' => 
		    array (
		      'description' => 'Domain to check e.g. example.com',
		      'type' => 'string',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => true,
		    ),
		  ),
		),
	);

}
