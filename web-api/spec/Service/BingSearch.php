<?php
namespace Daytalytics\Spec\Service;
        
/**
 * @SWG\Tag(
 *   name="BingSearch",
 *   description="BingSearch"
 * )
 */
/**
 * @SWG\Get(
 *     path="/bing_search/ns",
 *     tags={"BingSearch"},
 *     summary="BingSearch",
 *     description="BingSearch",
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
 *     @SWG\Parameter(
 *         name="start",
 *         in="query",
 *         description="Start position of results",
 *         required=false,
 *         type="number",
 *         default=0,
 * format="integer",
 *     ),
 *     @SWG\Parameter(
 *         name="loc",
 *         in="query",
 *         description="Search location [Options: 'us' (USA - United States), 'bg' (Bulgaria), 'cz' (Czech Republic), 'dk' (Denmark), 'at' (Austria), 'ch' (Switzerland), 'de' (Germany), 'gr' (Greece), 'au' (Australia), 'ca' (Canada), 'gb' (United Kingdom), 'id' (Indonesia), 'ie' (Ireland), 'in' (India), 'my' (Malaysia), 'nz' (New Zealand), 'ph' (Philippines), 'sg' (Singapore), 'xa' (Arabia), 'za' (South Africa), 'ar' (Argentina), 'cl' (Chile), 'es' (Spain), 'mx' (Mexico), 'xl' (Latin America), 'ee' (Estonia), 'fi' (Finland), 'fr' (France), 'il' (Israel), 'hr' (Croatia), 'hu' (Hungary), 'it' (Italy), 'jp' (Japan), 'kr' (Korea), 'lt' (Lithuania), 'lv' (Latvia), 'no' (Norway), 'be' (Belgium), 'nl' (Netherlands), 'pl' (Poland), 'br' (Brazil), 'pt' (Portugal), 'ro' (Romania), 'ru' (Russia), 'sk' (Slovak Republic), 'sl' (Slovenia), 'se' (Sweden), 'th' (Thailand), 'tr' (Turkey), 'ua' (Ukraine), 'cn' (China), 'hk' (Hong Kong SAR), 'tw' (Taiwan)]",
 *         required=false,
 *         type="string",
 *         default="us",
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
 *     path="/bing_search/pl",
 *     tags={"BingSearch"},
 *     summary="BingSearch",
 *     description="BingSearch",
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
 *     @SWG\Parameter(
 *         name="start",
 *         in="query",
 *         description="Start position of results",
 *         required=false,
 *         type="number",
 *         default=0,
 * format="integer",
 *     ),
 *     @SWG\Parameter(
 *         name="loc",
 *         in="query",
 *         description="Search location [Options: 'us' (USA - United States), 'bg' (Bulgaria), 'cz' (Czech Republic), 'dk' (Denmark), 'at' (Austria), 'ch' (Switzerland), 'de' (Germany), 'gr' (Greece), 'au' (Australia), 'ca' (Canada), 'gb' (United Kingdom), 'id' (Indonesia), 'ie' (Ireland), 'in' (India), 'my' (Malaysia), 'nz' (New Zealand), 'ph' (Philippines), 'sg' (Singapore), 'xa' (Arabia), 'za' (South Africa), 'ar' (Argentina), 'cl' (Chile), 'es' (Spain), 'mx' (Mexico), 'xl' (Latin America), 'ee' (Estonia), 'fi' (Finland), 'fr' (France), 'il' (Israel), 'hr' (Croatia), 'hu' (Hungary), 'it' (Italy), 'jp' (Japan), 'kr' (Korea), 'lt' (Lithuania), 'lv' (Latvia), 'no' (Norway), 'be' (Belgium), 'nl' (Netherlands), 'pl' (Poland), 'br' (Brazil), 'pt' (Portugal), 'ro' (Romania), 'ru' (Russia), 'sk' (Slovak Republic), 'sl' (Slovenia), 'se' (Sweden), 'th' (Thailand), 'tr' (Turkey), 'ua' (Ukraine), 'cn' (China), 'hk' (Hong Kong SAR), 'tw' (Taiwan)]",
 *         required=false,
 *         type="string",
 *         default="us",
 *     ),

 *     @SWG\Response(response="200", description="An example resource"),
 *     @SWG\Response(response="204", description="An empty resource"),
 *     @SWG\Response(response="400", description="Request error"),
 *     @SWG\Response(response="401", description="Unauthorized error"),
 *     @SWG\Response(response="403", description="Unauthenticated error"),
 *     @SWG\Response(response="500", description="Server error")
 * )
 */

    
class BingSearch {

    public static $name = "BingSearch";
    
