<?php

declare (strict_types=1);

namespace dtapps\qiniu\sms;

/**
 * Class Request
 * @package dtapps\qiniu\sms
 */
final class Request
{
    public $url;
    public $headers;
    public $body;
    public $method;

    public function __construct($method, $url, array $headers = array(), $body = null)
    {
        $this->method = strtoupper($method);
        $this->url = $url;
        $this->headers = $headers;
        $this->body = $body;
    }
}