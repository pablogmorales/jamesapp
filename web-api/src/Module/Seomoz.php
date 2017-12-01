<?php

namespace Daytalytics\Module;

use Daytalytics\DataGetter;
use Daytalytics\RequestException;

class Seomoz extends BaseModule {
    //APT auth token
    private $aptAuthToken = '';

    //how many link-data results to fetch at a time
    const link_data_page_size = 250;

    //See here for a description of each of these flags:
    //http://apiwiki.seomoz.org/w/page/13991145/Request-Response%20Format

    protected $url_metric_col_bit_flags = array(
        'Title' => 1,
        'ut' => 1,

        'URL' => 4,
        'uu' => 4,

        'Subdomain' => 8,
        'ufq' => 8,

        'Root Domain' => 16,
        'upl' => 16,

        'External Links' => 32,
        'ueid' => 32,

        'Subdomain External Links' => 64,
        'feid' => 64,

        'Root Domain External Links' => 128,
        'peid' => 128,

        'Juice-Passing Links' => 256,
        'ujid' => 256,

        'Subdomains linking' => 512,
        'uifq' => 512,

        'Root Domains Linking' => 1024,
        'uipl' => 1024,

        'Links' => 2048,
        'uid' => 2048,

        'Subdomain Subdomains Linking' => 4096,
        'fid' => 4096,

        'Root Domain Root Domains Linking' => 8192,
        'pid' => 8192,

        'mozRank' => 16384,
        'umrp' => 16384,
        'umrr' => 16384,

        'Subdomain mozRank' => 32768,
        'fmrp' => 32768,
        'fmrr' => 32768,

        'Root Domain mozRank' => 65536,
        'pmrp' => 65536,
        'pmrr' => 65536,

        'mozTrust' => 131072,
        'utrp' => 131072,
        'utrr' => 131072,

        'Subdomain mozTrust' => 262144,
        'ftrp' => 262144,
        'ftrr' => 262144,

        'Root Domain mozTrust' => 524288,
        'ptrp' => 524288,
        'ptrr' => 524288,

        'External mozRank' => 1048576,
        'uemrp' => 1048576,
        'uemrr' => 1048576,

        'Subdomain External Domain Juice' => 2097152,
        'fejp' => 2097152,
        'fejr' => 2097152,

        'Root Domain External Domain Juice' => 4194304,
        'pejp' => 4194304,
        'pejr' => 4194304,

        'Subdomain Domain Juice' => 8388608,
        'fjp' => 8388608,
        'fjr' => 8388608,

        'Root Domain Domain Juice' => 16777216,
        'pjp' => 16777216,
        'pjr' => 16777216,

        'HTTP Status Code' => 536870912,
        'us' => 536870912,

        'Links to Subdomain' => 4294967296,
        'fuid' => 4294967296,

        'Links to Root Domain' => 8589934592,
        'puid' => 8589934592,

        'Root Domains Linking to Subdomain' => 17179869184,
        'fipl' => 17179869184,

        'Page Authority' => 34359738368,
        'upa' => 34359738368,

        'Domain Authority' => 68719476736,
        'pda' => 68719476736,
    );

    protected $anchor_text_metric_col_bit_flags = array(
        't' => 2,
        'iu' => 8,
        'if' => 16,
        'eu' => 32,
        'ef' => 64,
        'ep' => 128,
        'imp' => 256,
        'emp' => 512,
        'f' => 1024
    );

    protected $link_metric_col_bit_flags = array(
        // These are unknown
        'luuu' => 0,
        'luut' => 0,
        'luus' => 0,
        'luupl' => 0,
        'luupa' => 0,
        'luumrr' => 0,
        'luumrp' => 0,
        'luuid' => 0,
        'luufq' => 0,
        'lrid' => 0,
        'lsrc' => 0,
        'ltgt' => 0,
        'lufmrp' => 0,
        'lufmrr' => 0,
        'lupda' => 0,
        'luueid' => 0,
        'lufrid' => 0,

        'Flags' => 2,
        'lf' => 2,

        'Anchor Text' => 4,
        'lt' => 4,

        'mozRank Passed' => 16,
        'lmrp' => 16,
        'lmrr' => 16,
    );

    private $link_bit_flags = array (
        "no-follow" => 1,
        "same-subdomain" => 2,
        "meta-refresh" => 4,
        "same-ip-address" => 8,
        "same-c-block" => 16,
        "301-redirect" => 64,
        "302-redirect" => 128,
        "no-script" => 256,
        "off-screen" => 512,
        "meta-no-follow" => 2048,
        "same-root-domain" => 4096,
        "feed-autodiscovery" => 16384,
        "rel-canonical" => 32768,
        "via-301" => 65536
    );

