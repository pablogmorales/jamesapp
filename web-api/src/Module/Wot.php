<?php

namespace Daytalytics\Module;

use Daytalytics\DataGetter;
use Daytalytics\RequestException;
use Daytalytics\ProxyServer;

/**
 *  Web Of Trust (WOT) server module
 * @see https://www.mywot.com/wiki/API
 */
class Wot extends BaseModule {

    protected $endPoint = 'http://api.mywot.com/0.4/public_link_json2?hosts=%s/&key=%s';
    
    protected $apiKey = '';
    
    protected $useProxies = false;

    public function define_service() {
        return [
            'domaintrustworthinessmetric' => [
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
     * @param $params
     *
     * @return mixed
     * @throws RequestException
     */
    public function handle_request(array $params = []) {
        if (empty($params['domain'])) {
            throw new RequestException('Missing domain');;
        }
        switch ($params['type']) {
            case 'domaintrustworthinessmetric':
                $result = $this->getTrustworthinessMetric($params['domain']);
                break;
            default:
                throw new RequestException('Invalid request type');
                break;
        }
        return $result;
    }

    public function getTrustworthinessMetric($domain) {
        if (empty($domain)) {
            throw new RequestException('Missing domain');
        }
        $domain = strtolower($domain);
        $requestUrl = sprintf($this->endPoint, $domain, $this->apiKey);
        $result = $this->request($requestUrl);

        if ($result) {
            $result = json_decode($result, true);
            if (!empty($result[$domain])) {
                $output = array();
                $data = $result[$domain];
                if (!empty($data[0])) {
                    $output['trustworthiness'] = array(
                        'score' => $data[0][0],
                        'reputation' => $this->getWorthinessInfo($data[0][0]),
                        'confidence' => $data[0][1]
                    );
                }
                if (!empty($data[4])) {
                    $output['childsafety'] = array(
                        'score' => $data[4][0],
                        'reputation' => $this->getWorthinessInfo($data[4][0]),
                        'confidence' => $data[4][1]
                    );
                }
                if (!empty($data['categories'])) {
                    $output['category'] = $this->getCategoryInfo($data['categories']);
                }
                return $output;
            }
        }
        throw new RequestException('Data not available');
    }

    protected function getWorthinessInfo($score) {
        $result = 'Very poor';
        if (20 <= $score && 40 > $score) {
            $result = 'Poor';
        } elseif (40 <= $score && 60 > $score) {
            $result = 'Unsatisfactory';
        } elseif (60 <= $score && 80 > $score) {
            $result = 'Good';
        } elseif ($score >= 80) {
            $result = 'Excellent';
        }
        return $result;
    }

    protected function getCategoryInfo($category) {
        $categories = array(
            101 => array(
                'group' => 'negative',
                'description' => 'Malware or viruses'
            ),
            102 => array(
                'group' => 'Negative',
                'description' => 'Poor customer experience'
            ),
            103 => array(
                'group' => 'Negative',
                'description' => 'Phishing'
            ),
            104 => array(
                'group' => 'Negative',
                'description' => 'Scam'
            ),
            105 => array(
                'group' => 'Negative',
                'description' => 'Potentially illegal'
            ),
            201 => array(
                'group' => 'Questionable',
                'description' => 'Misleading claims or unethical'
            ),
            202 => array(
                'group' => 'Questionable',
                'description' => 'Privacy risks'
            ),
            203 => array(
                'group' => 'Questionable',
                'description' => 'Suspicious'
            ),
            204 => array(
                'group' => 'Questionable',
                'description' => 'Hate, discrimination'
            ),
            205 => array(
                'group' => 'Questionable',
                'description' => 'Spam'
            ),
            206 => array(
                'group' => 'Questionable',
                'description' => 'Potentially unwanted programs'
            ),
            207 => array(
                'group' => 'Questionable',
                'description' => 'Ads / pop-ups'
            ),
            301 => array(
                'group' => 'Neutral',
                'description' => 'Online tracking'
            ),
            302 => array(
                'group' => 'Neutral',
                'description' => 'Alternative or controversial medicine'
            ),
            303 => array(
                'group' => 'Neutral',
                'description' => 'Opinions, religion, politics'
            ),
            304 => array(
                'group' => 'Neutral',
                'description' => 'Other'
            ),
            401 => array(
                'group' => 'Negative',
                'description' => 'Adult content'
            ),
            402 => array(
                'group' => 'Questionable',
                'description' => 'Incidental nudity'
            ),
            403 => array(
                'group' => 'Questionable',
                'description' => 'Gruesome or shocking'
            ),
            404 => array(
                'group' => 'Positive',
                'description' => 'Site for kids'
            ),
            501 => array(
                'group' => 'Positive',
                'description' => 'Good site'
            )
        );
        $code = array_keys($category)[0];
        $confidence = array_values($category)[0];
        $result = $categories[$code] + array('confidence' => $confidence);
        return $result;
    }

    protected function request($requestUrl) {
        $cacheKey = sha1($requestUrl);
        $dataGetterParams = array(
            'cache_key' => $cacheKey,
            'data_sources' => $this->format_data_sources_for_data_getter(),
            'url_getter_params' => array(
                'url' => $requestUrl
            )
        );
        return DataGetter::get_data_or_throw($this, $dataGetterParams);
    }
}