    public static $services = array (
		'ns' => 
		array (
		  'name' => 'ns',
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
		    'start' => 
		    array (
		      'description' => 'Start position of results',
		      'type' => 'number',
		      'in' => 'query',
		      'default' => 0,
		      'required' => false,
		      'format' => 'integer',
		    ),
		    'loc' => 
		    array (
		      'description' => 'Search location',
		      'type' => 'string',
		      'in' => 'query',
		      'default' => 'us',
		      'required' => false,
		      'options' => 
		      array (
		        'us' => 'USA - United States',
		        'bg' => 'Bulgaria',
		        'cz' => 'Czech Republic',
		        'dk' => 'Denmark',
		        'at' => 'Austria',
		        'ch' => 'Switzerland',
		        'de' => 'Germany',
		        'gr' => 'Greece',
		        'au' => 'Australia',
		        'ca' => 'Canada',
		        'gb' => 'United Kingdom',
		        'id' => 'Indonesia',
		        'ie' => 'Ireland',
		        'in' => 'India',
		        'my' => 'Malaysia',
		        'nz' => 'New Zealand',
		        'ph' => 'Philippines',
		        'sg' => 'Singapore',
		        'xa' => 'Arabia',
		        'za' => 'South Africa',
		        'ar' => 'Argentina',
		        'cl' => 'Chile',
		        'es' => 'Spain',
		        'mx' => 'Mexico',
		        'xl' => 'Latin America',
		        'ee' => 'Estonia',
		        'fi' => 'Finland',
		        'fr' => 'France',
		        'il' => 'Israel',
		        'hr' => 'Croatia',
		        'hu' => 'Hungary',
		        'it' => 'Italy',
		        'jp' => 'Japan',
		        'kr' => 'Korea',
		        'lt' => 'Lithuania',
		        'lv' => 'Latvia',
		        'no' => 'Norway',
		        'be' => 'Belgium',
		        'nl' => 'Netherlands',
		        'pl' => 'Poland',
		        'br' => 'Brazil',
		        'pt' => 'Portugal',
		        'ro' => 'Romania',
		        'ru' => 'Russia',
		        'sk' => 'Slovak Republic',
		        'sl' => 'Slovenia',
		        'se' => 'Sweden',
		        'th' => 'Thailand',
		        'tr' => 'Turkey',
		        'ua' => 'Ukraine',
		        'cn' => 'China',
		        'hk' => 'Hong Kong SAR',
		        'tw' => 'Taiwan',
		      ),
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
		    'start' => 
		    array (
		      'description' => 'Start position of results',
		      'type' => 'number',
		      'in' => 'query',
		      'default' => 0,
		      'required' => false,
		      'format' => 'integer',
		    ),
		    'loc' => 
		    array (
		      'description' => 'Search location',
		      'type' => 'string',
		      'in' => 'query',
		      'default' => 'us',
		      'required' => false,
		      'options' => 
		      array (
		        'us' => 'USA - United States',
		        'bg' => 'Bulgaria',
		        'cz' => 'Czech Republic',
		        'dk' => 'Denmark',
		        'at' => 'Austria',
		        'ch' => 'Switzerland',
		        'de' => 'Germany',
		        'gr' => 'Greece',
		        'au' => 'Australia',
		        'ca' => 'Canada',
		        'gb' => 'United Kingdom',
		        'id' => 'Indonesia',
		        'ie' => 'Ireland',
		        'in' => 'India',
		        'my' => 'Malaysia',
		        'nz' => 'New Zealand',
		        'ph' => 'Philippines',
		        'sg' => 'Singapore',
		        'xa' => 'Arabia',
		        'za' => 'South Africa',
		        'ar' => 'Argentina',
		        'cl' => 'Chile',
		        'es' => 'Spain',
		        'mx' => 'Mexico',
		        'xl' => 'Latin America',
		        'ee' => 'Estonia',
		        'fi' => 'Finland',
		        'fr' => 'France',
		        'il' => 'Israel',
		        'hr' => 'Croatia',
		        'hu' => 'Hungary',
		        'it' => 'Italy',
		        'jp' => 'Japan',
		        'kr' => 'Korea',
		        'lt' => 'Lithuania',
		        'lv' => 'Latvia',
		        'no' => 'Norway',
		        'be' => 'Belgium',
		        'nl' => 'Netherlands',
		        'pl' => 'Poland',
		        'br' => 'Brazil',
		        'pt' => 'Portugal',
		        'ro' => 'Romania',
		        'ru' => 'Russia',
		        'sk' => 'Slovak Republic',
		        'sl' => 'Slovenia',
		        'se' => 'Sweden',
		        'th' => 'Thailand',
		        'tr' => 'Turkey',
		        'ua' => 'Ukraine',
		        'cn' => 'China',
		        'hk' => 'Hong Kong SAR',
		        'tw' => 'Taiwan',
		      ),
		    ),
		  ),
		),
	);

}
