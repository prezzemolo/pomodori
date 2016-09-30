<?php
namespace pomodori;

class router
{
    private $url;
    private $method = null;
    private $param = null;

    /**
     * setParseUrl
     */
    public function setParam($param) {
        list($this->url, $this->method, $this->param) = array(rtrim($param['url'], '/'), $param['method'], $param['param']);
    }

    /**
     * Parse URL
     */
    public function parseUrl() {
        $route = 'notfound';
        $param = null;
        if ($this->method === 'POST') {
            switch (true) {
                case $this->url === '/base64/decode':
                    $route = 'base64Decode';
                    $param = isset($this->param['string'])
                        ? $this->param['string']
                        : null;
                    break;

                case $this->url === '/base64/encode':
                    $route = 'base64Encode';
                    $param = isset($this->param['string'])
                        ? $this->param['string']
                        : null;
                    break;
            }
        }

        if ($this->method === 'GET') {
            switch (true) {
                case $this->url === '':
                    $route = 'index';
                    break;

                case $this->url === '/time/epoch':
                    $route = 'epoch';
                    break;

                case $this->url === '/time/iso8601':
                    $route = 'iso8601';
                    break;

                case $this->url === '/ip/remote':
                    $route = 'remote';
                    break;

                case $this->url === '/meta':
                    $route = 'meta';
                    break;

                case $this->url === '/nicovideo/info':
                    $route = 'nicovideoInfo';
                    $param = isset($this->param['videoId'])
                        ? $this->param['videoId']
                        : null;
                    break;
            }
        }

        return array($route, $param);
    }
}
?>