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
        $method = null;
        $element = null;
        $material = null;
        $param = null;

        /**
         * specific route
         */
        switch (true) {
            case $this->url === '':
                $route = 'index';
                $method = 'GET';
                break;

            case $this->url === '/time/epoch':
                $route = 'epoch';
                $method = 'GET';
                break;

            case $this->url === '/time/iso8601':
                $route = 'iso8601';
                $method = 'GET';
                break;

            case $this->url === '/ip/remote':
                $route = 'remote';
                $method = 'GET';
                break;

            case $this->url === '/meta':
                $route = 'meta';
                $method = 'GET';
                break;

            case $this->url === '/nicovideo/info':
                $route = 'nicovideoInfo';
                $element = array('videoId');
                $method = 'GET';
                break;

            case $this->url === '/base64/decode':
                $route = 'base64Decode';
                $element = array('string');
                $method = 'POST';
                break;

            case $this->url === '/base64/encode':
                $route = 'base64Encode';
                $element = array('string');
                $method = 'POST';
                break;
        }

        /**
         * check method
         */
        if ($this->method !== $method)
            return array('invaildMethod', null);

        /**
         * assign param
         */
        if (isset($element)) {
            $self = $this;
            $material = array_map(
                function($name) use ($self){
                    return isset($self->param[$name]) ? $self->param[$name] : null;
                },
                $element
            );
            $param = array_combine($element, $material);
         }

        /**
         * return result
         */
        return array($route, $param);
    }
}
?>