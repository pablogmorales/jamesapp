<?php

namespace Daytalytics;


class EndpointFailuresHandler {

    private static $instance = null;

    public function __construct(){
        if (!class_exists('Database')) {
            require_once "database.class.php";
        }
        $this->_db = Database::get_instance();
    }

    public static function getInstance() {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }
    
    public static function hasFailed($moduleName) {
        $handler = static::getInstance();
        return $handler->_hasFailed($moduleName);
    }

    public static function save($moduleName){
        $handler = static::getInstance();
        return $handler->_save($moduleName);
    }

    private function _hasFailed($moduleName){
        $time = strtotime("-30 minutes");
        $sql = "SELECT *
                FROM endpoint_failures
                WHERE module_name='" . $this->_db->escape_string($moduleName) . "' AND last_failed > " . $time;
        $result = $this->_db->fetch_assoc($this->_db->query($sql));

        return ($result === false) ? false : true;
    }

    private function _save($moduleName) {
        $moduleName = $this->_db->escape_string($moduleName);
        $time = time();
        $sql = "INSERT INTO endpoint_failures (module_name, last_failed) VALUES ('{$moduleName}', {$time})";
        $result = $this->_db->query($sql);

        return $result;
    }

}