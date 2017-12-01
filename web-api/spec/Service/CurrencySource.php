<?php
namespace Daytalytics\Spec\Service;
        
/**
 * @SWG\Tag(
 *   name="CurrencySource",
 *   description="CurrencySource"
 * )
 */
/**
 * @SWG\Get(
 *     path="/currency_source/convert",
 *     tags={"CurrencySource"},
 *     summary="CurrencySource",
 *     description="CurrencySource",
 *     produces={"application/xml", "application/json", "text/html", "application/vnd.php-object"},
 *     @SWG\Parameter(
 *         name="from",
 *         in="query",
 *         description="A 3 letter currency code eg NZD. [Options are available from currencies]",
 *         required=true,
 *         type="string",
 *         default="",
 *     ),
 *     @SWG\Parameter(
 *         name="to",
 *         in="query",
 *         description="An array of 3 letter currency codes to convert to. [Options are available from currencies]",
 *         required=true,
 *         type="array",
 *         default="",
 * items={"type":"string"},
 * collectionFormat="multi",
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
 *     path="/currency_source/currencies",
 *     tags={"CurrencySource"},
 *     summary="CurrencySource",
 *     description="CurrencySource",
 *     produces={"application/xml", "application/json", "text/html", "application/vnd.php-object"},

 *     @SWG\Response(response="200", description="An example resource"),
 *     @SWG\Response(response="204", description="An empty resource"),
 *     @SWG\Response(response="400", description="Request error"),
 *     @SWG\Response(response="401", description="Unauthorized error"),
 *     @SWG\Response(response="403", description="Unauthenticated error"),
 *     @SWG\Response(response="500", description="Server error")
 * )
 */

    
class CurrencySource {

    public static $name = "CurrencySource";
    
    public static $services = array (
		'convert' => 
		array (
		  'name' => 'convert',
		  'description' => '',
		  'method' => 'get',
		  'parameters' => 
		  array (
		    'from' => 
		    array (
		      'description' => 'A 3 letter currency code eg NZD. [Options are available from currencies]',
		      'type' => 'string',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => true,
		    ),
		    'to' => 
		    array (
		      'description' => 'An array of 3 letter currency codes to convert to. [Options are available from currencies]',
		      'type' => 'array',
		      'in' => 'query',
		      'default' => NULL,
		      'required' => true,
		      'collectionFormat' => 'multi',
		      'items' => 
		      array (
		        'type' => 'string',
		      ),
		    ),
		  ),
		),
		'currencies' => 
		array (
		  'name' => 'currencies',
		  'description' => '',
		  'method' => 'get',
		  'parameters' => 
		  array (
		  ),
		),
	);

}
