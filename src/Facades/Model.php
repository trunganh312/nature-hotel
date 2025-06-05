<?php

namespace src\Facades;

use src\Libs\AbstractFacade;
use src\Libs\Container;

class Model extends AbstractFacade
{
    static protected $table = '';
    static protected $company_key = '';
    static protected $hotel_field = '';

    protected static function accessor()
    {
        return static::class;
    }

    protected static function init()
    {
        Container::getInstance()->add(static::accessor(), new \src\Libs\Database\Model(static::$table, static::$company_key, static::$hotel_field));
    }
}