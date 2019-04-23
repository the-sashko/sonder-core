<?php
/**
 * Plugin For Getting Youtube Metadata And Generating Embeded Player
 */
class YoutubePlugin
{
    /**
     * @var string Regexp For Full Youtube Link
     */
    const LINK_REGEXP = '/^(.*?)(https|http)\:\/\/'.
                        '(m\.youtube|www\.youtube|youtube)\.com\/'.
                        'watch(.*?)(\s(.*?)$|$)/su';

    /**
     * @var string Regexp For Short Youtube Link
     */
    const SHORT_LINK_REGEXP = '/(.*?)(https|http)\:\/\/'.
                              '(m\.youtu|www\.youtu|youtu)\.be\/'.
                              '(.*?)(\s(.*?)$|$)/su';

    /**
     * @var string Regexp For Youtube Shortcode
     */
    const SHORTCODE_REGEXP = '/^(.*?)\[Youtube:(.*?)\](.*?)$/su';

    /**
     * Replace Youtube URLs By Shortcodes In Text
     *
     * @param string $text Input Plain Text
     *
     * @return string Output Plain Text With Shortodes
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
     * Replace Youtube Shortcodes By HTML Tags In Text
     *
     * @param string $text Input Plain Text
     *
     * @return string Output Text With HTML Tags
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
     * Get Youtube Video Thumbnail By ID
     *
     * @param string $videoID Youtube Video ID
     *
     * @return string Youtube Video Thumbnail File Path
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
     * Get Youtube Video Meta Data By ID
     *
     * @param string $videoID Youtube Video ID
     *
     * @return array Youtube Video Meta Data
     */
    private function _getMetaData(string $videoID = '') : array
    {
        $videoID = $this->_sanitizeVideoID($videoID);

        $cacheFilePath = __DIR__."/cache/_{$videoID}.dat";

        if (!file_exists($cacheFilePath) || !file_exists($cacheFilePath)) {
            $metaDataURL = "https://www.youtube.com/".
                           "get_video_info?video_id={$videoID}";
            $metaData    = file_get_contents ($metaDataURL);
            file_put_contents($cacheFilePath, base64_encode($metaData));
        } else {
            $metaData = file_get_contents($cacheFilePath);
            $metaData = base64_decode($metaData);
        }

        parse_str($metaData, $metaData);

        return $metaData;
    }

    /**
     * Clean Youtube Video ID
     *
     * @param string $videoID Input Youtube Video ID
     *
     * @return string Youtube Output Youtube Video ID
     */
    private function _sanitizeVideoID(string $videoID = '') : string
    {
        $videoID = explode('#', $videoID)[0];
        $videoID = explode('&', $videoID)[0];

        return explode('?', $videoID)[0];
    }

    /**
     * Get List Of Youtube Video Thumbnail URLs 
     *
     * @param string $videoID Youtube Video ID
     *
     * @return string List Of Youtube Video Thumbnail URLs
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
     * Get Youtube Video Thumbnail Data 
     *
     * @param string $videoID Youtube Video ID
     *
     * @return string Youtube Video Thumbnail Data 
     */
    private function _getThumbnailContent(string $videoID = '') : string
    {
        $thumbnailURLs = $this->_getThumbnailURLs($videoID);

        $thumbnailContent = FALSE;

        foreach ($thumbnailURLs as $thumbnailURL) {
            if ($thumbnailContent != false) {
                break;
            }

            try {
                $content = file_get_contents($thumbnailURL);
            } catch(Exception $except) {
                $content = FALSE;
            }
        }

        return (string) $thumbnailContent;
    }

    /**
     * Get Youtube Video Title By Video ID 
     *
     * @param string $videoID Youtube Video ID
     *
     * @return string Youtube Video Title 
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
     * Normalize Youtube Video URLs In Text To Correct Formal 
     *
     * @param string $text Input Plain Text
     *
     * @return string Output Plain Text
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
     * Replce Youtube Video Full URL By Shortcode 
     *
     * @param string $text Input Plain Text
     *
     * @return string Output Text With Shortcodes
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
     * Replce Youtube Video Short URL By Shortcode 
     *
     * @param string $text Input Plain Text
     *
     * @return string Output Text With Shortcodes
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
     * Get List Of URL Params From URL Link
     *
     * @param string $url URL Link
     *
     * @return array List Of URL Params
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
     * Get Youtube Video ID From URL
     *
     * @param string $url Youtube Video URL
     *
     * @return string Youtube Video ID
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
     * Get Youtube Video Time Param From URL
     *
     * @param string $url Youtube URL
     *
     * @return string Youtube Time Param Value
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
