<?
namespace pomodori\nicovideo;

class info
{
    public function getDataFromAPI($videoId) {
        // default
        $category = null;

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
            $comment = (int) $infoFormatted->nicovideo_video_response->thread->num_res;
            $description = (string) $infoFormatted->nicovideo_video_response->video->description === '&nbsp;'
				? null
				: (string) $infoFormatted->nicovideo_video_response->video->description === '　'
				? null
                : (string) $infoFormatted->nicovideo_video_response->video->description;
            $image = $deleted === true
				? 'http://res.nimg.jp/img/common/video_deleted.jpg'
				: (string) $infoFormatted->nicovideo_video_response->video->options->{'@large_thumbnail'} === '1'
				? (string) $infoFormatted->nicovideo_video_response->video->thumbnail_url . '.L'
                : (string) $infoFormatted->nicovideo_video_response->video->thumbnail_url;
            $title = (string) $infoFormatted->nicovideo_video_response->video->title;
            $myList = (int) $infoFormatted->nicovideo_video_response->video->mylist_counter;
            $view = (int) $infoFormatted->nicovideo_video_response->video->view_counter;
            if ($thumbStatus === 'ok' && isset($thumbFormatted->thumb->tags->tag->attributes()->category)) {
                $category = (string) $thumbFormatted->thumb->tags->tag[(int)$thumbFormatted->thumb->tags->tag->attributes()->category];
            }
            return array(
                "code" => 200,
                "deleted" => $deleted,
                "category" => $category,
                "comment" => $comment,
                "description" => $description,
                "image" => $image,
                "title" => $title,
                "myList" => $myList,
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