    private $link_scopes = array (
        'page_to_page',
        'page_to_subdomain',
        'page_to_domain',
        'domain_to_page',
        'domain_to_subdomain',
        'domain_to_domain'
    );

    private $link_sorts = array (
        'page_authority',
        'domain_authority',
        'domains_linking_domain',
        'domains_linking_page'
    );

    private $link_filters = array (
        'internal',
        'external',
        'nofollow',
        'follow',
        '301',
    );

    private $anchor_text_scopes = array (
        'phrase_to_page',
        'phrase_to_subdomain',
        'phrase_to_domain',
        'term_to_page',
        'term_to_subdomain',
        'term_to_domain'
    );

    private $anchor_text_scope_to_prefix_map = array (
        'phrase_to_page' => 'apu',
        'phrase_to_subdomain' => 'apf',
        'phrase_to_domain' => 'app',
        'term_to_page' => 'atu',
        'term_to_subdomain' => 'atf',
        'term_to_domain' => 'atp'
    );


    public function define_service() {
        $params = [
            'url' => [
                'description' => 'The subject url',
                'required' => true
            ],
            'scope' => [
                'description' => 'Scope',
                'options' => $this->link_scopes
            ],
            'sort' => [
                'description' => 'Sort',
                'options' => $this->link_sorts
            ],
            'filter' => [
                'description' => 'Filters',
                'options' => $this->link_filters,
                'type' => 'array',
                'collectionFormat' => 'csv',
                'items' => [
                    'type' => 'string'
                ]
            ],
            'limit' => [
                'description' => 'Limit',
                'minimum' => 1,
                'maximum' => 1000,
                'default' => 50
            ],
            'offset' => [
                'description' => 'Offset',
                'default' => 0
            ]
        ];
        
        return [
            'url-metrics' => [
                'parameters' => [
                    'url' => $params['url']
                ]
            ],
            'link-data' => [
                'parameters' => $params
            ],
            'anchor-text-data' => [
                'parameters' => [
                    'url' => $params['url'],
                    'scope' => [
                        'description' => 'Scope',
                        'options' => $this->anchor_text_scopes
                    ]
                ]
            ]
        ];
    }

    public function handle_request(array $params = []) {
        //TT api key and username
        $apiKey 	 = '';
        $apiUsername = '';
        // Set api key and username constants
        if (!defined('api_key')) {
        	define('api_key', $apiKey);
        }
        if (!defined('api_username')) {
        	define('api_username', $apiUsername);
        }
        if (empty($params['type']))
            throw new RequestException("Invalid type");

        switch ($params['type']) {
            case 'url-metrics':
                if (empty($params['url']))
                    throw new RequestException("Url required.");

                return $this->get_url_metrics($params['url']);

            case 'link-data':
                if (empty($params['url']))
                    throw new RequestException("Url required.");

                if (empty($params['scope']))
                    $params['scope'] = $this->link_scopes[0];

                if (!in_array($params['scope'], $this->link_scopes))
                    throw new RequestException("Invalid scope.");

                if (empty($params['sort']))
                    $params['sort'] = $this->link_sorts[0];

                if (!in_array($params['sort'], $this->link_sorts))
                    throw new RequestException("Invalid sort.");

                if (!empty($params['filter'])) {
                    foreach($params['filter'] as $filter) {
                        if (!in_array($filter, $this->link_filters)) {
                            throw new RequestException("Invalid filter: $filter");
                        }
                    }
                } else {
                    $params['filter'] = [];
                }

                if (empty($params['limit'])) {
                    $params['limit'] = 50;
                } else {
                    if ($params['limit'] > 1000 || $params['limit'] < 1 ) {
                        throw new RequestException("Invalid limit");
                    }
                }

                if (empty($params['offset']))
                    $params['offset'] = 0;

                return $this->get_link_data($params);

            case 'anchor-text-data':
                if (empty($params['scope']))
                    $params['scope'] = $this->anchor_text_scopes[0];

                if (!in_array($params['scope'], $this->anchor_text_scopes))
                    throw new RequestException("Invalid scope.");

                if (empty($params['url']))
                    throw new RequestException("Url required.");

                return $this->get_anchor_text_data($params);

            default:
                throw new RequestException("Invalid type");
                break;
        }
    }

