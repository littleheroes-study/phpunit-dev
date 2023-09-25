<?php
namespace App\Core;

class Config
{
    protected static $directory;
 
    public static function set_config_directory($directory)
    {
        self::$directory = $directory;
    }
 
    public static function get_config_directory()
    {
        return rtrim(self::$directory, '/\\');
    }
 
    public static function get($route)
    {
        $values = preg_split('/\./', $route, -1, PREG_SPLIT_NO_EMPTY);
        $file = array_shift($values) . '.php';
        $key = array_shift($values);
        $action = array_shift($values);
        $baseDir = self::get_config_directory() . DIRECTORY_SEPARATOR;
        $config = include($baseDir . $file);
        return $config[$key][$action];
    }
}