<?
namespace pomodori;

class api
{
    const version = 'v0.03';

    private function default_header() {
        header("Content-Type: application/json");
        header("X-Powered-By: pomodori api ".$this::version);
    }

    public function epoch() {
        $this->default_header();
        $sender = array(
            "code" => http_response_code(),
            "epoch" => time()
        );
        echo json_encode($sender);
    }

    public function iso8601() {
        $this->default_header();
        $sender = array(
            "code" => http_response_code(),
            "iso8601" => date(DATE_ATOM)
        );
        echo json_encode($sender);
    }

    public function remote() {
        $this->default_header();
        $sender = array(
            "code" => http_response_code(),
            "remote" => isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']
        );
        echo json_encode($sender);
    }

    public function notfound() {
        $this->default_header();
        http_response_code(404);
        $sender = array(
            "code" => http_response_code(),
            "detail" => "NOT_FOUND"
        );
        echo json_encode($sender);
    }

    public function meta() {
        $this->default_header();
        $sender = array(
            "code" => http_response_code(),
            "version" => $this::version,
            "detail" => "an api server for my php learning.",
            "git" => "https://github.com/prezzemolo/pomodori"
        );
        echo json_encode($sender);
    }

    public function index() {
        $this->default_header();
        $sender = array(
            "code" => http_response_code(),
            "detail" => "This is pomodori api server."
        );
        echo json_encode($sender);
    }
}
?>