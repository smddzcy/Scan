<?php

/**
 * Class ScanThreader
 *
 * @description Multi-threading processor for Scan class
 *
 */
class ScanThreader extends Thread
{

    protected $baseURL;
    protected $path;
    protected $fullURL;
    /**
     * @var int Process result (currently HTTP response code) is accessed by this variable
     */
    public $result;

    /**
     * @return string Base URL
     */
    public function getBaseURL()
    {
        return $this->baseURL;
    }

    /**
     * @param string $baseURL
     */
    public function setBaseURL($baseURL)
    {
        $this->baseURL = $baseURL;
    }

    /**
     * @return string Path
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return string Full URL
     */
    public function getFullURL()
    {
        return $this->fullURL;
    }

    /**
     * @param string $fullURL
     */
    public function setFullURL($fullURL)
    {
        $this->fullURL = $fullURL;
    }

    /**
     * Makes HTTP HEAD requests
     * @param string $url
     * @return mixed HTTP header of response
     */
    public function request($url)
    {
        if (function_exists('curl_version')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:40.0) Gecko/20100101 Firefox/40.0');
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_NOBODY, true); // Set request to HEAD
            $run = curl_exec($ch);
            curl_close($ch);
            return $run;
        } else {
            return (!empty($responseHeader = get_headers($url)[0])) ? $responseHeader : false;
        }
    }

    /**
     * @param string $baseURL
     * @param string $path
     */
    public function __construct($baseURL, $path)
    {
        if ($baseURL{strlen($baseURL) - 1} != '/')
            $baseURL .= '/';
        $this->baseURL = $baseURL;
        $this->path = $path;
        $this->fullURL = $baseURL . $path;
    }

    public function run()
    {
        $requestResult = $this->request($this->fullURL);
        if ($requestResult !== false) {
            $this->result = substr($requestResult, 9, 3); // Get the response code
        } else {
            $this->result = false;
        }
    }

}