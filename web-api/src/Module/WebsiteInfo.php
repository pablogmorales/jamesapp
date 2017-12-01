<?php

namespace Daytalytics\Module;

use Daytalytics\DataGetter;
use Daytalytics\RequestException;
use Exception;
use DOMDocument;
use DOMXPath;

/**
  * WebsiteInfo server module
  */

class WebsiteInfo extends BaseModule {
    
    protected $params = [];
    
    public function define_service() {
        return [
            'extractwebsiteinfobydomain' => [
                'parameters' => [
                    'domain' => [
                        'description' => 'Domain to check e.g. example.com',
                        'required' => true
                    ]
                ]
            ]
        ];
    }

    /**
     *
     * @param unknown $params
     * @throws RequestException
     * @return multitype:
     */
    public function handle_request(array $params = []) {
        $this->setParams($params);
        
        switch (@$params['type']) {
            case 'extractwebsiteinfobydomain':
                return $this->extractWebsiteInfoByDomain(@$params['domain']);
                break;
            default:
                throw new RequestException('A valid type is required');
                break;
        }
    }

    public function extractWebsiteInfoByDomain($domain) {
        if(empty($domain)) {
            throw new RequestException('Domain is required');
        }
        
        $requestUrl = 'http://' . $domain;
        $cacheKey = sha1('WebsiteInfo_extractWebsiteInfoByDomain_' . $requestUrl);
        $cacheData = $this->db->get_parsed_data($cacheKey, '-1 day', $this->identify());
        if($cacheData && $data = unserialize($cacheData)) {
        	return $data;
        }
        
        $htmlContent = '';
		try {
        	$htmlContent = $this->request($requestUrl);
		} catch (Exception $e) {
			throw new RequestException('Invalid domain');
		}
		
		if($htmlContent) {
			$result = $this->parseWebPageContent($htmlContent);
			$this->db->set_parsed_data($cacheKey, serialize($result), $this->identify());
			return $result;
		}
		
        throw new RequestException('Data not available.');
    }
    
    public function setParams($params) {
        $this->params = $params;
    }

    protected function parseWebPageContent($content) {
        $domHtml = new DOMDocument('1.0', 'UTF-8');
        $domHtml->preserveWhitespace = true;
        @$domHtml->loadHTML($content);

        $phones = $this->parsePhoneNumbers($domHtml);
        $addresses = $this->parseAddresses($domHtml);

        if (empty($phones) && empty($addresses)) {
            $urls = $this->parseAboutContactUrls($domHtml);
                        
            if (!empty($urls)) {
                foreach($urls as $url) {
                    $content = $this->request($url);
                    if (!empty($content)) {
                        @$domHtml->loadHTML($content);
                        
                        if(empty($phones) && $_phones = $this->parsePhoneNumbers($domHtml)) {
                        	$phones = array_merge($phones, $_phones);
                        }
                        
                        if(empty($addresses) && $_addresses = $this->parseAddresses($domHtml)) {
                        	$addresses = array_merge($addresses, $_addresses);
                        }
                        
                        if (!empty($phones) && !empty($addresses)) {
                            break;
                        }
                    }
                }
            }
        }
        
        $phones = array_unique($phones);
        $addresses = array_unique($addresses);
        
        return compact('phones', 'addresses');
    }

    public function parsePhoneNumbers(DOMDocument $domHtml) {
        $results = array();
        $xpath = new DOMXPath($domHtml);
        $phones = $xpath->query("//*[@itemprop='telephone']");
        foreach($phones as $phone) {
            $results[] = trim($phone->nodeValue);
        }
        return $results;
    }

    public function parseAddresses(DOMDocument $domHtml) {
        $results = array();
        $xpath = new DOMXPath($domHtml);
        $addresses = $xpath->query("//*[@itemprop='address']");
        foreach($addresses as $address) {
            $results[] = trim($address->nodeValue);
        }
        if (empty($results)) {
            $addresses = $xpath->query("//*[@itemtype='http://schema.org/PostalAddress']");
            foreach($addresses as $address) {
                $results[] = trim($address->nodeValue);
            }
        }
        return $results;
    }

    public function parseAboutContactUrls(DOMDocument $domHtml) {
        $results = array();
        $xpath = new DOMXPath($domHtml);
        $anchors = $xpath->query("//a");
        foreach($anchors as $anchor) {
            $text = $anchor->textContent;
            $href = trim($anchor->getAttribute('href'));
            
            // Collect urls for possible contact info
            if(preg_match("/contact|about|support/i", $text) || preg_match("/contact|about|support/i", $href)) {
            	if(!preg_match("/^http/", $href)) {
            		// See if needed slash
            		$slash = !preg_match("/^\//", $href) ? '/' : '';		
            		// Prepend http for later url evaluation
            		$href = "http://{$this->params['domain']}{$slash}{$href}";
            	} else {
            		// URL must be within the site/domain (no external)
            		if(!preg_match("/{$this->params['domain']}/i", $href)) {
            			continue;
            		}
            	}
            	$results[] = $href;
            }
        }
        return array_unique($results);
    }

    protected function request($requestUrl) {
        $cacheKey = sha1("WebsiteInfo_" . $requestUrl);
        $dataGetterParams = array(
            'cache_key' => $cacheKey,
            'data_sources' => $this->format_data_sources_for_data_getter(),
            'url_getter_params' => array(
                'url' => $requestUrl,
            	'user_agent' => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:44.0) Gecko/20100101 Firefox/44.0', // newer browser agent
                'curl_options' => array(
                    CURLOPT_FOLLOWLOCATION => true
                )
            )
        );

        return DataGetter::get_data_or_throw($this, $dataGetterParams);
    }
}
