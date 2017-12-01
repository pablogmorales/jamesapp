<?php

namespace Daytalytics\Module;

use Daytalytics\DataGetter;
use Daytalytics\RequestException;
use Exception;

class BingSearch extends BaseModule {
	
    protected $useProxies = false;
    
	private $number_of_results_on_each_remote_page = 50;

	private $appid = '';
	private $allowed_location = array(
    	//array("Name"=>"ar-XA", "Language"=>"Arabic-Arabia"),
        array("Name"=>"bg-BG", "Language"=>"Bulgarian-Bulgaria"),
        array("Name"=>"cs-CZ", "Language"=>"Czech-Czech Republic"),
        array("Name"=>"da-DK", "Language"=>"Danish-Denmark"),
        array("Name"=>"de-AT", "Language"=>"German-Austria"),
        array("Name"=>"de-CH", "Language"=>"German-Switzerland"),
        array("Name"=>"de-DE", "Language"=>"German-Germany"),
        array("Name"=>"el-GR", "Language"=>"Greek-Greece"),
        array("Name"=>"en-AU", "Language"=>"English-Australia"),
        array("Name"=>"en-CA", "Language"=>"English-Canada"),
        array("Name"=>"en-GB", "Language"=>"English-United Kingdom"),
        array("Name"=>"en-ID", "Language"=>"English-Indonesia"),
        array("Name"=>"en-IE", "Language"=>"English-Ireland"),
        array("Name"=>"en-IN", "Language"=>"English-India"),
        array("Name"=>"en-MY", "Language"=>"English-Malaysia"),
        array("Name"=>"en-NZ", "Language"=>"English-New Zealand"),
        array("Name"=>"en-PH", "Language"=>"English-Philippines"),
        array("Name"=>"en-SG", "Language"=>"English-Singapore"),
        array("Name"=>"en-US", "Language"=>"English-United States"),
        array("Name"=>"en-XA", "Language"=>"English-Arabia"),
        array("Name"=>"en-ZA", "Language"=>"English-South Africa"),
        array("Name"=>"es-AR", "Language"=>"Spanish-Argentina"),
        array("Name"=>"es-CL", "Language"=>"Spanish-Chile"),
        array("Name"=>"es-ES", "Language"=>"Spanish-Spain"),
        array("Name"=>"es-MX", "Language"=>"Spanish-Mexico"),
        //array("Name"=>"es-US", "Language"=>"Spanish-United States"),
        array("Name"=>"es-XL", "Language"=>"Spanish-Latin America"),
        array("Name"=>"et-EE", "Language"=>"Estonian-Estonia"),
        array("Name"=>"fi-FI", "Language"=>"Finnish-Finland"),
        //array("Name"=>"fr-BE", "Language"=>"French-Belgium"),
        //array("Name"=>"fr-CA", "Language"=>"French-Canada"),
        //array("Name"=>"fr-CH", "Language"=>"French-Switzerland"),
        array("Name"=>"fr-FR", "Language"=>"French-France"),
        array("Name"=>"he-IL", "Language"=>"Hebrew-Israel"),
        array("Name"=>"hr-HR", "Language"=>"Croatian-Croatia"),
        array("Name"=>"hu-HU", "Language"=>"Hungarian-Hungary"),
        array("Name"=>"it-IT", "Language"=>"Italian-Italy"),
        array("Name"=>"ja-JP", "Language"=>"Japanese-Japan"),
        array("Name"=>"ko-KR", "Language"=>"Korean-Korea"),
        array("Name"=>"lt-LT", "Language"=>"Lithuanian-Lithuania"),
        array("Name"=>"lv-LV", "Language"=>"Latvian-Latvia"),
        array("Name"=>"nb-NO", "Language"=>"Norwegian-Norway"),
        array("Name"=>"nl-BE", "Language"=>"Dutch-Belgium"),
        array("Name"=>"nl-NL", "Language"=>"Dutch-Netherlands"),
        array("Name"=>"pl-PL", "Language"=>"Polish-Poland"),
        array("Name"=>"pt-BR", "Language"=>"Portuguese-Brazil"),
        array("Name"=>"pt-PT", "Language"=>"Portuguese-Portugal"),
        array("Name"=>"ro-RO", "Language"=>"Romanian-Romania"),
        array("Name"=>"ru-RU", "Language"=>"Russian-Russia"),
        array("Name"=>"sk-SK", "Language"=>"Slovak-Slovak Republic"),
        array("Name"=>"sl-SL", "Language"=>"Slovenian-Slovenia"),
        array("Name"=>"sv-SE", "Language"=>"Swedish-Sweden"),
        array("Name"=>"th-TH", "Language"=>"Thai-Thailand"),
        array("Name"=>"tr-TR", "Language"=>"Turkish-Turkey"),
        array("Name"=>"uk-UA", "Language"=>"Ukrainian-Ukraine"),
        array("Name"=>"zh-CN", "Language"=>"Chinese-China"),
        array("Name"=>"zh-HK", "Language"=>"Chinese-Hong Kong SAR"),
        array("Name"=>"zh-TW", "Language"=>"Chinese-Taiwan")
	    
	);
	
