<?
class api
{
    private $epoch;
    private $iso8601;
    private $remote;
    private $sender;

    const version = 'v0.01';

    public function epoch() {
        $this->epoch = time();
        $this->sender = array(
            "epoch" => $this->epoch
        );
        echo json_encode($this->sender);
    }

    public function iso8601() {
        $this->iso8601 = date(DATE_ATOM);
        $this->sender = array(
            "iso8601" => $this->iso8601
        );
        echo json_encode($this->sender);
    }

    public function remote() {
        $this->remote = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
        $this->sender = array(
            "remote" => $this->remote
        );
        echo json_encode($this->sender);
    }

    public function notfound() {
        header("HTTP/1.1 404 Not Found");
        $this->sender = array(
            "error" => "not found."
        );
        echo json_encode($this->sender);
    }
}
?>