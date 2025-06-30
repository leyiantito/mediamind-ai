<?php

namespace MediaMindAI\Providers;

use MediaMindAI\Core\Application;
use MediaMindAI\Core\Config;
use MediaMindAI\Core\Http\Request;
use MediaMindAI\Core\Routing\Router;
use MediaMindAI\Core\View\Factory as ViewFactory;

class AppServiceProvider
{
    /**
     * The application instance.
     *
     * @var \MediaMindAI\Core\Application
     */
    protected $app;

    /**
     * Create a new service provider instance.
     *
     * @param  \MediaMindAI\Core\Application  $app
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConfig();
        $this->registerRequest();
        $this->registerRouter();
        $this->registerView();
    }

    /**
     * Register the configuration services.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->app->singleton('config', function ($app) {
            $config = new Config();
            
            // Load configuration files
            $configPath = $app->configPath();
            if (file_exists($configPath)) {
                $config->loadConfigurationFiles($configPath);
            }
            
            return $config;
        });
    }

    /**
     * Register the request service.
     *
     * @return void
     */
    protected function registerRequest()
    {
        $this->app->singleton('request', function ($app) {
            return Request::capture();
        });
    }

    /**
     * Register the router service.
     *
     * @return void
     */
    protected function registerRouter()
    {
        $this->app->singleton('router', function ($app) {
            return new Router($app);
        });
    }

    /**
     * Register the view service.
     *
     * @return void
     */
    protected function registerView()
    {
        $this->app->singleton('view', function ($app) {
            return new ViewFactory(
                $app->basePath('resources/views'),
                $app->basePath('storage/framework/views')
            );
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Load routes
        $this->loadRoutes();
    }

    /**
     * Load the application routes.
     *
     * @return void
     */
    protected function loadRoutes()
    {
        $router = $this->app['router'];
        
        // Load web routes
        $webRoutes = $this->app->basePath('routes/web.php');
        if (file_exists($webRoutes)) {
            require $webRoutes;
        }
        
        // Load API routes if needed
        $apiRoutes = $this->app->basePath('routes/api.php');
        if (file_exists($apiRoutes)) {
            require $apiRoutes;
        }
    }
}
