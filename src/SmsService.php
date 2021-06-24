<?php

declare (strict_types=1);

namespace dtapps\qiniu\sms;

use think\exception\HttpException;

/**
 * Class SmsService
 * @package dtapps\qiniu\sms
 */
class SmsService extends Service
{
    private $accessKey, $secretKey, $body;
    private $method = "POST";
    private $headers = [
        "content-type" => "application/json"
    ];
    private $baseURL = Config::SMS_HOST . "/" . Config::SMS_VERSION . "/message";

    /**
     * @param string $value
     * @return $this
     */
    public function accessKey(string $value): self
    {
        $this->accessKey = $value;
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function secretKey(string $value): self
    {
        $this->secretKey = $value;
        return $this;
    }

    /**
     * 请求参数
     * @param array $params
     * @return $this
     */
    public function setParam(array $params): self
    {
        $this->body = json_encode($params);
        return $this;
    }

    /**
     * 请求接口
     * @param string $type
     * @return $this
     */
    private function setUrl(string $type): self
    {
        $this->baseURL .= $type;
        return $this;
    }

    /**
     * 请求类型
     * @param string $type
     * @return $this
     */
    private function setMethod(string $type): self
    {
        $this->method = $type;
        return $this;
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    private function withHeader($name, $value): self
    {
        $this->headers = array_merge($this->headers, [$name => $value]);
        return $this;
    }

    /**
     * 返回数组数据
     * @return array
     */
    public function toArray(): array
    {
        //首先检测是否支持curl
        if (!extension_loaded("curl")) {
            throw new HttpException(404, '请开启curl模块！');
        }
        // 签名
        $this->headers["Authorization"] = $this->authorizationV2($this->baseURL, $this->method, $this->body, $this->headers["content-type"]);
        $this->headers['Content-Type'] = $this->headers["content-type"];
        // 准备请求
        $request = new Request($this->method, $this->baseURL, $this->headers, $this->body);
        // 发送请求
        $ret = $this->sendRequest($request);
        if (!$ret->ok()) {
            return array(null, new Error($this->baseURL, $ret));
        }
        $r = ($ret->body === null) ? array() : $ret->json();
        return array($r, null);
    }

    /**
     * 网络请求
     * @param $request
     * @return Response
     */
    public function sendRequest($request): Response
    {
        $t1 = microtime(true);
        $ch = curl_init();
        $options = array(
            CURLOPT_USERAGENT => $this->userAgent(),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HEADER => true,
            CURLOPT_NOBODY => false,
            CURLOPT_CUSTOMREQUEST => $request->method,
            CURLOPT_URL => $request->url,
        );
        // Handle open_basedir & safe mode
        if (!ini_get('safe_mode') && !ini_get('open_basedir')) {
            $options[CURLOPT_FOLLOWLOCATION] = true;
        }
        if (!empty($request->headers)) {
            $headers = array();
            foreach ($request->headers as $key => $val) {
                array_push($headers, "$key: $val");
            }
            $options[CURLOPT_HTTPHEADER] = $headers;
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
        if (!empty($request->body)) {
            $options[CURLOPT_POSTFIELDS] = $request->body;
        }
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        $t2 = microtime(true);
        $duration = round($t2 - $t1, 3);
        $ret = curl_errno($ch);
        if ($ret !== 0) {
            $r = new Response(-1, $duration, array(), null, curl_error($ch));
            curl_close($ch);
            return $r;
        }
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headers = $this->parseHeaders(substr($result, 0, $header_size));
        $body = substr($result, $header_size);
        curl_close($ch);
        return new Response($code, $duration, $headers, $body, null);
    }

    private function userAgent(): string
    {
        $sdkInfo = "QiniuPHP/" . Config::SDK_VER;

        $systemInfo = php_uname("s");
        $machineInfo = php_uname("m");

        $envInfo = "($systemInfo/$machineInfo)";

        $phpVer = phpversion();

        return "$sdkInfo $envInfo PHP/$phpVer";
    }

    /**
     * @param $raw
     * @return array
     */
    private function parseHeaders($raw): array
    {
        $headers = array();
        $headerLines = explode("\r\n", $raw);
        foreach ($headerLines as $line) {
            $headerLine = trim($line);
            $kv = explode(':', $headerLine);
            if (count($kv) > 1) {
                $kv[0] = $this->ucwordsHyphen($kv[0]);
                $headers[$kv[0]] = trim($kv[1]);
            }
        }
        return $headers;
    }

    /**
     * @param $str
     * @return array|string|string[]
     */
    private function ucwordsHyphen($str)
    {
        return str_replace('- ', '-', ucwords(str_replace('-', '- ', $str)));
    }

    /**
     * @param $url
     * @param $method
     * @param null $body
     * @param null $contentType
     * @return string
     */
    public function authorizationV2($url, $method, $body = null, $contentType = null): string
    {
        $urlItems = parse_url($url);
        $host = $urlItems['host'];

        if (isset($urlItems['port'])) {
            $port = $urlItems['port'];
        } else {
            $port = '';
        }

        $path = $urlItems['path'];
        if (isset($urlItems['query'])) {
            $query = $urlItems['query'];
        } else {
            $query = '';
        }

        //write request uri
        $toSignStr = $method . ' ' . $path;
        if (!empty($query)) {
            $toSignStr .= '?' . $query;
        }

        //write host and port
        $toSignStr .= "\nHost: " . $host;
        if (!empty($port)) {
            $toSignStr .= ":" . $port;
        }

        //write content type
        if (!empty($contentType)) {
            $toSignStr .= "\nContent-Type: " . $contentType;
        }

        $toSignStr .= "\n\n";

        //write body
        if (!empty($body)) {
            $toSignStr .= $body;
        }

        $sign = $this->sign($toSignStr);
        return 'Qiniu ' . $sign;
    }

    private function sign($data): string
    {
        $hmac = hash_hmac('sha1', $data, $this->secretKey, true);
        return $this->accessKey . ':' . $this->base64_urlSafeEncode($hmac);
    }

    private function base64_urlSafeEncode(string $data): string
    {
        $find = array('+', '/');
        $replace = array('-', '_');
        return str_replace($find, $replace, base64_encode($data));
    }
}