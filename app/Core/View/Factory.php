<?php

namespace MediaMindAI\Core\View;

use InvalidArgumentException;
use MediaMindAI\Core\Http\Response;
use MediaMindAI\Core\View\Engines\EngineInterface;
use MediaMindAI\Core\View\Engines\PhpEngine;
use MediaMindAI\Core\View\Engines\FileViewFinder;

class Factory
{
    /**
     * The view finder implementation.
     *
     * @var \MediaMindAI\Core\View\Engines\FileViewFinder
     */
    protected $finder;

    /**
     * The engine implementation.
     *
     * @var \MediaMindAI\Core\View\Engines\EngineInterface
     */
    protected $engine;

    /**
     * Data that should be available to all templates.
     *
     * @var array
     */
    protected $shared = [];

    /**
     * Array of registered view name aliases.
     *
     * @var array
     */
    protected $aliases = [];

    /**
     * All of the registered view names.
     *
     * @var array
     */
    protected $names = [];

    /**
     * The extension to engine bindings.
     *
     * @var array
     */
    protected $extensions = [
        'php' => 'php',
        'css' => 'file',
        'html' => 'file',
    ];

    /**
     * The view's environment data.
     *
     * @var array
     */
    protected $data = [];

    /**
     * Create a new view factory instance.
     *
     * @param  string  $viewPath
     * @param  string  $cachePath
     * @return void
     */
    public function __construct($viewPath, $cachePath)
    {
        $this->finder = new FileViewFinder($viewPath);
        $this->share('__env', $this);
    }

    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string  $view
     * @param  array  $data
     * @param  array  $mergeData
     * @return \MediaMindAI\Core\View\View
     */
    public function make($view, $data = [], $mergeData = [])
    {
        $path = $this->finder->find(
            $view = $this->normalizeName($view)
        );

        // Next, we will create the view instance and call the view creator for the view
        // which can set any data, etc. Then we will return the view instance back to
        // the caller for rendering or performing other view manipulations on this.
        $data = array_merge($mergeData, $this->parseData($data));

        return new View($this, $this->getEngineFromPath($path), $view, $path, $data);
    }

    /**
     * Get the evaluated view contents for a named view.
     *
     * @param  string  $view
     * @param  mixed  $data
     * @return \MediaMindAI\Core\View\View
     */
    public function of($view, $data = [])
    {
        return $this->make($this->names[$view], $data);
    }

    /**
     * Normalize a view name.
     *
     * @param  string  $name
     * @return string
     */
    protected function normalizeName($name)
    {
        $delimiter = FileViewFinder::HINT_PATH_DELIMITER;

        if (strpos($name, $delimiter) === false) {
            return str_replace('/', '.', $name);
        }

        list($namespace, $name) = explode($delimiter, $name);

        return $namespace.$delimiter.str_replace('/', '.', $name);
    }

    /**
     * Parse the given data into a raw array.
     *
     * @param  mixed  $data
     * @return array
     */
    protected function parseData($data)
    {
        return $data instanceof Arrayable ? $data->toArray() : $data;
    }

    /**
     * Get the appropriate view engine for the given path.
     *
     * @param  string  $path
     * @return \MediaMindAI\Core\View\Engines\EngineInterface
     *
     * @throws \InvalidArgumentException
     */
    public function getEngineFromPath($path)
    {
        if (! $extension = $this->getExtension($path)) {
            throw new InvalidArgumentException("Unrecognized extension in file: {$path}");
        }

        $engine = $this->extensions[$extension];

        if (isset($this->engines[$engine])) {
            return $this->engines[$engine];
        }

        if (isset(static::$extensions[$engine])) {
            return $this->engines[$engine] = new static::$extensions[$engine];
        }

        throw new InvalidException("No engine is associated with the {$extension} extension.");
    }

