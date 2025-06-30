<?php

namespace MediaMindAI\Http\Controllers;

use MediaMindAI\Core\View\Factory as View;
use MediaMindAI\Core\Http\Request;
use MediaMindAI\Core\Http\Response;

abstract class Controller
{
    /**
     * The view factory instance.
     *
     * @var \MediaMindAI\Core\View\Factory
     */
    protected $view;

    /**
     * The request instance.
     *
     * @var \MediaMindAI\Core\Http\Request
     */
    protected $request;

    /**
     * Create a new controller instance.
     *
     * @param  \MediaMindAI\Core\View\Factory  $view
     * @param  \MediaMindAI\Core\Http\Request  $request
     * @return void
     */
    public function __construct(View $view, Request $request)
    {
        $this->view = $view;
        $this->request = $request;
    }

    /**
     * Execute an action on the controller.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function callAction($method, $parameters = [])
    {
        return $this->{$method}(...array_values($parameters));
    }

    /**
     * Return a view response.
     *
     * @param  string  $view
     * @param  array  $data
     * @param  int  $status
     * @param  array  $headers
     * @return \MediaMindAI\Core\Http\Response
     */
    protected function view($view, $data = [], $status = 200, array $headers = [])
    {
        return new Response(
            $this->view->make($view, $data)->render(),
            $status,
            $headers
        );
    }

    /**
     * Return a JSON response.
     *
     * @param  mixed  $data
     * @param  int  $status
     * @param  array  $headers
     * @param  int  $options
     * @return \MediaMindAI\Core\Http\Response
     */
    protected function json($data, $status = 200, array $headers = [], $options = 0)
    {
        $headers['Content-Type'] = 'application/json';
        
        return new Response(
            json_encode($data, $options),
            $status,
            $headers
        );
    }

    /**
     * Return a redirect response.
     *
     * @param  string  $url
     * @param  int  $status
     * @param  array  $headers
     * @return \MediaMindAI\Core\Http\Response
     */
    protected function redirect($url, $status = 302, array $headers = [])
    {
        $headers['Location'] = $url;
        
        return new Response('', $status, $headers);
    }

    /**
     * Return a redirect response to a named route.
     *
     * @param  string  $route
     * @param  array  $parameters
     * @param  int  $status
     * @param  array  $headers
     * @return \MediaMindAI\Core\Http\Response
     */
    protected function redirectToRoute($route, $parameters = [], $status = 302, array $headers = [])
    {
        // TODO: Implement route URL generation
        $url = $this->generateUrl($route, $parameters);
        
        return $this->redirect($url, $status, $headers);
    }

    /**
     * Return a redirect response to a controller action.
     *
     * @param  string  $action
     * @param  array  $parameters
     * @param  int  $status
     * @param  array  $headers
     * @return \MediaMindAI\Core\Http\Response
     */
    protected function redirectToAction($action, $parameters = [], $status = 302, array $headers = [])
    {
        // TODO: Implement action URL generation
        $url = $this->generateActionUrl($action, $parameters);
        
        return $this->redirect($url, $status, $headers);
    }

    /**
     * Generate a URL to a named route.
     *
     * @param  string  $name
     * @param  array  $parameters
     * @return string
     */
    protected function generateUrl($name, array $parameters = [])
    {
        // TODO: Implement route URL generation
        throw new \RuntimeException('Route URL generation not implemented');
    }

    /**
     * Generate a URL to a controller action.
     *
     * @param  string  $action
     * @param  array  $parameters
     * @return string
     */
    protected function generateActionUrl($action, array $parameters = [])
    {
        // TODO: Implement action URL generation
        throw new \RuntimeException('Action URL generation not implemented');
    }

    /**
     * Handle calls to missing methods on the controller.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        throw new \BadMethodCallException(sprintf(
            'Method %s::%s does not exist.', static::class, $method
        ));
    }
}
