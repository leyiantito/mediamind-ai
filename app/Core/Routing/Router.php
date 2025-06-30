<?php

namespace MediaMindAI\Core\Routing;

use MediaMindAI\Core\Http\Request;
use MediaMindAI\Core\Http\Response;
use Closure;
use Exception;

class Router
{
    /**
     * All registered routes.
     *
     * @var array
     */
    protected $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'DELETE' => [],
    ];

    /**
     * Register a GET route.
     *
     * @param  string  $uri
     * @param  mixed  $action
     * @return void
     */
    public function get($uri, $action)
    {
        $this->addRoute('GET', $uri, $action);
    }

    /**
     * Register a POST route.
     *
     * @param  string  $uri
     * @param  mixed  $action
     * @return void
     */
    public function post($uri, $action)
    {
        $this->addRoute('POST', $uri, $action);
    }

    /**
     * Register a PUT route.
     *
     * @param  string  $uri
     * @param  mixed  $action
     * @return void
     */
    public function put($uri, $action)
    {
        $this->addRoute('PUT', $uri, $action);
    }

    /**
     * Register a DELETE route.
     *
     * @param  string  $uri
     * @param  mixed  $action
     * @return void
     */
    public function delete($uri, $action)
    {
        $this->addRoute('DELETE', $uri, $action);
    }

    /**
     * Add a route to the routing table.
     *
     * @param  string  $method
     * @param  string  $uri
     * @param  mixed  $action
     * @return void
     */
    protected function addRoute($method, $uri, $action)
    {
        $this->routes[$method][$this->prepareUri($uri)] = $this->parseAction($action);
    }

    /**
     * Parse the action into a standard format.
     *
     * @param  mixed  $action
     * @return array|Closure
     */
    protected function parseAction($action)
    {
        if ($action instanceof Closure) {
            return $action;
        }

        if (is_string($action) && str_contains($action, '@')) {
            [$controller, $method] = explode('@', $action);
            return ['uses' => $controller . '@' . $method];
        }

        throw new Exception('Invalid route action.');
    }

    /**
     * Prepare the URI for matching.
     *
     * @param  string  $uri
     * @return string
     */
    protected function prepareUri($uri)
    {
        return '/' . trim($uri, '/');
    }

    /**
     * Dispatch the request to the application.
     *
     * @param  \MediaMindAI\Core\Http\Request  $request
     * @return \MediaMindAI\Core\Http\Response
     */
    public function dispatch(Request $request)
    {
        $method = $request->method();
        $uri = $request->path();

        if (!isset($this->routes[$method])) {
            return $this->methodNotAllowed();
        }

        $route = $this->findRoute($method, $uri);

        if (is_null($route)) {
            return $this->notFound();
        }

        $action = $route['action'];

        if ($action instanceof Closure) {
            return new Response($action($request));
        }

        if (is_array($action) && isset($action['uses'])) {
            return $this->runController($action['uses'], $request, $route['parameters']);
        }

        return $this->notFound();
    }

    /**
     * Find the route matching the request.
     *
     * @param  string  $method
     * @param  string  $uri
     * @return array|null
     */
    protected function findRoute($method, $uri)
    {
        if (!isset($this->routes[$method])) {
            return null;
        }

        foreach ($this->routes[$method] as $routeUri => $action) {
            $pattern = $this->compileRoute($routeUri);
            if (preg_match($pattern, $uri, $matches)) {
                $parameters = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                return [
                    'action' => $action,
                    'parameters' => $parameters
                ];
            }
        }

        return null;
    }

    /**
     * Compile the route to a regex pattern.
     *
     * @param  string  $uri
     * @return string
     */
    protected function compileRoute($uri)
    {
        $pattern = preg_replace('/\//', '\/', $uri);
        $pattern = preg_replace('/\{([a-z]+)\}/', '(?P<$1>[^\/]+)', $pattern);
        return '/^' . $pattern . '$/i';
    }

    /**
     * Run the controller action.
     *
     * @param  string  $action
     * @param  \MediaMindAI\Core\Http\Request  $request
     * @param  array  $parameters
     * @return \MediaMindAI\Core\Http\Response
     */
    protected function runController($action, $request, $parameters = [])
    {
        [$controller, $method] = explode('@', $action);
        $controller = 'MediaMindAI\\Http\\Controllers\\' . $controller;
        
        if (!class_exists($controller)) {
            throw new Exception("Controller {$controller} not found");
        }

        $controllerInstance = new $controller();
        
        if (!method_exists($controllerInstance, $method)) {
            throw new Exception("Method {$method} not found in controller {$controller}");
        }

        $response = $controllerInstance->$method($request, ...array_values($parameters));
        
        if (!$response instanceof Response) {
            $response = new Response($response);
        }
        
        return $response;
    }

    /**
     * Return a 404 Not Found response.
     *
     * @return \MediaMindAI\Core\Http\Response
     */
    protected function notFound()
    {
        return new Response('404 Not Found', 404);
    }

    /**
     * Return a 405 Method Not Allowed response.
     *
     * @return \MediaMindAI\Core\Http\Response
     */
    protected function methodNotAllowed()
    {
        return new Response('405 Method Not Allowed', 405);
    }
}