    function get_url_metrics($url) {
        // If url is string then convert it into an array
        if (!is_array($url)) {
            if (strpos($url, ',')) {
                $url = explode(',', $url);

                foreach ($url as $key => $value) {
                    if (empty($value)){
                        unset($url[$key]);
                    }
                }
            } else {
                $url = array($url);
            }
        }

        // Discard urls if count is greater than 10
        if (count($url) > 10) {
            $urlChunk = array_chunk($url, 10, true);
            $url      = $urlChunk[0];
        }

        $fields_required = array(
            'fmrp',
            'fmrr',
            'pda',
            'ueid',
            'ufq',
            'uid',
            'umrp',
            'umrr',
            'upa',
            'upl',
            'us',
            'ut',
            'uu',
            'utrp',
            'utrr',
        );
        $fields_bits = array_intersect_key($this->url_metric_col_bit_flags, array_fill_keys($fields_required, false));
        $cols = 0;

        // @todo - this flag forms an int value larger than 32bit. Likely only works
        // on 64bit servers. Harden this!
        foreach ($fields_bits as $field_name => $bitmask_64bit) {
            $cols |= $bitmask_64bit;
        }

        $apiUrl = "http://lsapi.seomoz.com/linkscape/url-metrics/";
        if (count($url) == 1) {
            $apiUrl .= urlencode($url[0]);
        }
        
        $apiUrl .= "?Cols=". $cols; 
        //$apiUrl .= "?Cols=103616268349";

        $dg_params = array(
            'cache_key' => "$apiUrl:url-metrics",
            'data_sources' => $this->format_data_sources_for_data_getter(array("local", "live")),
            'url_getter_params' => array(
                'url' => $apiUrl,
                'curl_options' => array (
                    CURLOPT_USERPWD => api_username . ":" . api_key
                ),
                'user_agent' => 'Daytalytics API, Doubledot Media (doubledotmedia.com/contact)',
            ),
        );

        if (count($url) > 1) {
            $dg_params['url_getter_params'] += array('post_data' => json_encode($url));
        }

        $data = DataGetter::get_data_or_throw($this, $dg_params);
        return $this->parse_url_metrics($data);
    }

    function parse_url_metrics($data) {
        $metrics = json_decode($data);
        $results = array();

        if (is_array($metrics)) {
            foreach ($metrics as $key => $metric) {
                $results[] =  $this->build_url_metrics($metric);
            }
            return $results;
        } else {
            return $this->build_url_metrics($metrics);
        }
    }

    function get_link_data($params) {
        $results = array();

        $page = 0;

        $page_size = self::link_data_page_size;
        // allow small chunks of data to be fetched
        if ($params['limit'] <= 5) {
            $page_size = 5;
        }
        elseif ($params['limit'] <= 10) {
            $page_size = 10;
        }

        while( ($page * $page_size) < $params['limit']) {

            // @todo - don't allow this offset to causes a block to start/end
            // at a non-standard value
            $offset = (($page * $page_size) + $params['offset']);

            $url  = "http://lsapi.seomoz.com/linkscape/links/" . urlencode($params['url']);
            $url .= "?Scope={$params['scope']}";
            $url .= "&Sort={$params['sort']}";
            $url .= "&Limit=" . $page_size;
            $url .= "&Offset=" . $offset;
            $url .= "&Filter=" . implode("+", $params['filter']);
            $url .= "&LinkCols=22";
            $url .= "&SourceCols=133982846973";
            $url .= "&TargetCols=133982846973";




            $dg_params = array(
                'cache_key' => $url,
                'data_sources' => $this->format_data_sources_for_data_getter(array("local", "live")),
                'url_getter_params' => array(
                    'url' => $url,
                    'curl_options' => array (
                        CURLOPT_USERPWD => api_username . ":" . api_key,
                        CURLOPT_TIMEOUT => 0,
                    ),
                    'proxy_request_options' => array (
                        'timeout' => 0,
                    ),
                ),
            );

            $data = DataGetter::get_data_or_throw($this, $dg_params);

            $results = array_merge($results, $this->parse_link_data($data));
            $page += 1;
        }

        //dont return more results than requested - even if we download more
        if ($params['limit'] < count($results))
            $results = array_slice($results, 0, $params['limit']);

        return $results;

    }

