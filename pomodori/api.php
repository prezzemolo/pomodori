<?
namespace pomodori;
require_once __DIR__.'/nicovideo.php';

class api
{
    const version = 'v0.03';

    private function default_header() {
        header('Content-Type: application/json');
        header('X-Powered-By: pomodori api '.$this::version);
        return;
    }

    public function epoch() {
        $this->default_header();
        echo json_encode(array(
            'code' => http_response_code(),
            'epoch' => time()
        ));
        return;
    }

    public function iso8601() {
        $this->default_header();
        echo json_encode(array(
            'code' => http_response_code(),
            'iso8601' => date(DATE_ATOM)
        ));
        return;
    }

    public function remote() {
        $this->default_header();
        echo json_encode(array(
            'code' => http_response_code(),
            'remote' => isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']
        ));
        return;
    }

    public function notfound() {
        $this->default_header();
        http_response_code(404);
        echo json_encode(array(
            'code' => http_response_code(),
            'detail' => 'content not found.'
        ));
        return;
    }

    public function meta() {
        $this->default_header();
        echo json_encode(array(
            'code' => http_response_code(),
            'version' => $this::version,
            'detail' => 'an api server for my php learning.',
            'git' => 'https://github.com/prezzemolo/pomodori'
        ));
        return;
    }

    public function index() {
        $this->default_header();
        echo json_encode(array(
            'code' => http_response_code(),
            'detail' => 'This is pomodori api server.'
        ));
        return;
    }

    public function nicovideoInfo($videoId = null) {
        $this->default_header();
        if (!isset($videoId)){
            http_response_code(400);
            $sender = array(
                'code' => http_response_code(),
                'detail' => 'please set GET videoId parameter.'
            );
            echo json_encode($sender);
            return;
        }
        $sender = array(
            'code' => http_response_code(),
            'videoId' => $videoId
        );
        echo json_encode($sender);
        return;
    }
}
?>