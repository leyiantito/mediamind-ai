<?php

namespace App\Core;

use Illuminate\Foundation\Application as BaseApplication;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Application extends BaseApplication
{
    /**
     * The base path of the application installation.
     *
     * @var string
     */
    protected $basePath;

    /**
     * Create a new application instance.
     *
     * @param  string|null  $basePath
     * @return void
     */
    public function __construct($basePath = null)
    {
        parent::__construct($basePath);
    }

    /**
     * Set the base path for the application.
     *
     * @param  string  $basePath
     * @return $this
     */
    public function setBasePath($basePath)
    {
        $this->basePath = rtrim($basePath, '\\/');
        $this->bindPathsInContainer();
        return $this;
    }

    /**
     * Bind all of the application paths in the container.
     *
     * @return void
     */
    protected function bindPathsInContainer()
    {
        $this->instance('path', $this->path());
        $this->instance('path.base', $this->basePath());
        $this->instance('path.config', $this->configPath());
        $this->instance('path.public', $this->publicPath());
        $this->instance('path.storage', $this->storagePath());
        $this->instance('path.database', $this->databasePath());
        $this->instance('path.resources', $this->resourcePath());
        $this->instance('path.bootstrap', $this->bootstrapPath());
    }

    /**
     * Get the path to the application configuration files.
     *
     * @param  string  $path Optionally, a path to append to the config path
     * @return string
     */
    public function configPath($path = '')
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'config'.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Get the path to the bootstrap directory.
     *
     * @param  string  $path Optionally, a path to append to the bootstrap path
     * @return string
     */
    public function bootstrapPath($path = '')
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'bootstrap'.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Get the path to the storage directory.
     *
     * @return string
     */
    public function storagePath()
    {
        return $this->storagePath ?: $this->basePath.DIRECTORY_SEPARATOR.'storage';
    }

    /**
     * Get the path to the resources directory.
     *
     * @param  string  $path
     * @return string
     */
    public function resourcePath($path = '')
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'resources'.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Get the path to the public / web directory.
     *
     * @return string
     */
    public function publicPath()
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'public';
    }

    /**
     * Get the path to the database directory.
     *
     * @param  string  $path
     * @return string
     */
    public function databasePath($path = '')
    {
        return $this->databasePath ?: $this->basePath.DIRECTORY_SEPARATOR.'database'.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Handle the incoming HTTP request.
     *
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     * @param  int  $type
     * @param  bool  $catch
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle($request, $type = self::MAIN_REQUEST, $catch = true)
    {
        return parent::handle($request, $type, $catch);
    }
}