<?php

namespace Sonder\Plugins;

use Throwable;

final class YoutubePlugin
{
    const LINK_REGEXP = '/^(.*?)(https|http)\:\/\/' .
    '(m\.youtube|www\.youtube|youtube)\.com\/' .
    'watch(.*?)(\s(.*?)$|$)/su';

    const SHORT_LINK_REGEXP = '/(.*?)(https|http)\:\/\/' .
    '(m\.youtu|www\.youtu|youtu)\.be\/' .
    '(.*?)(\s(.*?)$|$)/su';

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

    const SHORTCODE_REGEXP = '/^(.*?)\[Youtube:(.*?)\](.*?)$/su';

    const SHORTCODE_TEMPLATE = '[Youtube:%s]';

    const CACHE_DIR_PATH = __DIR__ . '/../../../cache/youtube';

    const DEFAULT_IMAGE_DIR_PATH = __DIR__ . '/res/image.jpg';

    const DEFAULT_TITLE = 'Youtube Video';

    private ?string $_defaultTitle = null;

    final public function __construct()
    {
        $cacheDirPath = $this->_getCacheDirPath();

        if (!file_exists($cacheDirPath) || !is_dir($cacheDirPath)) {
            mkdir($cacheDirPath, 0775, true);
        }

        $imageCacheDirPath = sprintf('%s/img', $cacheDirPath);

        if (!file_exists($imageCacheDirPath) || !is_dir($imageCacheDirPath)) {
            mkdir($imageCacheDirPath, 0775, true);
        }
    }

    /**
     * @param string|null $text
     *
     * @return string|null
     */
    final public function parseYoutubeUrls(?string $text = null): ?string
    {
        if (empty($text)) {
            return null;
        }

        $text = $this->_normalizeUrls($text);

        if (
            !preg_match(YoutubePlugin::LINK_REGEXP, $text) &&
            !preg_match(YoutubePlugin::SHORT_LINK_REGEXP, $text)
        ) {
            return $text;
        }

        $text = $this->_parseYoutubeFullUrls($text);
        $text = $this->_parseYoutubeShortUrls($text);

        return $this->parseYoutubeUrls($text);
    }

    /**
     * @param string|null $text
     *
     * @return string|null
     */
    final public function parseYoutubeShortCodes(?string $text = null): ?string
    {
        if (!preg_match(YoutubePlugin::SHORTCODE_REGEXP, $text)) {
            return $text;
        }

        $code = preg_replace(YoutubePlugin::SHORTCODE_REGEXP, '$2', $text);
        $url = sprintf('https://www.youtube.com/watch?v=%s', $code);
        $title = $this->_getTitleByCode($code);

        $code = sprintf(YoutubePlugin::SHORTCODE_TEMPLATE, $code);

        $link = '<a href="%s" class="youtube_link"><i class="fab ' .
            'fa-youtube"></i>&nbsp;%s</a>';

        $link = sprintf($link, $url, $title);

        $text = str_replace($code, $link, $text);

        return $this->parseYoutubeShortCodes($text);
    }

    /**
     * @param string|null $code
     *
     * @return string|null
     */
    final public function getThumbnailByCode(?string $code = null): ?string
    {
        $code = $this->_sanitizeVideoCode($code);

        if (empty($code)) {
            return null;
        }

        $imageCacheDirPath = sprintf('%s/img', $this->_getCacheDirPath());

        $filePath = sprintf('%s/%s.jpg', $imageCacheDirPath, $code);

        if (!file_exists($filePath) || !is_file($filePath)) {
            $this->_uploadThumbnail($code);
        }

        return realpath($filePath);
    }

