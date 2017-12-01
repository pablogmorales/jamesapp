<?php

namespace Daytalytics\Module;

use Daytalytics\DataGetter;
use Daytalytics\RequestException;
use DOMDocument;
use DOMXPath;

class GoogleSearch extends BaseModule {
	
	private $limit = 10000; //this is the limit to the number of times this module can use each proxy in a day.
	
	private $previous_result_count = 0;
	
	private $number_of_results_on_each_remote_page = 10;
	
	private $allowed_types = array("NS","PL","PS","NEWS");
	
	private $allowed_location = array(
        array("Name"=>"Australia", "Domain"=>"com.au", "GL"=>"au"),
        array("Name"=>"Belgium", "Domain"=>"be", "GL"=>"be"),
        array("Name"=>"Brazil", "Domain"=>"com.br", "GL"=>"br"),
        array("Name"=>"Canada", "Domain"=>"ca", "GL"=>"ca"),
        array("Name"=>"China", "Domain"=>"cn", "GL"=>"cn"),
        array("Name"=>"Denmark", "Domain"=>"dk", "GL"=>"da"),
        array("Name"=>"Finland", "Domain"=>"fi", "GL"=>"fi"),
        array("Name"=>"France", "Domain"=>"fr", "GL"=>"fr"),
        array("Name"=>"Germany", "Domain"=>"de", "GL"=>"de"),
        array("Name"=>"India", "Domain"=>"co.in", "GL"=>"in"),
        array("Name"=>"Ireland", "Domain"=>"ie", "GL"=>"ie"),
        array("Name"=>"Japan", "Domain"=>"co.jp", "GL"=>"jp"),
        array("Name"=>"Mexico", "Domain"=>"com.mx", "GL"=>"mx"),
        array("Name"=>"NewZealand", "Domain"=>"co.nz", "GL"=>"nz"),
        array("Name"=>"Poland", "Domain"=>"pl", "GL"=>"pl"),
        array("Name"=>"Slovenija", "Domain"=>"si", "GL"=>"si"),
        array("Name"=>"SouthAfrica", "Domain"=>"co.za", "GL"=>"za"),
        array("Name"=>"Spain", "Domain"=>"es", "GL"=>"es"),
        array("Name"=>"Sweden", "Domain"=>"se", "GL"=>"se"),
        array("Name"=>"UnitedKingdom", "Domain"=>"co.uk", "GL"=>"uk"),
        array("Name"=>"UnitedStates", "Domain"=>"com", "GL"=>"us")
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
		            'be' => 'Belgium',
		            'da' => 'Denmark',
		            'de' => 'Germany',
		            'au' => 'Australia',
		            'ca' => 'Canada',
		            'uk' => 'United Kingdom',
		            'ie' => 'Ireland',
		            'in' => 'India',
		            'nz' => 'New Zealand',
		            'za' => 'South Africa',
		            'es' => 'Spain',
		            'mx' => 'Mexico',
		            'fi' => 'Finland',
		            'fr' => 'France',
		            'jp' => 'Japan',
		            'pl' => 'Poland',
		            'br' => 'Brazil',
		            'si' => 'Slovenia',
		            'se' => 'Sweden',
		            'cn' => 'China'
		        ]
		    ]
		];
		return [
		    'ns' => [
		        'parameters' => $params
		    ],
		    'pl' => [
		        'parameters' => $params
		    ],
		    'news' => [
		        'parameters' => $params
		    ],
		    'ps' => [
		        'parameters' => [
		            'keyword' => $params['keyword'],
		            'limit' => $params['limit']
		         ]
		    ]
		];
	}

	public function handle_request(array $params = []) {
		if(!isset($params['type'])) {
			$params['type'] = 'NS';
		}else {
			$params['type'] = strtoupper($params['type']);
		}

		if($params['type'] == 'PS' && isset($params['limit'])) {
			unset($params['limit']);
		}

		if(!isset($params['keyword']) || empty($params['keyword'])) {
			throw new RequestException('A keyword is required.');
			return false;
		}

		if(!isset($params['limit']) || $params['limit'] < 0) {
			$params['limit'] = 10;
		}
		if ($params['limit'] > 200) {
			throw new RequestException('Results limit exceded (200).');
		}

		if(!isset($params['start']) || $params['start'] < 0) {
			$params['start'] = 0;
		}

		if(!in_array($params['type'], $this->allowed_types)) {
			throw new RequestException('Google '.$params['type'].' search is not yet supported.');
		}

		if(!isset($params['loc'])) {
			$params['loc'] = 'us';
		}

		switch ($params['type']) {
			case 'NS' :
			case 'PL' :
			case 'PS' :
			case 'NEWS' :
				return $this->search_google($params['keyword'], $params['type'], $params['start'], $params['limit'], $params['loc']);
				break;

			default :
				throw new RequestException('A valid type must be specified.');
				break;
		}
	}

	public function is_request_similar($request1, $request2) {
		//domain
		if (strpos($request1, 'http://www.google.com') !== 0) {
			return false;
		}
		if (strpos($request2, 'http://www.google.com') !== 0) {
			return false;
		}

		$query1_position = strpos($request1, '?');

		$query1 = substr($request1, $query1_position + 1);

		$query2_position = strpos($request2, '?');
		$query2 = substr($request2, $query2_position + 1);

		$request1_parts = $this->parse_query($query1);
		$request2_parts = $this->parse_query($query2);

		//keyword
		if ($request1['q'] != $request2['q']) {
			return false;
		}

		//language
		if ($request1_parts['hl'] != $request2_parts['hl']) {
			return false;
		}

		//location
		if ($request1_parts['gl'] != $request2_parts['gl']) {
			return false;
		}

		//page number
		if (!isset($request1_parts['start'])) {
			$request1_parts['start'] = 0;
		}
		if (!isset($request2_parts['start'])) {
			$request2_parts['start'] = 0;
		}
		if ($request1_parts['start'] != $request2_parts['start']) {
			return false;
		}

		//We only deal with natural serp pages of 10 results
		if ($request1_parts['start']%10 != 0) {
			return false;
		}
		if ($request2_parts['start']%10 != 0) {
			return false;
		}
		if(isset($request1_parts['num']) && $request1_parts['num'] != 10) {
			return false;
		}
		if(isset($request2_parts['num']) && $request2_parts['num'] != 10) {
			return false;
		}

		//No tests failed, requests must be the same
		return true;
	}

	private function get_serp_url($keyword, $current_page_start, $gl){
		$q = rawurlencode($keyword)."&hl=en&gl=" . urlencode($gl) . "&start=".((int)$current_page_start)."&sa=N&num=" . $this->number_of_results_on_each_remote_page;
		return "https://www.google.com/search?q=" . $q;
	}

	protected function get_url($keyword, $type, $current_page_start, $loc_name){
		//check location if valid
		$loc_name = str_replace(' ','',$loc_name);
		$gl = $this->get_location($loc_name);
		//page is uniquely identified by the search query, and results start position,
		//with the exception of NEWS pages, which use a different html result than
		//other request types
		if ($type == 'NEWS') {
			$cache_key = 'NEWS:' . sha1($keyword) . ':' . $current_page_start . ':' . $gl;
			$request = "https://news.google.com/news?um=1&as_qdr=m&as_drrb=q&cf=all&ned=" . urlencode($gl) . "&hl=en&q=".rawurlencode($keyword)."&start=".((int)$current_page_start);
		} else {
			$cache_key = 'WEB:' . sha1($keyword) . ':' . $current_page_start . ':' . $gl;
			$request = $this->get_serp_url($keyword, $current_page_start, $gl);
		}
		
		$other_identifier = '';
		if (($colonPos = strpos($keyword, ":")) !== false) {
			$other_identifier = substr($keyword, 0, $colonPos + 1);
		}

		$dg_params = array(
			'cache_key' => $cache_key,
			'data_sources' => $this->format_data_sources_for_data_getter(),
			'url_getter_params' => array(
				'url' => $request,
				'other_identifier' => $other_identifier,
			)
		);
		return DataGetter::get_data_or_throw($this, $dg_params);
	}
	
	public function collectPaidSearchData($html) {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->preserveWhitespace = true;
		@$dom->loadHTML($html);
		$xpath = new DOMXPath($dom);
		
		// List selector
		$lists = $xpath->query('//li[@class="ads-ad"]');
		$data = [];
		
		foreach ($lists as $list) {
			$title = '';
			$title_html = '';
			$url = '';
			$link = '';
			$description1 = '';
			$description2 = '';
			
			$rowHtml = $dom->saveHTML($list);
			$rowDom = new DOMDocument('1.0', 'UTF-8');
			@$rowDom->loadHTML($rowHtml);
			$xpath = new DOMXPath($rowDom);
			
			$linkElemQuery = $xpath->query('//h3//a');
			$titleElem = $xpath->query('//h3')->item(0);
			$linkElem = $linkElemQuery->item(1);
			$descriptionElem = $xpath->query('//div[contains(@class,"ads-creative")]')->item(0);
			
			if(empty($linkElem)) {
				continue;
			}
			
			$title_html = $titleElem->ownerDocument->saveHTML($titleElem);
			$title = $linkElem->nodeValue;
			$link = 'https://www.google.com' . $linkElemQuery->item(0)->getAttribute('href');
			
			preg_match("|^(https?://.+)\?|", $linkElem->getAttribute('href'), $matches);
			if(!empty($matches[1])) {
				$url = $matches[1];
			}
			if(!empty($descriptionElem->nodeValue)) {
				$description1 = $descriptionElem->nodeValue;
			}
			if($url) {
				$data[] = compact('title', 'title_html', 'link', 'url', 'description1', 'description2');
			}
		}
		return $data;
	}
	
	public function collectData($html) {
		
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->preserveWhitespace = true;
		@$dom->loadHTML($html);
		$xpath = new DOMXPath($dom);

		// List selector
		$lists = $xpath->query('//div[@class="g"]');
		$data = [];
		
		foreach ($lists as $list) {
			$title = '';
			$title_html = '';
			$url = '';
			$link = '';
			$description1 = '';
			$description2 = '';
			
			$rowHtml = $dom->saveHTML($list);
			$rowDom = new DOMDocument('1.0', 'UTF-8');
			@$rowDom->loadHTML($rowHtml);
			$xpath = new DOMXPath($rowDom);
			
			$titleElem = $xpath->query('//h3[@class="r"]')->item(0);
			$linkElem = $xpath->query('//h3[@class="r"]//a')->item(0);
			$descriptionElem = $xpath->query('//span[@class="st"]')->item(0);
			
			if(empty($linkElem)) {
				continue;
			}
			
			$title_html = $titleElem->ownerDocument->saveHTML($titleElem);
			$title = $linkElem->nodeValue;
			$link = $linkElem->getAttribute('href');
			
			preg_match("|\?q=(https?://.+)&sa=|", $link, $matches);
			if(!empty($matches[1])) {
				$url = $matches[1];
				$link = 'https://www.google.com' . $link;
			} else {
				$url = $link;
				// Can't find google link, set as empty
				$link = '';
			}
			
			if(!empty($descriptionElem->nodeValue)) {
				$description1 = $descriptionElem->nodeValue;
			}
			
			if($url) {
				$data[] = compact('title', 'title_html', 'link', 'url', 'description1', 'description2');
			}
		}
		return $data;
	}
	
	public function collectNewsData($html) {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->preserveWhitespace = true;
		@$dom->loadHTML($html);
		$xpath = new DOMXPath($dom);
		
		// List selector
		$lists = $xpath->query('//div[contains(@class,"blended-wrapper")]//table[@class="esc-layout-table"]//td[@class="esc-layout-article-cell"]');
		$data = [];
		
		foreach ($lists as $list) {
			$title = '';
			$title_html = '';
			$link = '';
			$position = '';
			$excerpt = '';
			$time = '';
			$source = '';
			$additional_articles = [];
			
			
			$rowHtml = $dom->saveHTML($list);
			$rowDom = new DOMDocument('1.0', 'UTF-8');
			$rowDom->preserveWhitespace = true;
			@$rowDom->loadHTML($rowHtml);
			
			$xpath = new DOMXPath($rowDom);
			
			$titleElem = $xpath->query('//div[@class="esc-lead-article-title-wrapper"]//h2')->item(0);
			$linkElem = $xpath->query('//div[@class="esc-lead-article-title-wrapper"]//h2//a')->item(0);
			$excerptElem = $xpath->query('//div[@class="esc-lead-snippet-wrapper"]')->item(0);
			$sourceElem = $xpath->query('//td[@class="al-attribution-cell source-cell"]')->item(0);
			$timeElem = $xpath->query('//td[@class="al-attribution-cell timestamp-cell"]//span[@class="al-attribution-timestamp"]')->item(0);
			
			$title_html = $titleElem->ownerDocument->saveHTML($titleElem);
			$title = $titleElem->nodeValue;
			$link = $linkElem->getAttribute('href');
			$source = $sourceElem->nodeValue;
			$excerpt = $excerptElem->nodeValue;
			
			// Cleanup to remove unknown chars
			$time = preg_replace("|[^\s0-9A-Za-z,]|", '', $timeElem->nodeValue);
			$time = strtotime($time);
			
			// Additional articles
			$moreLists = $xpath->query('//div[contains(@class,"esc-secondary-article-wrapper")]');
			if($moreLists) {
				foreach ($moreLists as $moreList) {
					$moreListRowHtml = $rowDom->saveHTML($moreList);
					
					$moreListDom = new DOMDocument('1.0', 'UTF-8');
					$moreListDom->preserveWhitespace = true;
					@$moreListDom->loadHTML($moreListRowHtml);
					$moreListXpath = new DOMXPath($moreListDom);
					
					$_titleElem = $moreListXpath->query('//div[@class="esc-secondary-article-title-wrapper"]')->item(0);
					$_linkElem =  $moreListXpath->query('//div[@class="esc-secondary-article-title-wrapper"]//a')->item(0);
					$_sourceElem = $moreListXpath->query('//label[@class="esc-secondary-article-source"]')->item(0);
					
					$additional_articles[] = [
						'title' => $_titleElem->nodeValue,
						'link' => $_linkElem->getAttribute('href'),
						'source' => $_sourceElem->nodeValue
					];
				}
			}
			$data[] = compact('title', 'title_html', 'link', 'excerpt', 'source', 'time') + ['additional articles' => $additional_articles];
		}
		return $data;
	}
	

	public function search_google($keyword, $type, $start, $limit, $loc_name) {
		$type = strtoupper($type);
		
		//how many results do we want?
		$max = $limit;

		//and starting from what offset?
		$start = $start;

		//Calculate how many pages of results we need
		$number_of_pages_required = ceil(($start + $max)/$this->number_of_results_on_each_remote_page);

		//Reset the previous search count
		$this->previous_result_count = 0;

		//if no results are required, get the first page anyway so we can get the total
		if ($number_of_pages_required == 0) {
			$number_of_pages_required = 1;
		}

		if ($max > 200) {
			throw new RequestException('Results limit exceded (200)');
		}

		$data = array();
		$total_available = false;
		//calculate the first page number. The equivalent offset should be $start or lower.
		$first_page = (int)floor( $start / $this->number_of_results_on_each_remote_page );
		$max_pages_available = false;
		$position_offset_due_to_fewer_remote_results_than_expected = 0;

		for ($i = $first_page; ($i < $number_of_pages_required && ($max_pages_available === false || $i < $max_pages_available)); $i++) {
			$current_page_offset = $i * $this->number_of_results_on_each_remote_page;

			//Get a page
			$html = $this->get_url($keyword, $type, $current_page_offset, $loc_name);

			//Get total consistent with the first and/or second page (i.e. definitely
			// get the first, but override with the second if we get that far).
			if ($i == $first_page || $i == $first_page + 1) {
				$total_available = $this->get_total($html);
				settype($total_available, 'int');
			}

			//if we just wanted the total, we've already go it
			if ($type == 'PL') {
				return $total_available;
			}

			//if we've come too far... (shouldn't occur, but just in case)
			if ($current_page_offset > $total_available) {
				break;
			}

			//if there are fewer results available than we intend to get
			//WARNING - google doesn't allow fetching over about 1000 results anyway, so using the total
			//as a limit is arbitrary in most cases
			if($total_available < ($start+$max)) {
				$max_pages_available = ceil( $total_available / $this->number_of_results_on_each_remote_page );
			}

			//get the results
			if($type == 'NS') {
				$posts = $this->collectData($html);
			} elseif($type == 'PS') {
					$posts = $this->collectPaidSearchData($html);
			} elseif ($type == 'NEWS'){
				$posts = $this->collectNewsData($html);
			}
			
			
			//each potential result
			$position_on_this_page = 0;
			
			foreach ($posts as &$post) {
				if ($type == 'NS') {
					$position = $current_page_offset + $position_on_this_page + $position_offset_due_to_fewer_remote_results_than_expected;
					$position_on_this_page++;
					$position = (int)$position;
					//haven't reached a result we want yet
					if ($position < $start)
						continue;

					//already have all of the results we want
					if ($position > $start + $max-1)
						break;

          			$datum = array('Title' => $post['title'], 'Url' => $post['url'], 'Position' => $position);
					$datum['SerpLink'] = $this->get_serp_url($keyword, $current_page_offset, $this->get_location($loc_name));

					$data[] = $datum;

				}
				elseif ($type == 'PS') {
					$position = $current_page_offset + $position_on_this_page;
					$position_on_this_page++;

					//already have all of the results we want
					if ($position > $start + $max - 1) {
						break;
					}

					$data[] = $post;
				}
				elseif ($type == 'NEWS') {
					if(!isset($number_on_page)) {
						$number_on_page = 0;
					} else {
						$number_on_page++;
					}
					$position = $current_page_offset + $number_on_page;
					if ($position < $start) {
						continue;
					}
					if ($position >= $start + $max) {
						break;
					}
					
					$post['position'] = $position;
					$data[] = $post;
				}
				else {
					continue;
				}
			}

			// This block of code helps handle when Google doesn't give us the expected
			// number of results on each page.
			if ($type == 'NS') {

				/* Leave this here to save time when debugging. Google changes their SERP often. *
				var_dump('were there enough results?',
					$position_on_this_page < $this->number_of_results_on_each_remote_page,
					count($data) < $limit,
					($current_page_offset + $position_on_this_page) < $total_available,
					$i == $number_of_pages_required-1
				);
				/**/

				//sometimes a page doesn't give us the number of results it was supposed to.
				//if there are more results, haven't got enough yet, and either we're about to stop,
				//only allow this to occur if the previous search actually got some results
				if (count($data) < $limit
					&& ($current_page_offset + $position_on_this_page) < $total_available
					&& $i == $number_of_pages_required-1) {
						if (count($data) <>  $this->previous_result_count) {
							$this->previous_result_count = count($data);
							$number_of_pages_required++;
						}
				}

				//expect a negative number. e.g. if there were 8 results instead of 10, add -2 to the offset. (latest result will be position 7, but position will be set to 8 (incremented after the result)
				if ($position_on_this_page < $this->number_of_results_on_each_remote_page) {
					$position_offset_due_to_fewer_remote_results_than_expected += $position_on_this_page - $this->number_of_results_on_each_remote_page;
				}
			}
		}
		
		//return the appropriate results
		switch ($type) {
			case 'PL':
				return $total_available;
				
			case 'PS':
			case 'NEWS':
				return array('Results' => $data);
			
			default:
				return array('Total' => $total_available, 'Results' => $data);
		}
	}

	public function get_total($html){
		$total = 0;
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->preserveWhitespace = true;
		@$dom->loadHTML($html);
		$xpath = new DOMXPath($dom);
		$resultStatsElem = $xpath->query("//*[@id='resultStats']")->item(0);

		if($resultStatsElem) {
			preg_match_all('/\d+/', $resultStatsElem->nodeValue, $matches);
            $total = intval(implode($matches[0]));
            $total =  number_format($total, 0, '.', ''); 
		}
		return (int) $total;
	}

	public function get_location($loc_name) {
		$loc_name = strtolower($loc_name);
		$locations = $this->allowed_location;
		reset($locations);
		$gl = false;
		while (list($tmpkey,$tmpval) = each ($locations)){
			if(is_array($locations[$tmpkey])) {
				if ($loc_name == strtolower($locations[$tmpkey]['Name']) || $loc_name == $locations[$tmpkey]['GL']) {
					$gl = $locations[$tmpkey]['GL'];
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
