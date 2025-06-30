<?php

namespace MediaMindAI\Core;

use MediaMindAI\Core\Support\Env;
use RuntimeException;

class Config
{
    /**
     * All of the configuration items.
     *
     * @var array
     */
    protected static $items = [];

    /**
     * The configuration paths.
     *
     * @var array
     */
    protected static $paths = [];

    /**
     * Set the configuration paths.
     *
     * @param  array|string  $paths
     * @return void
     */
    public static function setPaths($paths)
    {
        $paths = is_array($paths) ? $paths : [$paths];
        
        foreach ($paths as $path) {
            if (is_dir($path) && !in_array($path, static::$paths, true)) {
                static::$paths[] = rtrim($path, '/\\');
            }
        }
    }

    /**
     * Load all of the configuration items.
     *
     * @return void
     */
    public static function load()
    {
        static::$items = [];

        foreach (static::$paths as $path) {
            foreach (glob("$path/*.php") as $file) {
                $key = basename($file, '.php');
                static::$items[$key] = require $file;
            }
        }
    }

    /**
     * Get the specified configuration value.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        $segments = explode('.', $key);
        $file = array_shift($segments);
        
        if (!isset(static::$items[$file])) {
            $path = null;
            
            foreach (static::$paths as $configPath) {
                $configFile = "$configPath/$file.php";
                if (file_exists($configFile)) {
                    $path = $configFile;
                    break;
                }
            }
            
            if (!$path) {
                return $default;
            }
            
            static::$items[$file] = require $path;
        }
        
        $value = static::$items[$file];
        
        foreach ($segments as $segment) {
            if (is_array($value) && array_key_exists($segment, $value)) {
                $value = $value[$segment];
            } else {
                return $default;
            }
        }
        
        return $value;
    }

    /**
     * Set a given configuration value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public static function set($key, $value = null)
    {
        $keys = is_array($key) ? $key : [$key => $value];
        
        foreach ($keys as $k => $v) {
            static::setValue($k, $v);
        }
    }

    /**
     * Set a configuration value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    protected static function setValue($key, $value)
    {
        $segments = explode('.', $key);
        $file = array_shift($segments);
        
        if (!isset(static::$items[$file])) {
            static::$items[$file] = [];
        }
        
        $array = &static::$items[$file];
        
        while (count($segments) > 1) {
            $segment = array_shift($segments);
            
            if (!isset($array[$segment]) || !is_array($array[$segment])) {
                $array[$segment] = [];
            }
            
            $array = &$array[$segment];
        }
        
        $array[array_shift($segments)] = $value;
    }

    /**
     * Determine if the given configuration value exists.
     *
     * @param  string  $key
     * @return bool
     */
    public static function has($key)
    {
        return static::get($key) !== null;
    }

    /**
     * Get all of the configuration items.
     *
     * @return array
     */
    public static function all()
    {
        return static::$items;
    }

    /**
     * Get the configuration paths.
     *
     * @return array
     */
    public static function getPaths()
    {
        return static::$paths;
    }

    /**
     * Clear all of the configuration items.
     *
     * @return void
     */
    public static function clear()
    {
        static::$items = [];
    }

    /**
     * Get a configuration value from environment variables.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public static function env($key, $default = null)
    {
        $value = Env::get($key);
        
        if ($value === false) {
            return $default;
        }
        
        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return null;
        }
        
        if (preg_match('/\A([\'\"])(.*)\1\z/', $value, $matches)) {
            return $matches[2];
        }
        
        return $value;
    }
}
