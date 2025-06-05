<?php

namespace src\Libs;

class Router {

    protected static $map = []; // map path => file
    protected static $current_route; // route hiện tại
    protected static $current_file; // file tương ứng với route hiện tại

    /**
     * Thêm file tương ứng với path
     *
     * @param string $path
     * @param string $file
     * @return void
     */
    static function add($path, $file) {
        static::$map[static::formatPath($path)] = trim($file, '/');
    }

    /**
     * Check if a path exists in the map and return the corresponding file
     *
     * @param string $path
     * @return string|null
     */
    static function checkMap($path='') {
        $path = empty($path) ? static::removeQueryParams($_SERVER['REQUEST_URI']) : $path;
        $path = static::formatPath($path);
        static::$current_route = $path;
        static::$current_file = '/app/'. (isset(static::$map[$path]) ? static::$map[$path] : 'client/404.php');
        return static::$current_file;
    }

    /**
     * Remove query parameters from the request URI
     *
     * @param string $uri
     * @return string
     */
    static function removeQueryParams($uri) {
        return strtok($uri, '?');
    }

    /**
     * Check if a path is the current route
     *
     * @param string $path
     * @return boolean
     */
    static function isRoute($path) {
        $path = static::formatPath($path);
        return static::$current_route === $path;
    }

    /**
     * Format a path to trim leading and trailing slashes
     *
     * @param string $path
     * @return string
     */
    static function formatPath($path) {
        $path = trim($path, '/');
        return '/'. $path;
    }

    /**
     * Load routes from files in the routes directory
     *
     * @param string $directory
     * @return void
     */
    static function loadRoutes($directory) {
        foreach (glob($directory . '/*.php') as $file) {
            include $file;
        }
    }

    /**
     * Get the current route
     *
     * @return string|null
     */
    static function getCurrentRoute() {
        return static::$current_route;
    }
    
    /**
     * Get the current file
     *
     * @return string|null
     */
    static function getCurrentFile() {
        return static::$current_file;
    }

    /**
     * Dump the current map
     *
     * @return array
     */
    static function dump() {
        dump(static::$current_route);
        return dd(static::$map);
    }
}