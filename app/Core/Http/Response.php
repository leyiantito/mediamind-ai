<?php

namespace MediaMindAI\Core\Http;

class Response
{
    /**
     * The response content.
     *
     * @var string
     */
    protected $content;

    /**
     * The response status code.
     *
     * @var int
     */
    protected $statusCode;

    /**
     * The response headers.
     *
     * @var array
     */
    protected $headers = [];

    /**
     * The HTTP protocol version.
     *
     * @var string
     */
    protected $protocolVersion = '1.1';

    /**
     * The response status text.
     *
     * @var string
     */
    protected $statusText = '';

    /**
     * Status codes and their corresponding status text.
     *
     * @var array
     */
    protected static $statusTexts = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        103 => 'Early Hints',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => '(Unused)',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Too Early',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
    ];

    /**
     * Create a new HTTP response.
     *
     * @param  mixed  $content
     * @param  int  $status
     * @param  array  $headers
     * @return void
     */
    public function __construct($content = '', $status = 200, array $headers = [])
    {
        $this->setContent($content);
        $this->setStatusCode($status);
        $this->setHeaders($headers);
    }

    /**
     * Create a new response instance with the given content.
     *
     * @param  mixed  $content
     * @param  int  $status
     * @param  array  $headers
     * @return static
     */
    public static function make($content = '', $status = 200, array $headers = [])
    {
        return new static($content, $status, $headers);
    }

    /**
     * Create a new JSON response.
     *
     * @param  mixed  $data
     * @param  int  $status
     * @param  array  $headers
     * @param  int  $options
     * @return static
     */
    public static function json($data, $status = 200, array $headers = [], $options = 0)
    {
        $headers['Content-Type'] = 'application/json';
        
        return new static(json_encode($data, $options), $status, $headers);
    }

    /**
     * Create a new redirect response.
     *
     * @param  string  $url
     * @param  int  $status
     * @param  array  $headers
     * @return static
     */
    public static function redirect($url, $status = 302, array $headers = [])
    {
        $headers['Location'] = $url;
        
        return new static('', $status, $headers);
    }

    /**
     * Set the content on the response.
     *
     * @param  mixed  $content
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = (string) $content;
        
        return $this;
    }

    /**
     * Get the response content.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set the HTTP status code.
     *
     * @param  int  $code
     * @param  string|null  $text
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function setStatusCode($code, $text = null)
    {
        $this->statusCode = (int) $code;
        
        if ($this->isInvalid()) {
            throw new \InvalidArgumentException(
                sprintf('The HTTP status code "%s" is not valid.', $code)
            );
        }
        
        $this->statusText = $text ?? self::$statusTexts[$code] ?? 'unknown status';
        
        return $this;
    }

    /**
     * Get the status code for the response.
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Get the status text for the response.
     *
     * @return string
     */
    public function getStatusText()
    {
        return $this->statusText;
    }

    /**
     * Set multiple headers at once.
     *
     * @param  array  $headers
     * @return $this
     */
    public function setHeaders(array $headers)
    {
        foreach ($headers as $key => $value) {
            $this->setHeader($key, $value);
        }
        
        return $this;
    }

    /**
     * Set a header on the response.
     *
     * @param  string  $key
     * @param  string|array  $values
     * @param  bool  $replace
     * @return $this
     */
    public function setHeader($key, $values, $replace = true)
    {
        $key = str_replace('_', '-', strtolower($key));
        
        if ($replace || !isset($this->headers[$key])) {
            $this->headers[$key] = (array) $values;
        } else {
            $this->headers[$key] = array_merge($this->headers[$key], (array) $values);
        }
        
        return $this;
    }

    /**
     * Get the headers for the response.
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Set the HTTP protocol version.
     *
     * @param  string  $version
     * @return $this
     */
    public function setProtocolVersion($version)
    {
        $this->protocolVersion = $version;
        
        return $this;
    }

    /**
     * Get the HTTP protocol version.
     *
     * @return string
     */
    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    /**
     * Check if the response is invalid.
     *
     * @return bool
     */
    public function isInvalid()
    {
        return $this->statusCode < 100 || $this->statusCode >= 600;
    }

    /**
     * Check if the response is informative.
     *
     * @return bool
     */
    public function isInformational()
    {
        return $this->statusCode >= 100 && $this->statusCode < 200;
    }

    /**
     * Check if the response is successful.
     *
     * @return bool
     */
    public function isSuccessful()
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    /**
     * Check if the response is a redirection.
     *
     * @return bool
     */
    public function isRedirection()
    {
        return $this->statusCode >= 300 && $this->statusCode < 400;
    }

    /**
     * Check if the response indicates a client error.
     *
     * @return bool
     */
    public function isClientError()
    {
        return $this->statusCode >= 400 && $this->statusCode < 500;
    }

    /**
     * Check if the response indicates a server error.
     *
     * @return bool
     */
    public function isServerError()
    {
        return $this->statusCode >= 500 && $this->statusCode < 600;
    }

    /**
     * Check if the response is OK.
     *
     * @return bool
     */
    public function isOk()
    {
        return 200 === $this->statusCode;
    }

    /**
     * Check if the response is a forbidden response.
     *
     * @return bool
     */
    public function isForbidden()
    {
        return 403 === $this->statusCode;
    }

    /**
     * Check if the response is a not found response.
     *
     * @return bool
     */
    public function isNotFound()
    {
        return 404 === $this->statusCode;
    }

    /**
     * Check if the response is a redirect of some type.
     *
     * @return bool
     */
    public function isRedirect()
    {
        return in_array($this->statusCode, [201, 301, 302, 303, 307, 308]);
    }

    /**
     * Send HTTP headers and content.
     *
     * @return $this
     */
    public function send()
    {
        $this->sendHeaders();
        $this->sendContent();
        
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        } elseif (!in_array(PHP_SAPI, ['cli', 'phpdbg'], true)) {
            static::closeOutputBuffers(0, true);
        }
        
        return $this;
    }

    /**
     * Send HTTP headers.
     *
     * @return $this
     */
    public function sendHeaders()
    {
        // Headers have already been sent by the developer
        if (headers_sent()) {
            return $this;
        }
        
        // Status
        header(sprintf('HTTP/%s %s %s', 
            $this->protocolVersion, 
            $this->statusCode, 
            $this->statusText
        ), true, $this->statusCode);
        
        // Headers
        foreach ($this->headers as $name => $values) {
            $name = str_replace(' ', '-', ucwords(str_replace('-', ' ', $name)));
            
            foreach ($values as $value) {
                header("$name: $value", false, $this->statusCode);
            }
        }
        
        // Cookies
        // TODO: Implement cookie handling
        
        return $this;
    }

    /**
     * Send the content to the output buffer.
     *
     * @return $this
     */
    public function sendContent()
    {
        echo $this->content;
        
        return $this;
    }

    /**
     * Cleans or flushes output buffers up to target level.
     *
     * @param  int  $targetLevel
     * @param  bool  $flush
     * @return void
     */
    public static function closeOutputBuffers($targetLevel, $flush)
    {
        $status = ob_get_status(true);
        $level = count($status);
        $flags = PHP_OUTPUT_HANDLER_REMOVABLE | ($flush ? PHP_OUTPUT_HANDLER_FLUSHABLE : PHP_OUTPUT_HANDLER_CLEANABLE);

        while ($level-- > $targetLevel && ($s = $status[$level]) && (!isset($s['del']) ? !isset($s['flags']) || ($s['flags'] & $flags) === $flags : $s['del'])) {
            if ($flush) {
                ob_end_flush();
            } else {
                ob_end_clean();
            }
        }
    }

    /**
     * Convert the response to a string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getContent();
    }
}