	public function define_service() {
	    $params = [
	        'keyword' => [
	            'description' => 'Keyword search term',
	            'required' => true
	        ],
	        'limit' => [
	            'description' => 'Maximum number of search results',
	            'default' => 10,
	            'minimum' => 1,
	            'maximum' => 200
	        ],
	        'start' => [
	            'description' => 'Start position of results',
	            'default' => 0,
	        ],
	        'loc' => [
	            'description' => 'Search location',
	            'default' => 'us',
	            'options' => [
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
	                'tw' => 'Taiwan'
	            ]
	        ]
	    ];
		return [
		    'ns' => [
		        'parameters' => $params
		    ],
		    'pl' => [
		        'parameters' => $params
		    ]
		];
	}

	public function handle_request(array $params = []) {
		if (!isset($params['type'])) {
			$params['type'] = 'NS';
		}
		else {
			$params['type'] = strtoupper($params['type']);
		}

		if (!isset($params['keyword']) || empty($params['keyword'])) {
			throw new RequestException('A keyword is required.');
		}

		if (!isset($params['start'])) {
			$params['start'] = 0;
		}

		if (isset($params['limit']) && $params['limit'] >= 0) {
			$params['limit'] = (int)$params['limit'];
		}
		else {
			$params['limit'] = 10;
		}
		if ($params['limit'] > 200) {
			throw new RequestException("Results limit exceded (200).");
		}
		
		if (!isset($params['loc'])) {
			$params['loc'] = "US";
		}
				
		switch ($params['type']) {
			case 'PL' :
				$params['limit'] = 10;
				$results = $this->search_bing($params['keyword'], $params['type'], $params['start'], $params['limit'], $params['loc']);
				break;
			
			case 'NS' :
				$results = $this->search_bing($params['keyword'], $params['type'], $params['start'], $params['limit'], $params['loc']);
				break;
			
			default:
				throw new RequestException('A valid type is required.');
				break;
		}
		
		return $results;
	}

	function is_request_similar($request1, $request2) {
		$request1_parts = parse_url($request1);
		$request2_parts = parse_url($request2);

		if(empty($request1_parts) || empty($request2_parts)) {
			return false;
		}

		if($request1_parts['host'] != $request2_parts['host']) {
			return false;
		}

		$query1 = substr($request1, strpos($request1, '?')+1);
		$query2 = substr($request2, strpos($request2, '?')+1);

		$query1 = $this->parse_query($query1);
		$query2 = $this->parse_query($query2);


		if(isset($query1['q'])) {
			if($query1['q'] != $query2['q']) {
				return false;
			}
			if($query1['first'] != $query2['first']) {
				return false;
			}
		}
		else {
			if($query1['query'] != $query2['query']) {
				return false;
			}
			if($query1['Web.Count'] != $query2['Web.Count']) {
				return false;
			}
			if(!isset($query1['Web.Offset'])) {
				$query1['Web.Offset'] = 0;
			}
			if(!isset($query2['Web.Offset'])) {
				$query2['Web.Offset'] = 0;
			}
			if($query1['Web.Offset'] != $query2['Web.Offset']) {
				return false;
			}
		}
		return true;
	}

	function get_serp_url($keyword, $position, $loc) {
		//assume 10 results, since that is the default and cannot be changed in the url
		$per_page = 10;

		//find the position of the first search result for the page
		$page_number = floor($position / $per_page);
		$offset = ($page_number * $per_page) + 1;

		$gl = $this->get_location(str_replace(" ", '',$loc));

		$q = urlencode($keyword) . "&scope=web&filt=all&first=" . $offset . "&FORM=PERE&mkt=". $gl;
		return "http://www.bing.com/search?q=" . $q;
	}

	function get_url($keyword, $type, $current_page_start, $loc){
		
		$current_page_start_bing = $current_page_start;

		$q = '?q=' . rawurlencode($keyword);
		$q .= "&offset=" . ((int)$current_page_start);
		$q .= "&count=" . ((int)$this->number_of_results_on_each_remote_page);
		$q .= "&responseFilter=Webpages";

		$cl = '';
		//By default Bing will auto detect user's current location/ip if no location specified.
		if(isset($loc)){
			//check location if valid
			$loc_name = str_replace(' ','',$loc);
			$gl = $this->get_location($loc_name);
			$q .= '&mkt='.$gl;
			$loc = explode('-', $gl);
			$cl = ':' . $loc[1];
		}
		$url = "https://api.cognitive.microsoft.com/bing/v5.0/search".$q;
		$cache_key = $type . ':' . $keyword .':'. $current_page_start . $cl;

		$dg_params = array(
			'cache_key' => $cache_key,
			'data_sources' => $this->format_data_sources_for_data_getter(),
			'url_getter_params' => array(
				'url' => $url,
				'curl_options' => [
					CURLOPT_HTTPHEADER => ['Ocp-Apim-Subscription-Key:' . $this->appid]
				]
			)
		);
		return DataGetter::get_data_or_throw($this, $dg_params);
	}