    /**
     * @param string|null $url
     * @param bool $isReturnTimeParam
     *
     * @return string|null
     */
    final public function getVideoCodeFromUrl(
        ?string $url = null,
        bool    $isReturnTimeParam = true
    ): ?string
    {
        $code = null;

        if (!$this->_isValidUrl($url)) {
            return null;
        }

        $urlParams = $this->_getUrlParams($url);

        $urlFirstParam = array_values($urlParams);
        $urlFirstParam = array_shift($urlFirstParam);

        if (
            preg_match('/^([^=]+)$/su', $urlFirstParam) &&
            'watch' !== $urlFirstParam
        ) {
            $code = $urlFirstParam;
        }

        foreach ($urlParams as $urlParam) {
            if (preg_match('/^v=(.*?)$/su', $urlParam)) {
                $code = preg_replace('/^v=(.*?)$/su', '$1', $urlParam);
            }
        }

        if (empty($code)) {
            return null;
        }

        if ($isReturnTimeParam) {
            $time = $this->_getTimeFromUrl($url);
            $code = sprintf('%s%s', $code, (string)$time);
        }

        return $code;
    }

    /**
     * @param string|null $defaultTitle
     */
    final public function setDefaultTitle(?string $defaultTitle = null): void
    {
        if (empty($defaultTitle)) {
            $defaultTitle = YoutubePlugin::DEFAULT_TITLE;
        }

        $this->_defaultTitle = $defaultTitle;
    }

    /**
     * @param string|null $url
     *
     * @return bool
     */
    private function _isValidUrl(?string $url = null): bool
    {
        if (empty($url)) {
            return false;
        }

        if (preg_match(YoutubePlugin::LINK_REGEXP, $url)) {
            return true;
        }

        if (preg_match(YoutubePlugin::SHORT_LINK_REGEXP, $url)) {
            return true;
        }

        return false;
    }

    /**
     * @param string|null $code
     *
     * @return array|null
     */
    private function _getMetaData(?string $code = null): ?array
    {
        $code = $this->_sanitizeVideoCode($code);

        if (empty($code)) {
            return null;
        }

        $cacheDirPath = $this->_getCacheDirPath();
        $cacheFilePath = sprintf('%s/%s.json', $cacheDirPath, $code);

        $metaDataUrl = 'https://www.youtube.com/get_video_info?video_id=%s';
        $metaDataUrl = sprintf($metaDataUrl, $code);
        $metaData = file_get_contents($metaDataUrl);

        parse_str($metaData, $metaData);

        if (empty($metaData)) {
            return null;
        }

        file_put_contents($cacheFilePath, json_encode($metaData));

        return $metaData;
    }

    /**
     * @param string|null $code
     *
     * @return string|null
     */
    private function _sanitizeVideoCode(?string $code = null): ?string
    {
        $codeParts = explode('#', $code);
        $code = array_shift($codeParts);
        $codeParts = explode('&', $code);
        $code = array_shift($codeParts);
        $codeParts = explode('?', $code);

        return array_shift($codeParts);
    }

    /**
     * @param string $code
     */
    private function _uploadThumbnail(string $code): void
    {
        $imageCacheDirPath = sprintf('%s/img', $this->_getCacheDirPath());

        $filePath = sprintf('%s/%s.jpg', $imageCacheDirPath, $code);
        $urls = $this->_getThumbnailUrls($code);
        $content = null;

        if (file_exists($filePath) && is_file($filePath)) {
            unlink($filePath);
        }

        foreach ($urls as $url) {
            try {
                $content = file_get_contents($url);
            } catch (Throwable $thr) {
                continue;
            }

            if (!empty($content)) {
                file_put_contents($filePath, $content);

                break;
            }
        }

        if (empty($content)) {
            file_put_contents(YoutubePlugin::DEFAULT_IMAGE_DIR_PATH, $filePath);
        }

        chmod($filePath, 0775);
    }

    /**
     * @param string $code
     *
     * @return array
     */
    private function _getThumbnailUrls(string $code): array
    {
        $thumbnailUrls = [];

        foreach (YoutubePlugin::THUMBNAIL_URL_LIST as $thumbnailUrl) {
            $thumbnailUrls[] = sprintf($thumbnailUrl, $code);
        }

        return $thumbnailUrls;
    }

