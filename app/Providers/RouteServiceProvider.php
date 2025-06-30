<?php

namespace MediaMindAI\Providers;

use MediaMindAI\Core\ServiceProvider as BaseServiceProvider;
use MediaMindAI\Core\Routing\Router;

class RouteServiceProvider extends BaseServiceProvider
{
    /**
     * The controller namespace for the application.
     *
     * @var string|null
     */
    protected $namespace = 'MediaMindAI\\Http\\Controllers';

    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerRouter();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();
        $this->mapRoutes();
    }

    /**
     * Register the router instance.
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
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        // Configure rate limiting if needed
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function mapRoutes()
    {
        $this->mapApiRoutes();
        $this->mapWebRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        $this->loadRoutesFrom($this->app->basePath('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        $this->loadRoutesFrom($this->app->basePath('routes/api.php'));
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['router'];
    }
}
