<?php

use src\Libs\Router;

ob_start();
session_start();
mb_internal_encoding('UTF-8');
error_reporting(E_ALL);

define('PATH_ROOT', realpath(__DIR__ .'/../'));
define('PATH_CORE', PATH_ROOT . '/Core');

require_once(PATH_ROOT .'/vendor/autoload.php');

include_once(PATH_CORE .'/Config/constants.php');
require_once(PATH_CORE .'/Function/functions.php');

Router::loadRoutes(PATH_ROOT . '/routes');
Router::checkMap();

include_once PATH_ROOT . Router::getCurrentFile();