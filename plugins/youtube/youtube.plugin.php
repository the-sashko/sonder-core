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
     * @var array List Of Thumbnail URLs
     */
    const THUMBNAIL_URL_LIST = [
        'https://img.youtube.com/vi/%s/maxresdefault.jpg',
        'https://img.youtube.com/vi/%s/hqdefault.jpg',
        'https://img.youtube.com/vi/%s/mqdefault.jpg',
        'https://img.youtube.com/vi/%s/default.jpg',
        'https://img.youtube.com/vi/%s/sddefault.jpg',
        'https://img.youtube.com/vi/%s/2.jpg',
        'https://img.youtube.com/vi/%s/3.jpg',
        'https://img.youtube.com/vi/%s/1.jpg',
        'https://img.youtube.com/vi/%s/0.jpg'
    ];

    /**
     * @var string Regexp For Youtube Shortcode
     */
    const SHORTCODE_REGEXP = '/^(.*?)\[Youtube:(.*?)\](.*?)$/su';

    /**
     * @var string Youtube Shortcode Template
     */
    const SHORTCODE_TEMPLATE = '[Youtube:%s]';

    /**
     * @var string Path To Cache Directory
     */
    const CACHE_DIR_PATH = __DIR__.'/../../../res/cache/youtube';

    /**
     * @var string Path To Image Directory
     */
    const IMAGE_DIR_PATH = __DIR__.'/../../../res/cache/youtube/img';

    /**
     * @var string Path To Default Image File
     */
    const DEFAULT_IMAGE_DIR_PATH = __DIR__.'/res/image.jpg';

    /**
     * @var string Default Youtube Video Title
     */
    const DEFAULT_TITLE = 'Youtube Video';

    /**
     * @var string|null Default Youtube Video Title (Can Be Defined Out From
     *                  Plugin)
     */
    private $_defaultTitle = null;

    public function __construct()
    {
        if (
            !file_exists(static::CACHE_DIR_PATH) ||
            !is_dir(static::CACHE_DIR_PATH)
        ) {
            mkdir(static::CACHE_DIR_PATH, 0775, true);
        }

        if (
            !file_exists(static::IMAGE_DIR_PATH) ||
            !is_dir(static::IMAGE_DIR_PATH)
        ) {
            mkdir(static::IMAGE_DIR_PATH, 0775, true);
        }
    }

    /**
     * Replace Youtube URLs By Shortcodes In Text
     *
     * @param string|null $text Input Plain Text
     *
     * @return string|null Output Plain Text With Shortodes
     */
    public function parseYoutubeUrls(?string $text = null): ?string
    {
        if (empty($text)) {
            return null;
        }

        $text = $this->_normalizeUrls($text);

        if (
            !preg_match(static::LINK_REGEXP, $text) &&
            !preg_match(static::SHORT_LINK_REGEXP, $text)
        ) {
            return $text;
        }

        $text = $this->_parseYoutubeFullUrls($text);
        $text = $this->_parseYoutubeShortUrls($text);

        return $this->parseYoutubeUrls($text);
    }

    /**
     * Replace Youtube Shortcodes By HTML Tags In Text
     *
     * @param string|null $text Input Plain Text
     *
     * @return string|null Output Text With HTML Tags
     */
    public function parseYoutubeShortCodes(?string $text = null): ?string
    {
        if (!preg_match(static::SHORTCODE_REGEXP, $text)) {
            return $text;
        }

        $code  = preg_replace(static::SHORTCODE_REGEXP, '$2', $text);
        $url   = sprintf('https://www.youtube.com/watch?v=%s', $code);
        $title = $this->_getTitleByCode($code);

        $code = sprintf(static::SHORTCODE_TEMPLATE, $code);

        $link = '<a href="%s" class="youtube_link"><i class="fab '.
                'fa-youtube"></i>&nbsp;%s</a>';

        $link = sprintf($link, $url, $title);

        $text = str_replace($code, $link, $text);

        return $this->parseYoutubeShortCodes($text);
    }

    /**
     * Get Youtube Video Thumbnail By Code
     *
     * @param string|null $code Youtube Video Code
     *
     * @return string|null Youtube Video Thumbnail File Path
     */
    public function getThumbnailByCode(?string $code = null): ?string
    {
        $code = $this->_sanitizeVideoCode($code);

        if (empty($code)) {
            return null;
        }

        $filePath = sprintf('%s/%s.jpg', static::IMAGE_DIR_PATH, $code);

        if (!file_exists($filePath) || !is_file($filePath)) {
            $this->_uploadThumbnail($code);
        }

        return realpath($filePath);
    }

    /**
     * Get Youtube Video Code From URL
     *
     * @param string|null $url               Youtube Video URL
     * @param bool        $isReturnTimeParam Is Return Code With Time Param
     *
     * @return string|null Youtube Video Code
     */
    public function getVideoCodeFromUrl(
        ?string $url               = null,
        bool    $isReturnTimeParam = true
    ): ?string
    {
        $code = null;

        if (!$this->_isValidUrl($url)) {
            return $code;
        }

        $urlParams     = $this->_getUrlParams($url);

        $urlFirstParam = array_values($urlParams);
        $urlFirstParam = array_shift($urlFirstParam);

        if (
            preg_match('/^([^\=]+)$/su', $urlFirstParam) &&
            'watch' !== $urlFirstParam
        ) {
            $code = $urlFirstParam;
        }

        foreach ($urlParams as $urlParam) {
            if (preg_match('/^v\=(.*?)$/su', $urlParam)) {
                $code = preg_replace('/^v\=(.*?)$/su', '$1', $urlParam);
            }
        }

        if (empty($code)) {
            return null;
        }

        if ($isReturnTimeParam) {
            $time = $this->_getTimeFromUrl($url);
            $code = sprintf('%s%s', $code, (string) $time);
        }

        return $code;
    }

    /**
     * Set Default Youtube Video Title
     *
     * @param string|null $defaultTitle Default Youtube Video Title
     */
    public function setDefaultTitle(?string $defaultTitle = null): void
    {
        if (empty($defaultTitle)) {
            $defaultTitle = static::DEFAULT_TITLE;
        }

        $this->_defaultTitle = $defaultTitle;
    }

    /**
     * Check Is Value Valid Youtube Video URL
     *
     * @param string|null $url Youtube Video URL
     *
     * @return bool Is Input Value Valid Youtube Video URL
     */
    private function _isValidUrl(?string $url = null): bool
    {
        if (empty($url)) {
            return false;
        }

        if (preg_match(static::LINK_REGEXP, $url)) {
            return true;
        }

        if (preg_match(static::SHORT_LINK_REGEXP, $url)) {
            return true;
        }

        return false;
    }

    /**
     * Get Youtube Video Meta Data By Code
     *
     * @param string|null $code Youtube Video Code
     *
     * @return array|null Youtube Video Meta Data
     */
    private function _getMetaData(?string $code = null): ?array
    {
        $code = $this->_sanitizeVideoCode($code);

        if (empty($code)) {
            return null;
        }

        $cacheFilePath = sprintf('%s/%s.json', static::CACHE_DIR_PATH, $code);

        /*if (file_exists($cacheFilePath) && is_file($cacheFilePath)) {
            $cacheData = file_get_contents($cacheFilePath);

            return (array) json_decode($cacheData, true);
        }*/

        $metaDataUrl = 'https://www.youtube.com/get_video_info?video_id=%s';
        $metaDataUrl = sprintf($metaDataUrl, $code);
        $metaData    = file_get_contents($metaDataUrl);

        parse_str($metaData, $metaData);

        if (empty($metaData)) {
            return null;
        }

        file_put_contents($cacheFilePath, json_encode($metaData));

        return $metaData;
    }

    /**
     * Clean Youtube Video Code
     *
     * @param string|null $code Input Youtube Video Code
     *
     * @return string|null Youtube Output Youtube Video Code
     */
    private function _sanitizeVideoCode(?string $code = null): ?string
    {
        $codeParts = explode('#', $code);
        $code      = array_shift($codeParts);
        $codeParts = explode('&', $code);
        $code      = array_shift($codeParts);
        $codeParts = explode('?', $code);
        $code      = array_shift($codeParts);

        return $code;
    }

    /**
     * Uploas Youtube Video Thumbnail To Server
     *
     * @param string $code Youtube Video Code
     */
    private function _uploadThumbnail(string $code): void
    {
        $filePath = sprintf('%s/%s.jpg', static::IMAGE_DIR_PATH, $code);
        $urls     = $this->_getThumbnailUrls($code);
        $content  = null;

        if (file_exists($filePath) && is_file($filePath)) {
            unlink($filePath);
        }

        foreach ($urls as $url) {
            try {
                $content = file_get_contents($url);
            } catch (\Exception $exp) {
                continue;
            }

            if (!empty($content)) {
                file_put_contents($filePath, $content);

                break;
            }
        }

        if (empty($content)) {
            file_put_contents(static::DEFAULT_IMAGE_DIR_PATH, $filePath);
        }

        chmod($filePath, 0775);
    }

    /**
     * Get List Of Youtube Video Thumbnail URLs
     *
     * @param string $code Youtube Video Code
     *
     * @return array List Of Youtube Video Thumbnail URLs
     */
    private function _getThumbnailUrls(string $code): array
    {
        $thumbnailUrls = [];

        foreach (static::THUMBNAIL_URL_LIST as $thumbnailUrl) {
            $thumbnailUrls[] = sprintf($thumbnailUrl, $code);
        }

        return $thumbnailUrls;
    }

    /**
     * Get Youtube Video Title By Video Code
     *
     * @param string|null $code Youtube Video Code
     *
     * @return string Youtube Video Title
     */
    private function _getTitleByCode(?string $code = null): string
    {
        $title = $this->_getDefaultTitle();

        if (empty($code)) {
            return $title;
        }

        $metaData = $this->_getMetaData($code);

        if (empty($metaData)) {
            return $title;
        }

        if (
            array_key_exists('title', $metaData) &&
            !empty(trim($metaData['title']))
        ) {
            $title = preg_replace('/\s+/su', ' ', $metaData['title']);
        }

        $title = preg_replace('/\s+/su', ' ', $title);
        $title = preg_replace('/(^\s|\s$)/su', '', $title);

        return $title;
    }

    /**
     * Get Default Youtube Video Title
     *
     * @return string Default Youtube Video Title
     */
    private function _getDefaultTitle(): string
    {
        if (empty($this->_defaultTitle)) {
            return static::DEFAULT_TITLE;
        }

        return $this->_defaultTitle;
    }

    /**
     * Normalize Youtube Video URLs In Text To Correct Formal
     *
     * @param string|null $text Input Plain Text
     *
     * @return string Output Plain Text
     */
    private function _normalizeUrls(?string $text = null): string
    {
        $text = (string) $text;

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
     * @param string|null $text Input Plain Text
     *
     * @return string Output Text With Shortcodes
     */
    private function _parseYoutubeFullUrls(?string $text = null): string
    {
        $text = (string) $text;

        if (!preg_match(static::LINK_REGEXP, $text)) {
            return $text;
        }

        $url = preg_replace(static::LINK_REGEXP, '$2://$3.com/watch$4', $text);

        $code = $this->getVideoCodeFromUrl($url);

        if (empty($code)) {
            return str_replace($url, 'https://www.youtube.com', $text);
        }

        $shortCodeTemplate = sprintf(static::SHORTCODE_TEMPLATE, $code);

        return str_replace($url, $shortCodeTemplate, $text);
    }

    /**
     * Replce Youtube Video Short URL By Shortcode
     *
     * @param string|null $text Input Plain Text
     *
     * @return string Output Text With Shortcodes
     */
    private function _parseYoutubeShortUrls(?string $text = null): string
    {
        $text = (string) $text;

        if (!preg_match(static::SHORT_LINK_REGEXP, $text)) {
            return $text;
        }

        $url = preg_replace(static::SHORT_LINK_REGEXP, '$2://$3.be/$4', $text);

        $code = $this->getVideoCodeFromUrl($url);

        if (empty($code)) {
            return str_replace($url, 'https://www.youtube.com', $text);
        }

        $shortCodeTemplate = sprintf(static::SHORTCODE_TEMPLATE, $code);

        return str_replace($url, $shortCodeTemplate, $text);
    }

    /**
     * Get List Of URL Params From URL Link
     *
     * @param string|null $url URL Link
     *
     * @return array List Of URL Params
     */
    private function _getUrlParams(?string $url = null): array
    {
        $url = (string) $url;
        $url = explode('#', trim($url));

        $urlParams = array_shift($url);
        $urlParams = explode('/', $urlParams);
        $urlParams = end($urlParams);
        $urlParams = str_replace('?', '&', $urlParams);

        return explode('&', $urlParams);
    }

    /**
     * Get Youtube Video Time Param From URL
     *
     * @param string|null $url Youtube URL
     *
     * @return string|null Youtube Time Param Value
     */
    private function _getTimeFromUrl(?string $url = null): ?string
    {
        if (empty($url)) {
            return null;
        }

        $urlParams = $this->_getUrlParams($url);

        if (empty($urlParams)) {
            return null;
        }

        foreach ($urlParams as $urlParam) {
            if (!preg_match('/^t\=(.*?)$/su', $urlParam)) {
                continue;
            }

            $time = preg_replace('/^t\=(.*?)$/su', '$1', $urlParam);

            if (empty($time)) {
                return null;
            }

            if (preg_match('/^([0-9]+)$/su', $time)) {
                $time = sprintf('%ss', $time);
            }

            return sprintf('?t=%s', $time);
        }

        return null;
    }
}
