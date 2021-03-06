<?
namespace pomodori;
require_once __DIR__.'/nicovideo.php';

class api
{
    const version = 'v0.08';

    // CORS preflight
    public function preflight ($method) {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Request-Method: ' . $method);
        return;
    }

    // preheader (internal use)
    private function pre() {
        header('Content-Type: application/json');
        header('X-Powered-By: pomodori api '.$this::version);
        return;
    }

    // gen UUID v4
    private function gen_uuid() {
        for ($i = 0x1; $i <= 0x20; $i++) {
            switch (true) {
                case $i === 0xD:
                    $upperUUID .= '4';
                    break;
                case $i === 0x11:
                    $upperUUID .= dechex(0b1000 + mt_rand(0b00, 0b11));
                    break;
                default:
                    $upperUUID .= dechex(mt_rand(0x0, 0xF));
            }
            if ($i === 0x8 || $i === 0xC || $i === 0x10 || $i === 0x14) {
                $upperUUID .= '-';
            }
        }
        return strtolower($upperUUID);
    }

    // special: client sent invaild method
    public function invaild_method() {
        $this->pre();
        http_response_code(400);
        echo json_encode(array(
            'code' => http_response_code(),
            'detail' => 'please use vaild HTTP method.'
        ));
    }

    // special: not found
    public function not_found() {
        $this->pre();
        http_response_code(404);
        echo json_encode(array(
            'code' => http_response_code(),
            'detail' => 'content is not found.'
        ));
        return;
    }

    // special: index (URL not specified)
    public function index() {
        $this->pre();
        echo json_encode(array(
            'code' => http_response_code(),
            'detail' => 'This is pomodori api server.'
        ));
        return;
    }

    // time/epoch ... Return server time formated Epoch
    public function time_epoch() {
        $this->pre();
        echo json_encode(array(
            'code' => http_response_code(),
            'epoch' => time()
        ));
        return;
    }

    // time/iso8601 ... Return server time formated ISO8601
    public function time_iso8601() {
        $this->pre();
        echo json_encode(array(
            'code' => http_response_code(),
            'iso8601' => date(DATE_ATOM)
        ));
        return;
    }

    // ip/remote ... Return remote IP address
    public function ip_remote() {
        $this->pre();
        echo json_encode(array(
            'code' => http_response_code(),
            'remote' => isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']
        ));
        return;
    }

    // meta ... Return server information
    public function meta() {
        $this->pre();
        echo json_encode(array(
            'code' => http_response_code(),
            'version' => $this::version,
            'detail' => 'an api server for my php learning.',
            'git' => 'https://github.com/prezzemolo/pomodori'
        ));
        return;
    }

    // nicovideo/info ... Return niconico video information wrap nicovideo internal API
    public function nicovideo_info($param = null) {
        $info = new nicovideo\info();
        $this->pre();
        if (!isset($param['id'])){
            http_response_code(400);
            echo json_encode(array(
                'code' => http_response_code(),
                'detail' => 'please set GET id parameter.'
            ));
            return;
        }
        if (!preg_match('/^(sm|so|nm|)([0-9]+)$/', $param['id'])){
            http_response_code(400);
            echo json_encode(array(
                'code' => http_response_code(),
                'detail' => 'please set valid nicovideo\'s video ID.'
            ));
            return;
        }
        $video_info = $info->get($param['id']);
        http_response_code($video_info['code']);
        echo json_encode($video_info);
        return;
    }

    // base64/decode ... return string decorded base64.
    public function base64_decode($param = null) {
        $this->pre();
        if (!isset($param['string'])){
            http_response_code(400);
            echo json_encode(array(
                'code' => http_response_code(),
                'detail' => 'please set POST string parameter.'
            ));
            return;
        }
        echo json_encode(array(
            'code' => http_response_code(),
            'decorded' => base64_decode($param['string'])
        ));
        return;
    }

    // base64/encode ... return string encorded base64.
    public function base64_encode($param = null) {
        $this->pre();
        if (!isset($param['string'])){
            http_response_code(400);
            echo json_encode(array(
                'code' => http_response_code(),
                'detail' => 'please set POST string parameter.'
            ));
            return;
        }
        echo json_encode(array(
            'code' => http_response_code(),
            'encorded' => base64_encode($param['string'])
        ));
        return;
    }

    // uuid/generate ... gen UUID ver4
    public function uuid_generate() {
        $this->pre();
        echo json_encode(array(
            'code' => http_response_code(),
            'uuid' => $this->gen_uuid()
        ));
        return;
    }
}
?>