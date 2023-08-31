<?php

namespace App\Http\Controller\Request;

use Exception;

class Request {
    function __construct() {
        $this->curl = '';
        $this->opturl = '';
        $this->optRequest = 'get';
        $this->optParams = '';
        $this->response = '';
    }
    public static function init($url = '') {
        $Request = new Request;

        return $Request;
    }

    private function setter ($key, $val) {
        $this->$key = $val;
        return $this;
    }

    private function getter ($key, $default = '') {
        if (isset($this->$key)&&$this->$key !== '') {
            $val = $this->$key;
        } else {
            $this->setter($key, $default);
            $val = $this->$key;
        }
        
        return $val;
    }

    public function setRequestMethod($method) {
        $this->setter('optRequest', $method);

        return $this;
    }

    public function setUrl($url) {
        $this->setter('opturl', $url);

        return $this;
    }

    public function setParams() {
        try {
            $reqParams = [];
            $params = func_get_args();
            $dparam = [];

            if ($this->getRequestMethod() == 'get') {
                for ($i = 0;$i < count($params);$i++) {
                    if (($i % 2) == 0) {
                        $dparam[$i] = $params[$i];
                    } else {
                        $dparam[($i - 1)] .= '=' . urlencode($params[$i]);
                    }
                }

                $reqParams = implode('&', $dparam);
            } else {
                for ($i = 0;$i < count($params);$i++) {
                    if (($i % 2) == 0) {
                        $dparam[$params[$i]] = '';
                    } else {
                        $dparam[$params[($i - 1)]] = $params[$i];
                    }
                }

                $reqParams = $dparam;
            }

            $this->setter('optParams', $reqParams);

            return $this;
        } catch (Exception $e) {
           return $e->getCode() . ':' . $e->getMessage();
        }
    }

    public function getUrl($default = '') {
        return $this->getter('opturl', $default);
    }

    public function getRequestMethod ($default = '') {
        return $this->getter('optRequest', $default);
    }

    public function getParams($default = '') {
        return $this->getter('optParams', $default);
    }

    public function getResponse($default = '') {
        return $this->getter('response', $default);
    }

    public function send() {
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->getUrl() . '?' . $this->getParams(),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $this;
    }
}