<?php
namespace Daytalytics\Spec\Service;
        
/**
 * @SWG\Tag(
 *   name="EbayResearchApiAlt",
 *   description="EbayResearchApiAlt"
 * )
 */
/**
 * @SWG\Get(
 *     path="/ebay_research_api_alt/researchresults",
 *     tags={"EbayResearchApiAlt"},
 *     summary="EbayResearchApiAlt",
 *     description="EbayResearchApiAlt",
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
 *         description="Defaults to latest possible. Must be over 30 days old. e.g. 2009-08-01",
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

    
class EbayResearchApiAlt {

    public static $name = "EbayResearchApiAlt";
    
    public static $services = array (
		'researchresults' => 
		array (
		  'name' => 'researchresults',
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
		      'description' => 'Defaults to latest possible. Must be over 30 days old. e.g. 2009-08-01',
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
