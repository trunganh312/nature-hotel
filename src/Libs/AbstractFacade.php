<?php

namespace src\Libs;

use Exception;
use src\Libs\Container;

abstract class AbstractFacade
{
    /**
     * Call from attached instance
     * @param $name
     * @param array $arguments
     * @return mixed
     * @throws Exception
     */
    public static function __callStatic($name, array $arguments = [])
    {
        $object = self::getInstance();
        return call_user_func_array([$object, $name], $arguments);
    }

    /**
     * Get attached instance
     * @return mixed
     * @throws Exception
     */
    public static function __instance()
    {
        return Container::get(static::accessor());
    }

    public static function getInstance() {
        $object = Container::get(static::accessor());
        if ($object === false) {
            static::init();
            $object = Container::get(static::accessor());
        }
        return $object;
    }

    /**
     * Accessor name
     * @return string
     * @throws Exception
     */
    protected static function accessor(){
        throw new Exception(sprintf('Unknow accessor in %s', static::class));
        return '';
    }

    protected static function init()
    {
        $class = static::accessor();
        Container::getInstance()->add($class, new $class);
    }

}