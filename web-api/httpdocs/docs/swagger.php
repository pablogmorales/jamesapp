<?php 

$rootPath = dirname(dirname(__DIR__));

require $rootPath . '/config/bootstrap.php';

$swaggerJSON = file_get_contents($rootPath . '/spec/swagger.json');

$swagger = json_decode($swaggerJSON, true);

$baseUrl = getenv('APP_BASE_URL');

$urlComponents = parse_url($baseUrl);

/**
 *
 * @param unknown $spec
 * @return string
 */
function loadDocs($spec){
    if (is_array($spec)) {
        foreach ($spec as &$val) {
            $val = loadDocs($val);
        }
    } elseif (is_string($spec) && strpos($spec, '@link') === 0) {
        list(, $docPath) = explode('@link', $spec);
        $docFile = dirname(__DIR__) . trim($docPath);
        if (file_exists($docFile)) {
            $spec = file_get_contents($docFile);
        }
    }
    return $spec;
};


/**
 * Set URL parts
 */
$swagger['host'] = $urlComponents['host'];
$swagger['basePath'] = $urlComponents['path'];
$swagger['schemes'] = [$urlComponents['scheme']];

/**
 * Set security options to interface with custom auth hanlding in daytalytics/daytalytics.js
 */
$swagger['securityDefinitions'] = [
    "Token" => [
        "type" => "apiKey",
        "name" => "Auth-Token",
        "in" => "query"
    ],
    "Secret" => [
        "type" => "apiKey",
        "name" => "Auth-Secret",
        "in" => "query"
    ]
];

/**
 * ...other stuff to force...
 */


/**
 * Apply any doc embeds
 */
$swagger = loadDocs($swagger);


/**
 * Output
 */
header('Content-Type: application/json');
echo json_encode($swagger);
?>