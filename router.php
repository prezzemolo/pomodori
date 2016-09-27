<?php
namespace pomodori;

class Router
{
    private $url;
    private $parsed;

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
                $this->parsed = "index";
                break;

            case $this->url === "/time/epoch":
                $this->parsed = "epoch";
                break;

            case $this->url === "/time/iso8601":
                $this->parsed = "iso8601";
                break;

            case $this->url === "/ip/remote":
                $this->parsed = "remote";
                break;

            case $this->url === "/meta":
                $this->parsed = "meta";
                break;

            default:
                $this->parsed = "notfound";
        }
        return $this->parsed;
    }
}
?>