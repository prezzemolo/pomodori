<?php
namespace pomodori;

class router
{
    /**
     * setParseUrl
     */
    public function set_param($param) {
        list($this->url, $this->method, $this->param) = array(rtrim($param['url'], '/'), $param['method'], $param['param']);
    }

    /**
     * Parse URL
     */
    public function parse() {
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
                $route = 'nicovideo_info';
                $element = array('id');
                $method = 'GET';
                break;

            case $this->url === '/base64/decode':
                $route = 'base64_decode';
                $element = array('string');
                $method = 'POST';
                break;

            case $this->url === '/base64/encode':
                $route = 'base64_encode';
                $element = array('string');
                $method = 'POST';
                break;

            default:
                return array('not_found', null);
        }

        /**
         * check method
         */
        if ($this->method !== $method)
            return array('invaild_method', null);

        /**
         * assign param
         */
        if (isset($element)) {
            $self = $this;
            $material = array_map(
                function($name) use ($self){
                    return isset($self->param[$name]) ? $self->param[$name] : null;
                },$element);
            $param = array_combine($element, $material);
         }

        /**
         * return result
         */
        return array($route, isset($param) ? $param : null);
    }
}
?>