    /**
     * Get the extension used by the view file.
     *
     * @param  string  $path
     * @return string
     */
    protected function getExtension($path)
    {
        $extensions = array_keys($this->extensions);

        return array_first($extensions, function ($value) use ($path) {
            return str_ends_with($path, '.' . $value);
        });
    }

    /**
     * Add a piece of shared data to the environment.
     *
     * @param  array|string  $key
     * @param  mixed  $value
     * @return mixed
     */
    public function share($key, $value = null)
    {
        $keys = is_array($key) ? $key : [$key => $value];

        foreach ($keys as $key => $value) {
            $this->shared[$key] = $value;
        }

        return $value;
    }

    /**
     * Register a view composer event.
     *
     * @param  array|string  $views
     * @param  \Closure|string  $callback
     * @return array
     */
    public function composer($views, $callback)
    {
        $composers = [];

        foreach ((array) $views as $view) {
            $composers[] = $this->addViewEvent($view, $callback);
        }

        return $composers;
    }

    /**
     * Add an event for a given view.
     *
     * @param  string  $view
     * @param  \Closure|string  $callback
     * @return \Closure
     */
    protected function addViewEvent($view, $callback)
    {
        $name = $this->normalizeName($view);

        if ($callback instanceof Closure) {
            $this->addEventListener($name, $callback);

            return $callback;
        } elseif (is_string($callback)) {
            return $this->addClassEvent($name, $callback);
        }
    }

    /**
     * Register a view creator event.
     *
     * @param  array|string  $views
     * @param  \Closure|string  $callback
     * @return array
     */
    public function creator($views, $callback)
    {
        return $this->addViewEvent($views, $callback);
    }

    /**
     * Add a new namespace to the loader.
     *
     * @param  string  $namespace
     * @param  string|array  $hints
     * @return void
     */
    public function addNamespace($namespace, $hints)
    {
        $this->finder->addNamespace($namespace, $hints);
    }

    /**
     * Replace the namespace hints for the given namespace.
     *
     * @param  string  $namespace
     * @param  string|array  $hints
     * @return void
     */
    public function replaceNamespace($namespace, $hints)
    {
        $this->finder->replaceNamespace($namespace, $hints);
    }

    /**
     * Check if a given view exists.
     *
     * @param  string  $view
     * @return bool
     */
    public function exists($view)
    {
        try {
            $this->finder->find($view);
        } catch (InvalidArgumentException $e) {
            return false;
        }

        return true;
    }

    /**
     * Get the appropriate view engine for the given path.
     *
     * @param  string  $path
     * @return \MediaMindAI\Core\View\Engines\EngineInterface
     */
    public function getEngine($path)
    {
        return $this->engines[pathinfo($path, PATHINFO_EXTENSION)];
    }

    /**
     * Get the view finder instance.
     *
     * @return \MediaMindAI\Core\View\Engines\FileViewFinder
     */
    public function getFinder()
    {
        return $this->finder;
    }

    /**
     * Set the view finder instance.
     *
     * @param  \MediaMindAI\Core\View\Engines\FileViewFinder  $finder
     * @return void
     */
    public function setFinder(FileViewFinder $finder)
    {
        $this->finder = $finder;
    }

    /**
     * Get an item from the shared data.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function shared($key, $default = null)
    {
        return array_key_exists($key, $this->shared) ? $this->shared[$key] : $default;
    }

    /**
     * Get all of the shared data for the environment.
     *
     * @return array
     */
    public function getShared()
    {
        return $this->shared;
    }

    /**
     * Register a view extension.
     *
     * @param  string  $extension
     * @param  string  $engine
     * @param  \Closure  $resolver
     * @return void
     */
    public function addExtension($extension, $engine, $resolver = null)
    {
        if (isset($resolver)) {
            $this->engines[$engine] = $resolver;
        }

        $this->extensions[$extension] = $engine;
    }

    /**
     * Get the extension to engine bindings.
     *
     * @return array
     */
    public function getExtensions()
    {
        return $this->extensions;
    }
}
