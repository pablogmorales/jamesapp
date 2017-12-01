<?php
$rootPath = dirname(__DIR__);

/*
 * Bootstrap
 */
require $rootPath . '/config/bootstrap.php';

$specPath = $rootPath . '/spec/';
$serviceSpecPath = $specPath . 'Service/';

if ($specs = glob($serviceSpecPath . '*.php')) {
    array_map('unlink', $specs);
}

$ModuleRegistry = Daytalytics\ModuleRegistry::get_instance();

$modules = $ModuleRegistry->get_module_names();

foreach ($modules as $name) {
    $module = $ModuleRegistry->get_module($name);
    $moduleClass = get_class($module);
    if (!$moduleClass::$private) {
        (new Daytalytics\Spec\Service($module))->create($serviceSpecPath);
    }
}

$swagger = Swagger\scan($specPath);
$swagger->saveAs($specPath . 'swagger.json');