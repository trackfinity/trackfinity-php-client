<?php
namespace TrackFinity;
/**
 * Class FilterRequest
 */
class FilterRequest
{
    /**
     * @var array
     */
    public $client  = [];
    /**
     * @var array
     */
    public $headers = [];
    /**
     * @var array
     */
    public $server = [];

    /**
     * FilterRequest constructor.
     */
    function __construct()
    {
        $this->buildAllHeaders();
        $this->buildClientInfomation();
        $this->buildServerInfomation();
    }

    /**
     * @return array
     */
    function getDataAsArray()
    {
        $return            = [];
        $return['client']  = (array)$this->client;
        $return['headers'] = (array)$this->headers;
        $return['server']  = (array)$this->server;

        return $return;
    }

    /**
     * Get all HTTP header key/values as an associative array for the current request.
     *
     * @return string[string] The HTTP header key/value pairs.
     */
    function buildAllHeaders()
    {
        $headers     = [];
        $copy_server = [
            'CONTENT_TYPE'   => 'Content-Type',
            'CONTENT_LENGTH' => 'Content-Length',
            'CONTENT_MD5'    => 'Content-Md5',
        ];
        foreach ($_SERVER as $key => $value) {
            if (substr($key, 0, 5) === 'HTTP_') {
                $key = substr($key, 5);
                if (!isset($copy_server[$key]) || !isset($_SERVER[$key])) {
                    $key           = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $key))));
                    $headers[$key] = $value;
                }
            } elseif (isset($copy_server[$key])) {
                $headers[$copy_server[$key]] = $value;
            }
        }
        if (!isset($headers['Authorization'])) {
            if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
                $headers['Authorization'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
            } elseif (isset($_SERVER['PHP_AUTH_USER'])) {
                $basic_pass               = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '';
                $headers['Authorization'] = 'Basic ' . base64_encode($_SERVER['PHP_AUTH_USER'] . ':' . $basic_pass);
            } elseif (isset($_SERVER['PHP_AUTH_DIGEST'])) {
                $headers['Authorization'] = $_SERVER['PHP_AUTH_DIGEST'];
            }
        }
        $this->headers = $headers;

        return $this;
    }

    /**
     * @return $this
     */
    function buildClientInfomation()
    {
        $this->client['ipaddress']   = $this->getClientIP();
        $this->client['referer']     = $this->getReferer();
        $this->client['requestUri']  = $_SERVER['REQUEST_URI'];
        $this->client['queryString'] = $_SERVER['QUERY_STRING'];

        return $this;
    }

    /**
     *
     */
    function buildServerInfomation()
    {
        $this->server = [];
    }

    /**
     * @return string
     */
    function getReferer()
    {
        $referer = '';
        if (isset($_SERVER['HTTP_REFERER'])) {
            $referer = $_SERVER['HTTP_REFERER'];
        }

        return $referer;
    }

    /**
     * @return mixed
     */
    function getClientIP()
    {
        //TODO support cloudflare and other DNS real IP headers
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];

        if (filter_var($client, FILTER_VALIDATE_IP)) {
            $ip = $client;
        } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
            $ip = $forward;
        } else {
            $ip = $remote;
        }

        return $ip;
    }
}