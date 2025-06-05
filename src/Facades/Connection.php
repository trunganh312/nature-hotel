<?php

namespace src\Facades;

use src\Libs\Container;
use src\Libs\AbstractFacade;

class Connection extends AbstractFacade
{
    protected static function accessor()
    {
        return \src\Libs\Database\Connection::class;
    }
}