<?php
namespace Daytalytics\Spec;

use Daytalytics\Module\BaseModule;
use Daytalytics\ModuleRegistry;

class Service {
    
    protected $module;
    
    protected $identity = [];
    
    protected $specName = '';
    
    protected $specTemplate = '<?php
namespace Daytalytics\Spec\Service;
        
%s
    
class %s {

    public static $name = "%s";
    
    public static $services = %s;

}
';

    protected $annotationTemplate = '/**
 * @SWG\Tag(
 *   name=%s,
 *   description=%s
 * )
 */
';
    
    protected $annotationServiceTemplate = '/**
 * @SWG\Get(
 *     path="%s",
 *     tags={%s},
 *     summary=%s,
 *     description=%s,
 *     produces={"application/xml", "application/json", "text/html", "application/vnd.php-object"},
%s
 *     @SWG\Response(response="200", description="An example resource"),
 *     @SWG\Response(response="204", description="An empty resource"),
 *     @SWG\Response(response="400", description="Request error"),
 *     @SWG\Response(response="401", description="Unauthorized error"),
 *     @SWG\Response(response="403", description="Unauthenticated error"),
 *     @SWG\Response(response="500", description="Server error")
 * )
 */
';
    
    protected $annotationParamTemplate = ' *     @SWG\Parameter(
 *         name=%s,
 *         in=%s,
 *         description=%s,
 *         required=%s,
 *         type=%s,
 *         default=%s,%s
 *     ),
';
    
    public function __construct(BaseModule $module) {
        $this->module = $module;
        $this->identity = $this->module->identify(true);
        $this->specName = $this->identity['name'];
    }
    
    public function create($specPath) {
        $specFile = $this->specName . '.php';
        $spec = $this->generate();
        file_put_contents($specPath . $specFile, $spec);
    }
    
    public function generate() {
        $params = [
            $this->getSwaggerServiceAnnotation(),//api docs
            $this->specName,//service class name
            $this->getServiceName(),//service name
            $this->getServiceDefinition(),//service definition
        ];
        return vsprintf($this->specTemplate, $params);
    }
        
    public function getSwaggerServiceAnnotation() {
        $services = $this->normalizeServices();
        $annotation = vsprintf($this->annotationTemplate, [
            json_encode(str_replace('"', "'", $this->getServiceName())),
            json_encode(str_replace('"', "'", $this->getServiceDescription()))
        ]);
        foreach ($services as $service => $definition) {
            $params = '';
            foreach ($definition['parameters'] as $param => $options) {
                $extra = '';
                $extraKeys = ['format', 'maximum', 'minimum', 'items', 'minItems', 'maxItems', 'collectionFormat', 'enum'];
                foreach ($extraKeys as $key) {
                    if (isset($options[$key])) {
                        $extra.= "\n * {$key}=" . json_encode($options[$key]) . ",";
                    }
                }            
                if (isset($options['options'])) {
                    $optionString = implode(', ', $this->parseParameterOptions((array) $options['options']));
                    $options['description'] .= " [Options: {$optionString}]";
                }
                $params .= vsprintf($this->annotationParamTemplate, [
                    json_encode($param), 
                    json_encode($options['in']),
                    json_encode(str_replace('"', "'", $options['description'])),
                    json_encode(!empty($options['required'])),
                    json_encode($options['type']),
                    json_encode(!is_null($options['default']) ? $options['default'] : ''),
                    $extra
                ]);
            }
            $path = '/' . ModuleRegistry::underscore($this->getServiceName());
            if (!empty($service) && $service !== 'default') {
                $path .= '/' . strtolower($service);
            }
            $annotation.= vsprintf($this->annotationServiceTemplate, [
                $path,
                json_encode(str_replace('"', "'", $this->getServiceName())),
                json_encode(str_replace('"', "'", $this->getServiceName())),
                json_encode(str_replace('"', "'", $this->getServiceDescription())),
                $params,
                json_encode(str_replace('"', "'", $this->getServiceName())),
                json_encode(str_replace('"', "'", $this->getServiceDescription()))
            ]);

        }
        return $annotation;
    }
    
    public function getServiceName() {
        if (isset($this->identity['name'])) {
            return $this->identity['name'];
        }
        return $this->module->name();
    }
    
    public function getServiceDescription() {
        if (isset($this->identity['description'])) {
            return $this->identity['description'];
        }
        return $this->getServiceName();
    }
    
    public function getServiceDefinition() {
        $services = $this->normalizeServices();
        return str_replace(["\n  ", "\n"], ["\n\t", "\n\t"], var_export($services, true));
    }
    
    public function normalizeServices() {
        $types = @$this->identity['input'] ?: [];
        $services = [];
        $defaultService = [
            'name' => $this->getServiceName(),
            'description' => $this->getServiceDescription(),
            'method' => 'get',
            'parameters' => []
        ];
        if (empty($types) || !is_array($types)) {
            $services = ['default' => $defaultService];
        } else {
            if (!isset($types[0]) && (!isset($types[key($types)]['parameters']))) {
                $types = [$types];
            }
            foreach ($types as $key => $type) {
                if (!is_numeric($key)) {
                    $serviceType = $key;
                } else {
                    $serviceType = @$type['type'] ?: '';
                }
                $serviceType = $serviceType ?: 'default';
                $method = @$type['method'] ?: 'get';
                $name = @$type['name'];
                $description = @$type['description'] ?: '';
                unset($type['type'], $type['method'], $type['name'], $type['description']);
                $parameters = $type ? $this->parseParameters($type) : [];
                $subServiceTypes = explode(',', $serviceType);
                foreach ($subServiceTypes as $serviceType) {
                    $serviceType = trim($serviceType);
                    $name = $serviceType && $serviceType !== 'default'  ? $serviceType : ($name ?: $this->getServiceName());
                    $services[$serviceType] = compact('name', 'description', 'method', 'parameters');
                }
            }
        }
        return $services;
    }
    
    public function parseParameters($params) {
        if (isset($params['parameters'])) {
            $params = $params['parameters'];
        }
        $defaultParam = [
            'description' => '',
            'type' => 'string',
            'in' => 'query',
            'default' => null,
            'required' => false
        ];
        $parameters = [];
        foreach ($params as $param => $options) {
            $match = [];
            if (is_string($options)) {
               if (is_numeric($options)) {
                   $options = ['default' => $options];
               } elseif (preg_match('/(\w,)+\w/', $options)) {
                   $options = ['description' => 'Options:' . $options];
               } else {
                   $options = ['description' => $options];
               } 
            } elseif (!is_array($options)) {
                $options = ['default' => $options];
            }
            if (isset($options['default']) && !is_string($options['default']) && !isset($options['type'])) {
                if (is_numeric($options['default'])) {
                    $options['type'] = 'number';
                    $options['format'] = is_float($options['default']) ? 'float' : 'integer';
                }
                //other type
            }
            $parameters[$param] = array_merge($defaultParam, $options);
        }
        return $parameters;
    }
    
    public function parseParameterOptions(array $options) {
        $keys = [];
        foreach ($options as $key => $value) {
            if (is_int($key)) {
                $keys[] = "'{$value}'";
            } else {
                $keys[] = "'{$key}' ({$value})";
            }
        }
        return $keys;
    }
}