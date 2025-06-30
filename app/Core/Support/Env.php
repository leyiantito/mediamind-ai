<?php

namespace MediaMindAI\Core\Support;

class Env
{
    /**
     * The environment variables.
     *
     * @var array
     */
    protected static $variables = [];

    /**
     * Whether the environment has been loaded.
     *
     * @var bool
     */
    protected static $loaded = false;

    /**
     * Load the environment variables from the .env file.
     *
     * @param  string  $path
     * @return void
     */
    public static function load($path)
    {
        if (!file_exists($path)) {
            throw new \RuntimeException("The .env file does not exist at [{$path}].");
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            // Skip comments and invalid lines
            if (strpos(trim($line), '#') === 0 || strpos($line, '=') === false) {
                continue;
            }

            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            // Remove quotes if present
            if (strlen($value) > 1) {
                if (($value[0] === '"' && substr($value, -1) === '"') || 
                    ($value[0] === "'" && substr($value, -1) === "'")) {
                    $value = substr($value, 1, -1);
                }
            }

            // Handle variable references like ${VAR}
            if (preg_match('/\$\{(.*)\}/', $value, $matches)) {
                $nestedVar = $matches[1] ?? null;
                if ($nestedVar && isset(static::$variables[$nestedVar])) {
                    $value = str_replace($matches[0], static::$variables[$nestedVar], $value);
                }
            }

            // Handle variable references like $VAR
            if (strpos($value, '$') !== false) {
                $value = preg_replace_callback('/\$([A-Z_]+)/', function($matches) {
                    return static::get($matches[1], $matches[0]);
                }, $value);
            }

            static::$variables[$name] = $value;
            
            // Also set in $_ENV and $_SERVER for compatibility
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
            
            // For getenv() support
            putenv("$name=$value");
        }

        static::$loaded = true;
    }

    /**
     * Get an environment variable.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        // Check in loaded variables first
        if (isset(static::$variables[$key])) {
            return static::$variables[$key];
        }

        // Check in environment variables
        $value = getenv($key);
        if ($value !== false) {
            return $value;
        }

        // Check in $_ENV
        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }

        // Check in $_SERVER
        if (isset($_SERVER[$key])) {
            return $_SERVER[$key];
        }

        return $default;
    }

    /**
     * Set an environment variable.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public static function set($key, $value)
    {
        static::$variables[$key] = $value;
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
        putenv("$key=$value");
    }

    /**
     * Check if an environment variable exists.
     *
     * @param  string  $key
     * @return bool
     */
    public static function has($key)
    {
        return !is_null(static::get($key));
    }

    /**
     * Get all environment variables.
     *
     * @return array
     */
    public static function all()
    {
        return static::$variables;
    }

    /**
     * Clear all environment variables.
     *
     * @return void
     */
    public static function clear()
    {
        static::$variables = [];
        $_ENV = [];
        $_SERVER = array_filter($_SERVER, function($key) {
            return strpos($key, 'PHP_') !== 0;
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Check if the environment has been loaded.
     *
     * @return bool
     */
    public static function isLoaded()
    {
        return static::$loaded;
    }

    /**
     * Get the application environment.
     *
     * @return string
     */
    public static function environment()
    {
        return static::get('APP_ENV', 'production');
    }

    /**
     * Determine if the application is in the local environment.
     *
     * @return bool
     */
    public static function isLocal()
    {
        return static::environment() === 'local';
    }

    /**
     * Determine if the application is in the production environment.
     *
     * @return bool
     */
    public static function isProduction()
    {
        return static::environment() === 'production';
    }

    /**
     * Determine if the application is in the testing environment.
     *
     * @return bool
     */
    public static function isTesting()
    {
        return static::environment() === 'testing';
    }
}
