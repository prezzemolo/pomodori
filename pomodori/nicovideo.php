<?
namespace pomodori\nicovideo;

class info
{
    public function getDataFromAPI($videoId) {
        // default
        $category = null;
        $userNickname = null;
        $userImage = null;
        $userSecret = true;

        // retrive api data
        $thumbRaw = file_get_contents('http://ext.nicovideo.jp/api/getthumbinfo/' . $videoId);
        $infoRaw = file_get_contents('http://api.ce.nicovideo.jp/nicoapi/v1/video.info?__format=json&v=' . $videoId);
        $thumbFormatted = simplexml_load_string($thumbRaw);
        $infoFormatted = json_decode($infoRaw);
        $thumbStatus = (string)$thumbFormatted->attributes()->status;
        $infoStatus = $infoFormatted->nicovideo_video_response->{'@status'};

        // error check
        if ($thumbStatus === 'fail' && $infoStatus === 'fail') {
            $thumbErrorCode = (string)$thumbFormatted->error->code;
            $infoErrorCode = $infoFormatted->nicovideo_video_response->error->code;
            if ($thumbErrorCode === 'NOT_FOUND' && $infoErrorCode === 'NOT_FOUND')
                return array(
                    "code" => 404,
                    "detail" => 'video is not found.'
                );
            return array(
                "code" => 410,
                "detail" => 'private or deleted video.'
            );
        }

        if ($infoStatus === 'ok') {
            $deleted = (string) $infoFormatted->nicovideo_video_response->video->deleted === '1' ? true : false;
            $userId = (int) $infoFormatted->nicovideo_video_response->video->user_id;
            $userRaw = file_get_contents('http://api.ce.nicovideo.jp/api/v1/user.info?__format=json&user_id=' . $userId);
            $userFormatted = json_decode($userRaw);
            if ($userFormatted->nicovideo_user_response->{'@status'} === 'ok') {
                $userNickname = (string) $userFormatted->nicovideo_user_response->user->nickname;
                $userImage = (string) $userFormatted->nicovideo_user_response->user->thumbnail_url;
                $userSecret = (string) $userFormatted->nicovideo_user_response->user->thumbnail_url === '1' ? true : false;
            }
            $comment = (int) $infoFormatted->nicovideo_video_response->thread->num_res;
            $rawDescription = mb_convert_kana((string) $infoFormatted->nicovideo_video_response->video->description, 'as', 'UTF-8');
            $description = $rawDescription === '&nbsp;'
                ? null
                : $rawDescription === ''
                ? null
                : $rawDescription;
            $image = $deleted === true
                ? 'http://res.nimg.jp/img/common/video_deleted.jpg'
                : (string) $infoFormatted->nicovideo_video_response->video->options->{'@large_thumbnail'} === '1'
                ? (string) $infoFormatted->nicovideo_video_response->video->thumbnail_url . '.L'
                : (string) $infoFormatted->nicovideo_video_response->video->thumbnail_url;
            $title = (string) $infoFormatted->nicovideo_video_response->video->title;
            $myList = (int) $infoFormatted->nicovideo_video_response->video->mylist_counter;
            $view = (int) $infoFormatted->nicovideo_video_response->video->view_counter;
            $rawSeconds = (int) $infoFormatted->nicovideo_video_response->video->length_in_seconds;
            $rawMinutes = (int) intval($rawSeconds / 60);
            $hours = (int) intval($rawMinutes / 60);
            $minutes = (int) $rawMinutes % 60;
            $seconds = (int) $rawSeconds % 60;
            $time = $hours > 0
                ? sprintf("%d:%02d:%02d", $hours, $minutes, $seconds)
                : sprintf("%d:%02d", $minutes, $seconds);
            if ($thumbStatus === 'ok' && isset($thumbFormatted->thumb->tags->tag->attributes()->category)) {
                $category = (string)$thumbFormatted->thumb->tags->tag[(int)$thumbFormatted->thumb->tags->tag->attributes()->category - 1];
            }
            $reported = (string) $infoFormatted->nicovideo_video_response->video->options->{'@mobile'} === '1' ? true : false;
            $uploaded = (string) $infoFormatted->nicovideo_video_response->video->first_retrieve;
            return array(
                "code" => 200,
                "deleted" => $deleted,
                "category" => $category,
                "comment" => $comment,
                "description" => $description,
                "hours" => $hours,
                "image" => $image,
                "time" => $time,
                "title" => $title,
                "minutes" => $minutes,
                "myList" => $myList,
                "reported" => $reported,
                "seconds" => $seconds,
                "uploaded" => $uploaded,
                "userNickname" => $userNickname,
                "userId" => $userId,
                "userImage" => $userImage,
                "userSecret" => $userSecret,
                "view" => $view
            );
        }

        return array(
            "code" => 500,
            "detail" => 'unable to interpret the nicovideo api.'
        );
    }

    private function saveDataToDB($videoId, $data) {
        return null;
    }

    public function getDataFromDB($videoId) {
        return null;
    }

    public function getData($videoId) {
        $data = $this->getDataFromDB($videoId);
        if ($deta === null) {
            $data = $this->getDataFromAPI($videoId);
            $this->saveDataToDB($videoId, $data);
        }
        return $data;
    }
}
?>