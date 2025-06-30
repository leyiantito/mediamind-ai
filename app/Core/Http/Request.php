<?php

namespace MediaMindAI\Core\Http;

class Request
{
    /**
     * The request method.
     *
     * @var string
     */
    protected $method;

    /**
     * The request URI.
     *
     * @var string
     */
    protected $uri;

    /**
     * The request query parameters.
     *
     * @var array
     */
    protected $query = [];

    /**
     * The request POST data.
     *
     * @var array
     */
    protected $request = [];

    /**
     * The request cookies.
     *
     * @var array
     */
    protected $cookies = [];

    /**
     * The request server parameters.
     *
     * @var array
     */
    protected $server = [];

    /**
     * The request headers.
     *
     * @var array
     */
    protected $headers = [];

    /**
     * The request body.
     *
     * @var string|null
     */
    protected $content;

    /**
     * Create a new HTTP request.
     *
     * @param  array  $query
     * @param  array  $request
     * @param  array  $cookies
     * @param  array  $server
     * @param  string|null  $content
     */
    public function __construct(array $query = [], array $request = [], array $cookies = [], array $server = [], $content = null)
    {
        $this->initialize($query, $request, $cookies, $server, $content);
    }

    /**
     * Create a request from PHP globals.
     *
     * @return static
     */
    public static function capture()
    {
        return new static($_GET, $_POST, $_COOKIE, $_SERVER, file_get_contents('php://input'));
    }

    /**
     * Initialize the request with data.
     *
     * @param  array  $query
     * @param  array  $request
     * @param  array  $cookies
     * @param  array  $server
     * @param  string|null  $content
     * @return void
     */
    protected function initialize(array $query = [], array $request = [], array $cookies = [], array $server = [], $content = null)
    {
        $this->query = $query;
        $this->request = $request;
        $this->cookies = $cookies;
        $this->server = $server;
        $this->content = $content;
        $this->method = strtoupper($server['REQUEST_METHOD'] ?? 'GET');
        $this->uri = $server['REQUEST_URI'] ?? '/';
        $this->headers = $this->getHeadersFromServer($server);
    }

    /**
     * Get the request method.
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Get the request URI.
     *
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Get the request path.
     *
     * @return string
     */
    public function getPath()
    {
        $path = parse_url($this->uri, PHP_URL_PATH);
        return $path ?: '/';
    }

    /**
     * Get all query parameters.
     *
     * @return array
     */
    public function query()
    {
        return $this->query;
    }

    /**
     * Get a query parameter.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $this->query[$key] ?? $default;
    }

    /**
     * Get all POST data.
     *
     * @return array
     */
    public function request()
    {
        return $this->request;
    }

    /**
     * Get a POST parameter.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function post($key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->request;
        }

        return $this->request[$key] ?? $default;
    }

    /**
     * Get all input data.
     *
     * @return array
     */
    public function all()
    {
        return array_merge($this->query, $this->request);
    }

    /**
     * Get an input parameter.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function input($key, $default = null)
    {
        if (array_key_exists($key, $this->request)) {
            return $this->request[$key];
        }

        if (array_key_exists($key, $this->query)) {
            return $this->query[$key];
        }

        return $default;
    }

    /**
     * Get all cookies.
     *
     * @return array
     */
    public function cookies()
    {
        return $this->cookies;
    }

    /**
     * Get a cookie.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function cookie($key, $default = null)
    {
        return $this->cookies[$key] ?? $default;
    }

    /**
     * Get all headers.
     *
     * @return array
     */
    public function headers()
    {
        return $this->headers;
    }

    /**
     * Get a header.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function header($key, $default = null)
    {
        $key = strtoupper(str_replace('-', '_', $key));
        return $this->headers[$key] ?? $default;
    }

    /**
     * Get the request body.
     *
     * @return string|null
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Get the JSON payload.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function json($key = null, $default = null)
    {
        $data = json_decode($this->content, true);

        if (is_null($key)) {
            return $data;
        }

        return $data[$key] ?? $default;
    }

    /**
     * Check if the request is an AJAX request.
     *
     * @return bool
     */
    public function isAjax()
    {
        return isset($this->server['HTTP_X_REQUESTED_WITH']) && 
               strtolower($this->server['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Check if the request is a JSON request.
     *
     * @return bool
     */
    public function isJson()
    {
        $contentType = $this->header('CONTENT_TYPE', '');
        return strpos($contentType, '/json') !== false || 
               strpos($contentType, '+json') !== false;
    }

    /**
     * Get the client IP address.
     *
     * @return string
     */
    public function ip()
    {
        if (isset($this->server['HTTP_CLIENT_IP'])) {
            return $this->server['HTTP_CLIENT_IP'];
        }

        if (isset($this->server['HTTP_X_FORWARDED_FOR'])) {
            return $this->server['HTTP_X_FORWARDED_FOR'];
        }

        return $this->server['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    /**
     * Get the request scheme.
     *
     * @return string
     */
    public function getScheme()
    {
        return $this->isSecure() ? 'https' : 'http';
    }

    /**
     * Check if the request is secure.
     *
     * @return bool
     */
    public function isSecure()
    {
        $https = $this->server['HTTPS'] ?? '';
        return !empty($https) && $https !== 'off';
    }

    /**
     * Get the request host.
     *
     * @return string
     */
    public function getHost()
    {
        return $this->server['HTTP_HOST'] ?? 'localhost';
    }

    /**
     * Get the request URL.
     *
     * @return string
     */
    public function url()
    {
        return $this->getScheme() . '://' . $this->getHost() . $this->getUri();
    }

    /**
     * Get the full URL for the request.
     *
     * @return string
     */
    public function fullUrl()
    {
        $query = http_build_query($this->query);
        $url = $this->url();
        
        return $query ? $url . '?' . $query : $url;
    }

    /**
     * Get the current path info for the request.
     *
     * @return string
     */
    public function path()
    {
        return trim(parse_url($this->uri, PHP_URL_PATH), '/');
    }

    /**
     * Get the current decoded path info for the request.
     *
     * @return string
     */
    public function decodedPath()
    {
        return rawurldecode($this->path());
    }

    /**
     * Get the root URL for the application.
     *
     * @return string
     */
    public function root()
    {
        return $this->getScheme() . '://' . $this->getHost();
    }

    /**
     * Get the current encoded path info for the request.
     *
     * @return string
     */
    public function encodedPath()
    {
        return $this->path();
    }

    /**
     * Get the current segment of the request path.
     *
     * @param  int  $index
     * @param  mixed  $default
     * @return string|null
     */
    public function segment($index, $default = null)
    {
        $segments = explode('/', $this->path());
        return $segments[$index - 1] ?? $default;
    }

    /**
     * Get all of the segments for the request path.
     *
     * @return array
     */
    public function segments()
    {
        return explode('/', $this->path());
    }

    /**
     * Get the request's input source.
     *
     * @return array
     */
    public function getInputSource()
    {
        return $this->getMethod() === 'GET' ? $this->query : $this->request;
    }

    /**
     * Get the headers from the server variables.
     *
     * @param  array  $server
     * @return array
     */
    protected function getHeadersFromServer(array $server)
    {
        $headers = [];
        
        foreach ($server as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $headers[substr($key, 5)] = $value;
            } elseif (in_array($key, ['CONTENT_LENGTH', 'CONTENT_MD5', 'CONTENT_TYPE'], true)) {
                $headers[$key] = $value;
            }
        }
        
        return $headers;
    }
}
