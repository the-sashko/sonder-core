<?php
/**
 * Plugin For Getting Youtube Metadata And Generating Embeded Player
 */
class YoutubePlugin
{
    const LINK_REGEXP = '/^(.*?)(https|http)\:\/\/'.
                        '(m\.youtube|www\.youtube|youtube)\.com\/'.
                        'watch(.*?)(\s(.*?)$|$)/su';

    const SHORT_LINK_REGEXP = '/(.*?)(https|http)\:\/\/'.
                              '(m\.youtu|www\.youtu|youtu)\.be\/'.
                              '(.*?)(\s(.*?)$|$)/su';

    const SHORTCODE_REGEXP = '/^(.*?)\[Youtube:(.*?)\](.*?)$/su';

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    public function parseYoutubeURL(string $text = '') : string
    {
        $text = $this->_normalizeURL($text);

        if (
            !preg_match(static::LINK_REGEXP, $text) &&
            !preg_match(static::SHORT_LINK_REGEXP, $text)
        ) {
            return $text;
        }

        $text = $this->_parseYoutubeFullURL($text);
        $text = $this->_parseYoutubeShortURL($text);

        return $this->parseYoutubeURL($text);
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    public function parseYoutubeShortCode(string $text = '') : string
    {
        if (!preg_match(static::SHORTCODE_REGEXP, $text)) {
            return $text;
        }

        $videoID = preg_replace(static::SHORTCODE_REGEXP, '$2', $text);

        $youtubeURL = "https://www.youtube.com/watch?v={$videoID}";

        $title = $this->_getTitleByID($videoID);

        $text = str_replace(
            "[Youtube:{$videoID}]",
            "<a href=\"{$youtubeURL}\"".
            "class=\"post_media_link post_content_link_youtube\">".
            "<i class=\"fab fa-youtube\"></i>&nbsp;{$videoTitle}".
            "</a>",
            $text
        );

        return $this->parseYoutubeShortCode($text);
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    public function getThumbnailByID(string $videoID = '') : string
    {
        $videoID = $this->_sanitizeVideoID($videoID);

        $thumbnailLocalPath = __DIR__."/res/img/{$videoID}.jpg";

        if (file_exists($thumbnailLocalPath) && is_file($thumbnailLocalPath)) {
            return $thumbnailLocalPath;
        }

        $thumbnailContent = $this->_getThumbnailContent($videoID);

        if (strlen($thumbnailContent) > 0) {
            file_put_contents($thumbnailLocalPath, $thumbnailContent);

            return $thumbnailLocalPath;
        }

        return __DIR__."/res/img/default.jpg";
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _getMetaData(string $videoID = '') : array
    {
        $videoID = $this->_sanitizeVideoID($videoID);

        $cacheFilePath = __DIR__."/cache/_{$videoID}.dat";

        if (!file_exists($cacheFilePath) || !file_exists($cacheFilePath)) {
            $metaDataURL = "https://www.youtube.com/".
                           "get_video_info?video_id={$videoID}";
            $metaData = file_get_contents ($metaDataURL);
            file_put_contents($cacheFilePath, base64_encode($metaData));
        } else {
            $metaData = file_get_contents($cacheFilePath);
            $metaData = base64_decode($metaData);
        }

        parse_str($metaData, $metaData);

        return $metaData;
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _sanitizeVideoID(string $videoID = '') : string
    {
        $videoID = explode('#', $videoID)[0];
        $videoID = explode('&', $videoID)[0];

        return explode('?', $videoID)[0];
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _getThumbnailURLs(string $videoID = '') : array
    {
        return [
            "https://img.youtube.com/vi/{$videoID}/maxresdefault.jpg",
            "https://img.youtube.com/vi/{$videoID}/hqdefault.jpg",
            "https://img.youtube.com/vi/{$videoID}/mqdefault.jpg",
            "https://img.youtube.com/vi/{$videoID}/default.jpg",
            "https://img.youtube.com/vi/{$videoID}/sddefault.jpg",
            "https://img.youtube.com/vi/{$videoID}/2.jpg",
            "https://img.youtube.com/vi/{$videoID}/3.jpg",
            "https://img.youtube.com/vi/{$videoID}/1.jpg",
            "https://img.youtube.com/vi/{$videoID}/0.jpg"
        ];
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _getThumbnailContent(string $videoID = '') : string
    {
        $thumbnailURLs = $this->_getThumbnailURLs($videoID);

        $thumbnailContent = false;

        foreach ($thumbnailURLs as $thumbnailURL) {
            if ($thumbnailContent != false) {
                break;
            }

            try {
                $content = file_get_contents($thumbnailURL);
            } catch(Exception $except) {
                $content = false;
            }
        }

        return (string) $thumbnailContent;
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _getTitleByID(string $videoID = '') : string
    {
        $title = _t('Youtube Video');

        $metaData = $this->_getMetaData($videoID);

        if (!isset($metaData['title']) || strlen(trim($metaData['title']))>0) {
            return $title;
        }

        $title = preg_replace('/\s+/su', ' ', $metaData['title']);

        return preg_replace('/(^\s|\s$)/su', '', $title);
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _normalizeURL(string $text = '') : string
    {
        $text = preg_replace(
            '/^(.*?)(https|http)\:\/\/'.
            '(m\.youtube|www\.youtube|youtube)\.com\/watch(\s)(.*?)$/su',
            '$1https://www.youtube.com$4$5',
            $text
        );

        $text = preg_replace(
            '/^(.*?)(https|http)\:\/\/'.
            '(m\.youtube|www\.youtube|youtube)\.com\/watch$/su',
            '$1https://www.youtube.com$4',
            $text
        );

        $text = preg_replace(
            '/^(.*?)(https|http)\:\/\/'.
            '(m\.youtu|www\.youtu|youtu)\.be\/(\s)(.*?)$/su',
            '$1https://www.youtube.com$4$5',
            $text
        );

        $text = preg_replace(
            '/^(.*?)(https|http)\:\/\/'.
            '(m\.youtu|www\.youtu|youtu)\.be\/$/su',
            '$1https://www.youtube.com$4',
            $text
        );

        return $text;
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _parseYoutubeFullURL(string $text = '') : string
    {
        if (!preg_match(static::LINK_REGEXP, $text)) {
            return $text;
        }

        $url = preg_replace(static::LINK_REGEXP, '$2://$3.com/watch$4', $text);

        $videoID = $this->_getVideoIDFromURL($url);

        if (!strlen($videoID) > 0) {
            return str_replace($url, 'https://www.youtube.com', $text);
        }

        return str_replace($url, '[Youtube:'.$videoID.']', $text);
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _parseYoutubeShortURL(string $text = '') : string
    {
        if (!preg_match(static::SHORT_LINK_REGEXP, $text)) {
            return $text;
        }

        $url = preg_replace(static::SHORT_LINK_REGEXP, '$2://$3.be/$4', $text);

        $videoID = $this->_getVideoIDFromURL($url);

        if (!strlen($videoID) > 0) {
            return str_replace($url, 'https://www.youtube.com', $text);
        }

        return str_replace($url, '[Youtube:'.$videoID.']', $text);
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _getURLParams(string $url = '') : array
    {
        $urlParams = trim($url);
        $urlParams = explode('#', $url)[0];
        $urlParams = explode('/', $urlParams);
        $urlParams = end($urlParams);
        $urlParams = str_replace('?', '&', $urlParams);

        return explode('&', $urlParams);
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _getVideoIDFromURL(string $url = '') : string
    {
        $videoID = '';

        $urlParams = $this->_getURLParams($url);

        if (
            preg_match('/^([^\=]+)$/su', $urlParams[0]) &&
            $urlParams[0] != 'watch'
        ) {
            $videoID = $urlParams[0];
        }

        foreach ($urlParams as $urlParam) {
            if (preg_match('/^v\=(.*?)$/su', $urlParam)) {
                $videoID = preg_replace('/^v\=(.*?)$/su', '$1', $urlParam);
            }
        }

        if (!strlen($videoID) > 0) {
            return $videoID;
        }

        return $videoID.$this->_getTimeParamFromURL($url);
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _getTimeParamFromURL(string $url = '') : string
    {
        $urlParams = $this->_getURLParams($url);

        foreach ($urlParams as $urlParam) {
            if (!preg_match('/^t\=(.*?)$/su', $urlParam)) {
                continue;
            }

            $time = preg_replace('/^t\=(.*?)$/su', '$1', $urlParam);

            if (!strlen($time) > 0) {
                return '';
            }

            if (preg_match('/^([0-9]+)$/su', $time)) {
                $time = $time.'s';
            }

            return '?t='.$time;
        }

        return '';
    }
}
?>