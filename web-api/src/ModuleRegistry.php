<?php
namespace Daytalytics;

use ReflectionClass;
use Exception;
use Daytalytics\Module\BaseModule;

class ModuleRegistry {
    
    private static $instance;
    
    protected $modules = [];
    
    protected $module_paths_by_identifier = [];
    
    /**
     * 
     * @return \Daytalytics\ModuleRegistry
     */
    public static function get_instance() {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * 
     * @param unknown $module
     * @return BaseModule
     */
    public static function get($module) {
        return self::get_instance()->get_module($module);
    }
    
    public static function underscore($module) {
        return strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $module));
    }
    
    public static function camelize($module) {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $module)));
    }
    
    /**
     * 
     * @param unknown $module
     * @return mixed
     */
    public function get_module($module) {
        $moduleName = static::camelize($module);
        if (!isset($this->modules[$moduleName])) {
            $this->modules[$moduleName]= $this->include_module($moduleName);
        }
        return $this->modules[$moduleName];
    }
    
    /**
     * get an array of all the module identities in the system
     * 
     * @return array
     */
    public function get_module_names() {
        $moduleFiles = $this->get_module_files();
        $moduleNames = [];
        foreach($moduleFiles as $moduleName) {
            try {
                $module = $this->include_module($moduleName);
            } catch (Exception $e) {
                $module = false;
            }
            if ($module) {
                $moduleNames[] = $moduleName;
            }
        }
        return $moduleNames;
    }
    
    /**
     * This method instantiates a module.
     */
    protected function include_module($moduleName) {
        $moduleClass = 'Daytalytics\Module\\' . $moduleName;
        if(!class_exists($moduleClass)) {
            throw new Exception('Could not include module"'.$moduleClass.'"');
        }
        $module = null;
        if (class_exists($moduleClass) && is_subclass_of($moduleClass, 'Daytalytics\Module\BaseModule')) {
            $reflection = new ReflectionClass($moduleClass);
            if ($reflection->isAbstract()) {
                return false;
            }
            $module = new $moduleClass(Database::get_instance());
        }
        if (empty($module)) {
            throw new Exception('Could not include module"'.$moduleClass.'"');
        }
        return $module;
    }
    
    /**
     * Get list of possible module names from the module files
     * 
     * @return mixed[]
     */
    protected function get_module_files() {
        $modules_dir = __DIR__ . '/Module';
        $files = glob($modules_dir . '/*.php');
        $classes = [];
        foreach ($files as $file) {
            $className = current(explode('.', basename($file)));
            $classes[] = $className;
        }
        return $classes;
    }
}