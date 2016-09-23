<?
class api
{
    private $epoch;
    private $iso8601;
    private $remote;
    private $sender;

    const version = 'v0.01';

    private function default_header() {
        header("Content-Type: application/json");
        header("Server: pomodori api ".$version);
        header("X-Powered-By: MIT License");
    }

    public function epoch() {
        $this->default_header();
        $this->epoch = time();
        $this->sender = array(
            "status" => 200,
            "epoch" => $this->epoch
        );
        echo json_encode($this->sender);
    }

    public function iso8601() {
        $this->default_header();
        $this->iso8601 = date(DATE_ATOM);
        $this->sender = array(
            "status" => 200,
            "iso8601" => $this->iso8601
        );
        echo json_encode($this->sender);
    }

    public function remote() {
        $this->default_header();
        $this->remote = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
        $this->sender = array(
            "status" => 200,
            "remote" => $this->remote
        );
        echo json_encode($this->sender);
    }

    public function notfound() {
        $this->default_header();
        header("HTTP/1.1 404 Not Found");
        $this->sender = array(
            "status" => 404,
            "error" => "not found."
        );
        echo json_encode($this->sender);
    }

    public function meta() {
        $this->default_header();
        $this->sender = array(
            "status" => 200,
            "version" => $this->version
        );
        echo json_encode($this->sender);
    }
}
?>