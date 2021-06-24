<?php

declare (strict_types=1);

namespace dtapps\qiniu\sms;

/**
 * Class Error
 * @package dtapps\qiniu\sms
 */
final class Error
{
    private $url;
    private $response;

    public function __construct($url, $response)
    {
        $this->url = $url;
        $this->response = $response;
    }

    public function code()
    {
        return $this->response->statusCode;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function message()
    {
        return $this->response->error;
    }
}