    function parse_link_data($data) {
        $links = json_decode($data);

        $to_return = array();
        foreach($links as $link) {
            $datum = array (
                'subdomain-mozrank-pretty' => @$link->fmrp,
                'subdomain-mozrank-raw' => @$link->fmrr,
                'page-title' => @$link->ut,
                'url' => @$link->uu,
                'root-domain' => @$link->upl,
                'http-status-code' => @$link->us,
                'mozrank-pretty' => @$link->umrp,
                'mozrank-raw' => @$link->umrr,
                'page-authority' => @$link->upa,
                'domain-authority' => @$link->pda,
                'number-of-juice-passing-external-links-to-url' => @$link->ueid,
                'subdomain' => @$link->ufq,
                'total-number-of-links-to-url' => @$link->uid,
                'link-target' => @$link->luuu,
                'link-title' => @$link->luut,
                'link-status' => @$link->luus,
                'pay-level-domain' => @$link->luupl,
                'link-page-authority' => @$link->luupa,
                'link-mozrank-raw' => @$link->luumrr,
                'link-mozrank-pretty' => @$link->luumrp,
                'total-number-of-links-to-target-link' => @$link->luuid,
                'link-domain' => @$link->luufq,
                'internal-id-of-link' => @$link->lrid,
                'internal-id-of-source-url' => @$link->lsrc,
                'internal-id-of-target-url' => @$link->ltgt,
                'link-subdomain-mozrank-pretty' => @$link->lufmrp,
                'link-subdomain-mozrank-raw' => @$link->lufmrr,
                'link-pay-level-domain-page-authority' => @$link->lupda,
                'link-number-of-juice-passing-external-links' => @$link->luueid,
                'lufrid' => @$link->lufrid,
                'anchor-text' => @$link->lt,
                'frid' => @$link->frid,
                'prid' => @$link->prid,
            );

            foreach ($this->link_bit_flags as $name => $mask) {
                $datum[$name] = ($link->lf & $mask) == $mask;
            }

            $to_return[] = $datum;
        }

        return $to_return;
    }

    function get_anchor_text_data($params) {

        $fields_required = array(
            't',
            'iu',
            'if',
            'eu',
            'ef',
            'ep',
            'imp',
            'emp',
            'f',
        );
        $fields_bits = array_intersect_key($this->anchor_text_metric_col_bit_flags, array_fill_keys($fields_required, false));
        $cols = 0;
        foreach ($fields_bits as $field_name => $bitmask) {
            $cols |= $bitmask;
        }

        $url = "http://lsapi.seomoz.com/linkscape/anchor-text/" . urlencode($params['url']);
        $url .= "?Scope=" . $params['scope'];
        $url .= "&Sort=domains_linking_page";
        $url .= "&Cols=" . $cols;

        $dg_params = array(
            'cache_key' => "{$params['url']}:anchor-text-data:{$params['scope']}",
            'data_sources' => $this->format_data_sources_for_data_getter(array("local", "live")),
            'url_getter_params' => array(
                'url' => $url,
                'curl_options' => array (
                    CURLOPT_USERPWD => api_username . ":" . api_key
                ),
            ),
        );
        $data = DataGetter::get_data_or_throw($this, $dg_params);
        return $this->parse_anchor_text_data($data, $params['scope']);
    }

    function parse_anchor_text_data($data, $scope) {
        $anchor_data = json_decode($data);
        $scope_prefix = $this->anchor_text_scope_to_prefix_map[$scope];
        $to_return = array();

        foreach($anchor_data as $anchor) {
            $to_return[] = array (
                'phrase' => $anchor->{$scope_prefix . 't'},
                'internal-pages-linking' => $anchor->{$scope_prefix . 'iu'},
                'internal-subdomains-linking' => $anchor->{$scope_prefix . 'if'},
                'external-pages-linking' => $anchor->{$scope_prefix . 'eu'},
                'external-subdomains-linking' => $anchor->{$scope_prefix . 'ef'},
                'external-root-domains-linking' => $anchor->{$scope_prefix . 'ep'},
                'internal-mozrank-passed' => $anchor->{$scope_prefix . 'imp'},
                'external-mozrank-passed' => $anchor->{$scope_prefix . 'emp'},
            );
        }

        return $to_return;
    }

  /**
     * @name is_valid_response()
     * @description test whether the response is expected
     */
    function is_valid_response($response) {
        if (!empty($response)) {
            return true;
        }
        return false;
    }

    function build_url_metrics($metric) {
        $urlMetrics = array (
                       'subdomain-mozrank-pretty' => @$metric->fmrp,
                       'subdomain-mozrank-raw' => @$metric->fmrr,
                       'domain-authority' => @$metric->pda,
                       'number-of-juice-passing-external-links-to-url' => @$metric->ueid,
                       'subdomain' => @$metric->ufq,
                       'total-number-of-links-to-url' => @$metric->uid,
                       'mozrank-pretty' => @$metric->umrp,
                       'mozrank-raw' => @$metric->umrr,
                       'moztrust-pretty' => @$metric->utrp,
                       'moztrust-raw' => @$metric->utrr,
                       'page-authority' => @$metric->upa,
                       'root-domain' => @$metric->upl,
                       'http-status-code' => @$metric->us,
                       'page-title' => @$metric->ut,
                       'url' => @$metric->uu
                      );
        return $urlMetrics;
    }

}
