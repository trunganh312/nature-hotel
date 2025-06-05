<?php

namespace src\Libs;

use League\Container\Exception\NotFoundException;

class Container {

    static $instance;

    static function add($class_name) {
        return self::getInstance()->add($class_name);
    }

    static function getInstance() {
        if (!self::$instance) {
            self::$instance = new \League\Container\Container();
        }
        return self::$instance;
    }

    static function get($class_name) {
        try {
            return self::getInstance()->get($class_name);
        } catch (NotFoundException $e) {
            return false;
        }
    }
}