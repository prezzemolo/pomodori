<?
namespace pomodori\nicovideo;

class info
{
    private function connection_db () {
        try {
            // connect
            $this->db = new \PDO("sqlite:__DIR__/../ndb.db");
            // throw exception
            $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            // change default fetch mode
            $this->db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
            // emulate off
            $this->db->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);

            // create table
            $this->db->exec("CREATE TABLE IF NOT EXISTS video(
                id nchar(15) primary key,
                code int,
                deleted bit,
                category nchar(30),
                comment int,
                description nchar(1000),
                image nchar(100),
                time nchar(100),
                time_hours int,
                time_minutes int,
                time_seconds int,
                title nchar(100),
                my_list int,
                reported bit,
                updated_at nchar(30),
                uploaded_at nchar(30),
                user_nickname nchar(16),
                user_id int,
                user_image nchar(100),
                user_secret bit,
                view int
            )");
            return;
        } catch (Exception $e) {
            $this->db = null;
            return;
        }
    }

    private function closing_db () {
        $this->db = null;
    }

    private function save_to_db($id, $data) {
        if (!isset($this->db)) {
            return;
        }
        if ($data['code'] !== 200) {
            return;
        }
        $savedata = array_values($data);
        array_unshift($savedata, $id);
        try {
            $sql = "INSERT INTO video (
                id,
                code,
                deleted,
                category,
                comment,
                description,
                image,
                time,
                time_hours,
                time_minutes,
                time_seconds,
                title,
                my_list,
                reported,
                updated_at,
                uploaded_at,
                user_nickname,
                user_id,
                user_image,
                user_secret,
                view
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $insert = $this->db->prepare($sql);
            $insert->execute($savedata);
            $this->db->query("VACUUM");
        } catch (Exception $e) {
            return;
        }
        return;
    }

    private function get_from_db($id) {
        if (!isset($this->db)) {
            return;
        }
        try {
            $search = $this->db->prepare("SELECT
            code,
            deleted,
            category,
            comment,
            description,
            image,
            time,
            time_hours,
            time_minutes,
            time_seconds,
            title,
            my_list,
            reported,
            updated_at,
            uploaded_at,
            user_nickname,
            user_id,
            user_image,
            user_secret,
            view
            FROM video WHERE id = ?");
            $search->execute(array($id));
            $result = $search->fetch();
            if ($result === false) {
                return;
            }
        } catch (Exception $e) {
            return;
        }
        $data = $result;
        // change to correct type (type casting)
        $data['code'] = 200;
        $data['deleted'] = (boolean) $data['deleted'];
        $data['comment'] = (int) $data['comment'];
        $data['time_hours'] = (int) $data['time_hours'];
        $data['time_minutes'] = (int) $data['time_minutes'];
        $data['time_seconds'] = (int) $data['time_seconds'];
        $data['my_list'] = (int) $data['my_list'];
        $data['reported'] = (boolean) $data['reported'];
        $data['user_id'] = (int) $data['user_id'];
        $data['user_secret'] = (boolean) $data['user_secret'];
        $data['view'] = (int) $data['view'];
        return $data;
    }

    private function get_from_api($id) {
        // default
        $category = null;
        $user_nickname = null;
        $user_image = null;
        $user_secret = true;

        // retrive api data
        $updated_at = date(DATE_ATOM);
        $thumb_raw = file_get_contents('http://ext.nicovideo.jp/api/getthumbinfo/' . $id);
        $info_raw = file_get_contents('http://api.ce.nicovideo.jp/nicoapi/v1/video.info?__format=json&v=' . $id);
        $thumb_formatted = simplexml_load_string($thumb_raw);
        $info_formatted = json_decode($info_raw);
        $thumb_status = (string)$thumb_formatted->attributes()->status;
        $info_status = $info_formatted->nicovideo_video_response->{'@status'};

        // error check
        if ($thumb_status === 'fail' && $info_status === 'fail') {
            $thumb_error = (string)$thumb_formatted->error->code;
            $info_error = $info_formatted->nicovideo_video_response->error->code;
            if ($thumb_error === 'NOT_FOUND' && $info_error === 'NOT_FOUND')
                return array(
                    "code" => 404,
                    "detail" => 'video is not found.'
                );
            return array(
                "code" => 410,
                "detail" => 'private or deleted video.'
            );
        }

        if ($info_status === 'ok') {
            $deleted = (string) $info_formatted->nicovideo_video_response->video->deleted === '1' ? true : false;
            $user_id = (int) $info_formatted->nicovideo_video_response->video->user_id;
            $user_raw = file_get_contents('http://api.ce.nicovideo.jp/api/v1/user.info?__format=json&user_id=' . $user_id);
            $user_formatted = json_decode($user_raw);
            if ($user_formatted->nicovideo_user_response->{'@status'} === 'ok') {
                $user_nickname = (string) $user_formatted->nicovideo_user_response->user->nickname;
                $user_image = (string) $user_formatted->nicovideo_user_response->user->thumbnail_url;
                $user_secret = (string) $user_formatted->nicovideo_user_response->vita_option->user_secret === '1' ? true : false;
            }
            $comment = (int) $info_formatted->nicovideo_video_response->thread->num_res;
            $raw_description = mb_convert_kana((string) $info_formatted->nicovideo_video_response->video->description, 'as', 'UTF-8');
            $description = $raw_description === '&nbsp;'
                ? null
                : $raw_description === ''
                ? null
                : $raw_description;
            $image = $deleted === true
                ? 'http://res.nimg.jp/img/common/video_deleted.jpg'
                : (string) $info_formatted->nicovideo_video_response->video->options->{'@large_thumbnail'} === '1'
                ? (string) $info_formatted->nicovideo_video_response->video->thumbnail_url . '.L'
                : (string) $info_formatted->nicovideo_video_response->video->thumbnail_url;
            $title = (string) $info_formatted->nicovideo_video_response->video->title;
            $my_list = (int) $info_formatted->nicovideo_video_response->video->mylist_counter;
            $view = (int) $info_formatted->nicovideo_video_response->video->view_counter;
            $raw_seconds = (int) $info_formatted->nicovideo_video_response->video->length_in_seconds;
            $raw_minutes = (int) intval($raw_seconds / 60);
            $time_hours = (int) intval($raw_minutes / 60);
            $time_minutes = (int) $raw_minutes % 60;
            $time_seconds = (int) $raw_seconds % 60;
            $time = $hours > 0
                ? sprintf("%d:%02d:%02d", $time_hours, $time_minutes, $time_seconds)
                : sprintf("%d:%02d", $time_minutes, $time_seconds);
            if ($thumb_status === 'ok' && isset($thumb_formatted->thumb->tags->tag->attributes()->category)) {
                $category = (string)$thumb_formatted->thumb->tags->tag[(int)$thumb_formatted->thumb->tags->tag->attributes()->category - 1];
            }
            $reported = (string) $info_formatted->nicovideo_video_response->video->options->{'@mobile'} === '1' ? true : false;
            $uploaded_at = (string) $info_formatted->nicovideo_video_response->video->first_retrieve;
            return array(
                "code" => 200,
                "deleted" => $deleted,
                "category" => $category,
                "comment" => $comment,
                "description" => $description,
                "image" => $image,
                "time" => $time,
                "time_hours" => $time_hours,
                "time_minutes" => $time_minutes,
                "time_seconds" => $time_seconds,
                "title" => $title,
                "my_list" => $my_list,
                "reported" => $reported,
                "updated_at" => $updated_at,
                "uploaded_at" => $uploaded_at,
                "user_nickname" => $user_nickname,
                "user_id" => $user_id,
                "user_image" => $user_image,
                "user_secret" => $user_secret,
                "view" => $view
            );
        }

        return array(
            "code" => 500,
            "detail" => 'unable to interpret the nicovideo api.'
        );
    }

    public function get($id) {
        $this->connection_db();
        $data = $this->get_from_db($id);
        if (!isset($data)) {
            $data = $this->get_from_api($id);
            $this->save_to_db($id, $data);
        }
        $this->closing_db();
        return $data;
    }
}
?>