<?php
namespace pomodori;

class Router
{
    private $url;

    /**
     * setParseUrl
     */
    public function setUrl($url) {
        $this->url = rtrim($url, '/');
    }

    /**
     * Parse URL
     */
    public function parseUrl() {
        switch (true) {
            case $this->url === "":
                $parsed = "index";
                break;

            case $this->url === "/time/epoch":
                $parsed = "epoch";
                break;

            case $this->url === "/time/iso8601":
                $parsed = "iso8601";
                break;

            case $this->url === "/ip/remote":
                $parsed = "remote";
                break;

            case $this->url === "/meta":
                $parsed = "meta";
                break;

            default:
                $this->parsed = "notfound";
        }
        return $parsed;
    }
}
?>