	function search_bing($keyword, $type, $start, $limit, $loc) {
		
		//how many results do we want?
		$max = $limit;
		
		//and starting from what offset?
		$start = $start;
		
		//how many pages do we need?
		$number_of_pages_required = ceil(($start + $max)/$this->number_of_results_on_each_remote_page);
		
		//if no results are required, get the first page anyway so we can get the total, etc
		if ($number_of_pages_required == 0) {
			$number_of_pages_required = 1;
		}
		
		
		if ($limit > 200) {
			throw new RequestException('Results limit exceded (200).');
		}
		
		//work out which page to start from
		$first_page = floor($start/$this->number_of_results_on_each_remote_page);
		
		//initialise variables
		$total_available = false;
		$max_pages_available = false;
		$data = array(); // a temporary storage for result items
		// Set default return values
		$results = array('Total' => 0, 'Results' => $data);
		
		if ($type == 'NS' || $type == 'PL') {

			//make sure we're not trying to get more pages than we need, or more pages than are available
			for ($i = $first_page; ($i < $number_of_pages_required && ($max_pages_available === false || $i < $max_pages_available)); $i++) {
				
				$current_page_offset = $i * $this->number_of_results_on_each_remote_page;
			
				//get a page
				$json_response = $this->get_url($keyword, $type, $current_page_offset, $loc);
				$results = json_decode($json_response);

				//get error from bing
				if(isset($results->errors)) {
					$error = array_shift($results->errors);
					throw new Exception($error->message);
				}

				if($results === false || $results->_type !== 'SearchResponse') {
					throw new Exception('Unrecognised results.');
				}
				
				if(isset($results->webPages)) {
					$WebResultSet = $results->webPages;
				}
					
				//get the total
				if ($total_available === false) {
					if(isset($WebResultSet->totalEstimatedMatches)) {
						$total_available = (int)$WebResultSet->totalEstimatedMatches;
					} else {
						$total_available = 0;
					}
				}
				
				//if we just wanted the total, we've already go it
				if ($type == 'PL') {
					return $total_available;
				}
				
				//if we've come too far (this shouldn't occur, but it's just in case)
				if ($current_page_offset > $total_available) {
					break;
				}
				
				//if there are fewer results available than we intend to get
				if($total_available < ($start+$max)) {
					$max_pages_available = ceil( $total_available / $this->number_of_results_on_each_remote_page );
				}
				
				
				$position_on_this_page = 0;
				if (!empty($WebResultSet->value)) {
					foreach($WebResultSet->value as $result) {
						
						//work out the total offset to get to this result
						$position = (int)($current_page_offset + $position_on_this_page);
						
						//increment the counter ready for the next result
						$position_on_this_page++;
						
						if ($position < $start) {
							continue;
						}
						
						if ($position > ($start + $max) - 1) {
							break;
						}

						//$data[] = array('Title' => (string)$result_simplexmlelement->Title, 'Url' => (string)$result_simplexmlelement->Url, 'Position' => $position);
						$datum = array();
						$datum['Title'] = (string)$result->name;
						$datum['Url'] = (string)$result->displayUrl;
						$datum['Position'] = $position;
						if ($type="NS") {
							$datum['SerpLink'] = $this->get_serp_url($keyword, $position, $loc);
						}

						$data[] = $datum;
					}
				}
				
			}
			$results = array('Total' => $total_available, 'Results' => $data);
		}

		return $results;
	}

	function get_location($loc_name) {
		$loc_name = strtoupper($loc_name);
		$locations = $this->allowed_location;
		reset($locations);
		$gl = false;
		while (list($tmpkey,$tmpval) = each ($locations)){
			if(is_array($locations[$tmpkey])) {
				$loc = explode('-', $locations[$tmpkey]['Name']);
				$lang = explode('-', $locations[$tmpkey]['Language']);
				$lang_up = str_replace(' ','',$lang[1]);
				if ($loc_name == $loc[1] || $loc_name == strtoupper($lang_up)) {
					$gl = $locations[$tmpkey]['Name'];
					break;
				}
			}
		}
		if($gl===false) {
			throw new RequestException('Google search location: '.$loc_name.' is not yet supported.');
		}
		return $gl;
	}
}
?>