    /**
     * @param string|null $code
     *
     * @return string
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

        return preg_replace('/(^\s|\s$)/su', '', $title);
    }

    /**
     * @return string
     */
    private function _getDefaultTitle(): string
    {
        if (empty($this->_defaultTitle)) {
            return YoutubePlugin::DEFAULT_TITLE;
        }

        return $this->_defaultTitle;
    }

    /**
     * @param string|null $text
     *
     * @return string
     */
    private function _normalizeUrls(?string $text = null): string
    {
        $text = (string)$text;

        $text = preg_replace(
            '/^(.*?)(https|http):\/\/' .
            '(m\.youtube|www\.youtube|youtube)\.com\/watch(\s)(.*?)$/su',
            '$1https://www.youtube.com$4$5',
            $text
        );

        $text = preg_replace(
            '/^(.*?)(https|http):\/\/' .
            '(m\.youtube|www\.youtube|youtube)\.com\/watch$/su',
            '$1https://www.youtube.com$4',
            $text
        );

        $text = preg_replace(
            '/^(.*?)(https|http):\/\/' .
            '(m\.youtu|www\.youtu|youtu)\.be\/(\s)(.*?)$/su',
            '$1https://www.youtube.com$4$5',
            $text
        );

        return preg_replace(
            '/^(.*?)(https|http):\/\/' .
            '(m\.youtu|www\.youtu|youtu)\.be\/$/su',
            '$1https://www.youtube.com$4',
            $text
        );
    }

    /**
     * @param string|null $text
     *
     * @return string
     */
    private function _parseYoutubeFullUrls(?string $text = null): string
    {
        $text = (string)$text;

        if (!preg_match(YoutubePlugin::LINK_REGEXP, $text)) {
            return $text;
        }

        $url = preg_replace(YoutubePlugin::LINK_REGEXP, '$2://$3.com/watch$4', $text);

        $code = $this->getVideoCodeFromUrl($url);

        if (empty($code)) {
            return str_replace($url, 'https://www.youtube.com', $text);
        }

        $shortCodeTemplate = sprintf(YoutubePlugin::SHORTCODE_TEMPLATE, $code);

        return str_replace($url, $shortCodeTemplate, $text);
    }

    /**
     * @param string|null $text
     *
     * @return string
     */
    private function _parseYoutubeShortUrls(?string $text = null): string
    {
        $text = (string)$text;

        if (!preg_match(YoutubePlugin::SHORT_LINK_REGEXP, $text)) {
            return $text;
        }

        $url = preg_replace(YoutubePlugin::SHORT_LINK_REGEXP, '$2://$3.be/$4', $text);

        $code = $this->getVideoCodeFromUrl($url);

        if (empty($code)) {
            return str_replace($url, 'https://www.youtube.com', $text);
        }

        $shortCodeTemplate = sprintf(YoutubePlugin::SHORTCODE_TEMPLATE, $code);

        return str_replace($url, $shortCodeTemplate, $text);
    }

    /**
     * @param string|null $url
     *
     * @return array
     */
    private function _getUrlParams(?string $url = null): array
    {
        $url = (string)$url;
        $url = explode('#', trim($url));

        $urlParams = array_shift($url);
        $urlParams = explode('/', $urlParams);
        $urlParams = end($urlParams);
        $urlParams = str_replace('?', '&', $urlParams);

        return explode('&', $urlParams);
    }

    /**
     * @param string|null $url
     *
     * @return string|null
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
            if (!preg_match('/^t=(.*?)$/su', $urlParam)) {
                continue;
            }

            $time = preg_replace('/^t=(.*?)$/su', '$1', $urlParam);

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

    /**
     * @return string
     */
    private function _getCacheDirPath(): string
    {
        if (!defined('APP_PROTECTED_DIR_PATH')) {
            return YoutubePlugin::CACHE_DIR_PATH;
        }

        return sprintf(
            '%s/cache/youtube',
            APP_PROTECTED_DIR_PATH
        );
    }
}
