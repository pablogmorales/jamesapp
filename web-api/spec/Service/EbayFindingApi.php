<?php
namespace Daytalytics\Spec\Service;
        
/**
 * @SWG\Tag(
 *   name="EbayFindingApi",
 *   description="EbayFindingApi"
 * )
 */
/**
 * @SWG\Get(
 *     path="/ebay_finding_api/finditemsbykeywords",
 *     tags={"EbayFindingApi"},
 *     summary="EbayFindingApi",
 *     description="EbayFindingApi",
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
 *         name="loc",
 *         in="query",
 *         description="Location [Options: 'us' (EBAY-US), 'uk' (EBAY-GB), 'au' (EBAY-AU), 'ca' (EBAY-ENCA)]",
 *         required=true,
 *         type="number",
 *         default="",
 *     ),
 *     @SWG\Parameter(
 *         name="start",
 *         in="query",
 *         description="Start time",
 *         required=false,
 *         type="string",
 *         default="",
 * format="dateTime",
 *     ),
 *     @SWG\Parameter(
 *         name="end",
 *         in="query",
 *         description="End time",
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
/**
 * @SWG\Get(
 *     path="/ebay_finding_api/finditemscompletedbykeywords",
 *     tags={"EbayFindingApi"},
 *     summary="EbayFindingApi",
 *     description="EbayFindingApi",
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
 *         name="loc",
 *         in="query",
 *         description="Location [Options: 'us' (EBAY-US), 'uk' (EBAY-GB), 'au' (EBAY-AU), 'ca' (EBAY-ENCA)]",
 *         required=true,
 *         type="number",
 *         default="",
 *     ),
 *     @SWG\Parameter(
 *         name="start",
 *         in="query",
 *         description="Start time",
 *         required=false,
 *         type="string",
 *         default="",
 * format="dateTime",
 *     ),
 *     @SWG\Parameter(
 *         name="end",
 *         in="query",
 *         description="End time",
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
/**
 * @SWG\Get(
 *     path="/ebay_finding_api/finditemstotalbykeywords",
 *     tags={"EbayFindingApi"},
 *     summary="EbayFindingApi",
 *     description="EbayFindingApi",
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
 *         name="loc",
 *         in="query",
 *         description="Location [Options: 'us' (EBAY-US), 'uk' (EBAY-GB), 'au' (EBAY-AU), 'ca' (EBAY-ENCA)]",
 *         required=true,
 *         type="number",
 *         default="",
 *     ),
 *     @SWG\Parameter(
 *         name="start",
 *         in="query",
 *         description="Start time",
 *         required=false,
 *         type="string",
 *         default="",
 * format="dateTime",
 *     ),
 *     @SWG\Parameter(
 *         name="end",
 *         in="query",
 *         description="End time",
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
/**
 * @SWG\Get(
 *     path="/ebay_finding_api/finditemscompletedtotalbykeywords",
 *     tags={"EbayFindingApi"},
 *     summary="EbayFindingApi",
 *     description="EbayFindingApi",
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
 *         name="loc",
 *         in="query",
 *         description="Location [Options: 'us' (EBAY-US), 'uk' (EBAY-GB), 'au' (EBAY-AU), 'ca' (EBAY-ENCA)]",
 *         required=true,
 *         type="number",
 *         default="",
 *     ),
 *     @SWG\Parameter(
 *         name="start",
 *         in="query",
 *         description="Start time",
 *         required=false,
 *         type="string",
 *         default="",
 * format="dateTime",
 *     ),
 *     @SWG\Parameter(
 *         name="end",
 *         in="query",
 *         description="End time",
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
/**
 * @SWG\Get(
 *     path="/ebay_finding_api/findsolditemstotalbykeywords",
 *     tags={"EbayFindingApi"},
 *     summary="EbayFindingApi",
 *     description="EbayFindingApi",
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
 *         name="loc",
 *         in="query",
 *         description="Location [Options: 'us' (EBAY-US), 'uk' (EBAY-GB), 'au' (EBAY-AU), 'ca' (EBAY-ENCA)]",
 *         required=true,
 *         type="number",
 *         default="",
 *     ),
 *     @SWG\Parameter(
 *         name="start",
 *         in="query",
 *         description="Start time",
 *         required=false,
 *         type="string",
 *         default="",
 * format="dateTime",
 *     ),
 *     @SWG\Parameter(
 *         name="end",
 *         in="query",
 *         description="End time",
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
/**
 * @SWG\Get(
 *     path="/ebay_finding_api/finditemsadvancedtotalbykeywords",
 *     tags={"EbayFindingApi"},
 *     summary="EbayFindingApi",
 *     description="EbayFindingApi",
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
 *         name="loc",
 *         in="query",
 *         description="Location [Options: 'us' (EBAY-US), 'uk' (EBAY-GB), 'au' (EBAY-AU), 'ca' (EBAY-ENCA)]",
 *         required=true,
 *         type="number",
 *         default="",
 *     ),
 *     @SWG\Parameter(
 *         name="start",
 *         in="query",
 *         description="Start time",
 *         required=false,
 *         type="string",
 *         default="",
 * format="dateTime",
 *     ),
 *     @SWG\Parameter(
 *         name="end",
 *         in="query",
 *         description="End time",
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
/**
 * @SWG\Get(
 *     path="/ebay_finding_api/findcatgoriesbykeywords",
 *     tags={"EbayFindingApi"},
 *     summary="EbayFindingApi",
 *     description="EbayFindingApi",
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
 *         name="loc",
 *         in="query",
 *         description="Location [Options: 'us' (EBAY-US), 'uk' (EBAY-GB), 'au' (EBAY-AU), 'ca' (EBAY-ENCA)]",
 *         required=true,
 *         type="number",
 *         default="",
 *     ),
 *     @SWG\Parameter(
 *         name="start",
 *         in="query",
 *         description="Start time",
 *         required=false,
 *         type="string",
 *         default="",
 * format="dateTime",
 *     ),
 *     @SWG\Parameter(
 *         name="end",
 *         in="query",
 *         description="End time",
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
/**
 * @SWG\Get(
 *     path="/ebay_finding_api/findcurrency",
 *     tags={"EbayFindingApi"},
 *     summary="EbayFindingApi",
 *     description="EbayFindingApi",
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
 *         name="loc",
 *         in="query",
 *         description="Location [Options: 'us' (EBAY-US), 'uk' (EBAY-GB), 'au' (EBAY-AU), 'ca' (EBAY-ENCA)]",
 *         required=true,
 *         type="number",
 *         default="",
 *     ),
 *     @SWG\Parameter(
 *         name="start",
 *         in="query",
 *         description="Start time",
 *         required=false,
 *         type="string",
 *         default="",
 * format="dateTime",
 *     ),
 *     @SWG\Parameter(
 *         name="end",
 *         in="query",
 *         description="End time",
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

    
class EbayFindingApi {

    public static $name = "EbayFindingApi";
    
    public static $services = array (
		'finditemsbykeywords' => 
		array (
		  'name' => 'finditemsbykeywords',
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
		    'loc' => 
		    array (
		      'description' => 'Location',
		      'type' => 'number',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => true,
		      'options' => 
		      array (
		        'us' => 'EBAY-US',
		        'uk' => 'EBAY-GB',
		        'au' => 'EBAY-AU',
		        'ca' => 'EBAY-ENCA',
		      ),
		    ),
		    'start' => 
		    array (
		      'description' => 'Start time',
		      'type' => 'string',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => false,
		      'format' => 'dateTime',
		    ),
		    'end' => 
		    array (
		      'description' => 'End time',
		      'type' => 'string',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => false,
		      'format' => 'dateTime',
		    ),
		  ),
		),
		'finditemscompletedbykeywords' => 
		array (
		  'name' => 'finditemscompletedbykeywords',
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
		    'loc' => 
		    array (
		      'description' => 'Location',
		      'type' => 'number',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => true,
		      'options' => 
		      array (
		        'us' => 'EBAY-US',
		        'uk' => 'EBAY-GB',
		        'au' => 'EBAY-AU',
		        'ca' => 'EBAY-ENCA',
		      ),
		    ),
		    'start' => 
		    array (
		      'description' => 'Start time',
		      'type' => 'string',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => false,
		      'format' => 'dateTime',
		    ),
		    'end' => 
		    array (
		      'description' => 'End time',
		      'type' => 'string',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => false,
		      'format' => 'dateTime',
		    ),
		  ),
		),
		'finditemstotalbykeywords' => 
		array (
		  'name' => 'finditemstotalbykeywords',
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
		    'loc' => 
		    array (
		      'description' => 'Location',
		      'type' => 'number',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => true,
		      'options' => 
		      array (
		        'us' => 'EBAY-US',
		        'uk' => 'EBAY-GB',
		        'au' => 'EBAY-AU',
		        'ca' => 'EBAY-ENCA',
		      ),
		    ),
		    'start' => 
		    array (
		      'description' => 'Start time',
		      'type' => 'string',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => false,
		      'format' => 'dateTime',
		    ),
		    'end' => 
		    array (
		      'description' => 'End time',
		      'type' => 'string',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => false,
		      'format' => 'dateTime',
		    ),
		  ),
		),
		'finditemscompletedtotalbykeywords' => 
		array (
		  'name' => 'finditemscompletedtotalbykeywords',
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
		    'loc' => 
		    array (
		      'description' => 'Location',
		      'type' => 'number',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => true,
		      'options' => 
		      array (
		        'us' => 'EBAY-US',
		        'uk' => 'EBAY-GB',
		        'au' => 'EBAY-AU',
		        'ca' => 'EBAY-ENCA',
		      ),
		    ),
		    'start' => 
		    array (
		      'description' => 'Start time',
		      'type' => 'string',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => false,
		      'format' => 'dateTime',
		    ),
		    'end' => 
		    array (
		      'description' => 'End time',
		      'type' => 'string',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => false,
		      'format' => 'dateTime',
		    ),
		  ),
		),
		'findsolditemstotalbykeywords' => 
		array (
		  'name' => 'findsolditemstotalbykeywords',
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
		    'loc' => 
		    array (
		      'description' => 'Location',
		      'type' => 'number',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => true,
		      'options' => 
		      array (
		        'us' => 'EBAY-US',
		        'uk' => 'EBAY-GB',
		        'au' => 'EBAY-AU',
		        'ca' => 'EBAY-ENCA',
		      ),
		    ),
		    'start' => 
		    array (
		      'description' => 'Start time',
		      'type' => 'string',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => false,
		      'format' => 'dateTime',
		    ),
		    'end' => 
		    array (
		      'description' => 'End time',
		      'type' => 'string',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => false,
		      'format' => 'dateTime',
		    ),
		  ),
		),
		'finditemsadvancedtotalbykeywords' => 
		array (
		  'name' => 'finditemsadvancedtotalbykeywords',
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
		    'loc' => 
		    array (
		      'description' => 'Location',
		      'type' => 'number',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => true,
		      'options' => 
		      array (
		        'us' => 'EBAY-US',
		        'uk' => 'EBAY-GB',
		        'au' => 'EBAY-AU',
		        'ca' => 'EBAY-ENCA',
		      ),
		    ),
		    'start' => 
		    array (
		      'description' => 'Start time',
		      'type' => 'string',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => false,
		      'format' => 'dateTime',
		    ),
		    'end' => 
		    array (
		      'description' => 'End time',
		      'type' => 'string',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => false,
		      'format' => 'dateTime',
		    ),
		  ),
		),
		'findcatgoriesbykeywords' => 
		array (
		  'name' => 'findcatgoriesbykeywords',
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
		    'loc' => 
		    array (
		      'description' => 'Location',
		      'type' => 'number',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => true,
		      'options' => 
		      array (
		        'us' => 'EBAY-US',
		        'uk' => 'EBAY-GB',
		        'au' => 'EBAY-AU',
		        'ca' => 'EBAY-ENCA',
		      ),
		    ),
		    'start' => 
		    array (
		      'description' => 'Start time',
		      'type' => 'string',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => false,
		      'format' => 'dateTime',
		    ),
		    'end' => 
		    array (
		      'description' => 'End time',
		      'type' => 'string',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => false,
		      'format' => 'dateTime',
		    ),
		  ),
		),
		'findcurrency' => 
		array (
		  'name' => 'findcurrency',
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
		    'loc' => 
		    array (
		      'description' => 'Location',
		      'type' => 'number',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => true,
		      'options' => 
		      array (
		        'us' => 'EBAY-US',
		        'uk' => 'EBAY-GB',
		        'au' => 'EBAY-AU',
		        'ca' => 'EBAY-ENCA',
		      ),
		    ),
		    'start' => 
		    array (
		      'description' => 'Start time',
		      'type' => 'string',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => false,
		      'format' => 'dateTime',
		    ),
		    'end' => 
		    array (
		      'description' => 'End time',
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
