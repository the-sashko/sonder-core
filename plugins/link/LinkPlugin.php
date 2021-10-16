<?php

namespace Sonder\Plugins;

use Sonder\Plugins\Link\Classes\Parser;

final class LinkPlugin
{
    const SHORTCODE_REGEXP = '/\[Link\:(.*?)\:\"(.*?)\"\]/su';

    const CACHE_DIR_PATH = __DIR__ . '/../../../res/cache/link';

    /**
     * @var Parser
     */
    private Parser $_parser;

    final public function __construct()
    {
        $this->_parser = new Parser();
    }

    /**
     * @param string|null $text
     *
     * @return string|null
     */
    final public function parseLinkUrls(?string $text = null): ?string
    {
        if (empty($text)) {
            return null;
        }

        return preg_replace_callback(
            '/(https|http):\/\/(.*?)(\s|$)/su',
            [
                $this,
                '_getLinkShortCode'
            ],
            $text
        );
    }

    /**
     * @param string|null $text
     * @param bool $displayIcons
     *
     * @return string|null
     */
    final public function parseLinkShortCodes(
        ?string $text = null,
        bool    $displayIcons = false
    ): ?string
    {
        if (empty($text)) {
            return null;
        }

        $icon = '';

        if ($displayIcons) {
            $icon = '<i class="fas fa-link"></i>&nbsp;';
        }

        return preg_replace(
            LinkPlugin::SHORTCODE_REGEXP,
            '<a href="$1"' .
            ' target="_blank" rel="nofollow" class="external_link">' .
            $icon . '$2' .
            '</a>',
            $text
        );
    }

    /**
     * @param string|null $url
     *
     * @return array|null
     */
    private function _getWebPageMetaData(?string $url = null): ?array
    {
        if (empty($url)) {
            return null;
        }

        $cacheDirPath = LinkPlugin::CACHE_DIR_PATH;

        if (defined('APP_PROTECTED_DIR_PATH')) {
            $cacheDirPath = APP_PROTECTED_DIR_PATH;
            $cacheDirPath = sprintf('%/cache/link', $cacheDirPath);
        }

        $cacheFilePath = sprintf(
            '%s/%s_%s.json',
            $cacheDirPath,
            hash('sha256', $url),
            hash('md5', $url)
        );

        if (file_exists($cacheFilePath) && is_file($cacheFilePath)) {
            return $this->_getWebPageMetaDataFromCache($cacheFilePath);
        }

        $html = $this->_parser->getPageContent($url);

        $metaData = [
            'url' => $url,
            'title' => $this->_parser->getPageTitle($html, $url),
            'description' => $this->_parser->getPageDescription($html),
            'image' => $this->_parser->getPageImage($html, $url)
        ];

        $this->_saveWebPageMetaDataToCache($metaData, $cacheFilePath);

        return $metaData;
    }

    /**
     * @param array $metaData
     *
     * @param string $cacheFilePath
     */
    private function _saveWebPageMetaDataToCache(
        array  $metaData,
        string $cacheFilePath
    ): void
    {
        file_put_contents($cacheFilePath, json_encode($metaData));
    }

    /**
     * @param string|null $fileCache
     *
     * @return array
     */
    private function _getWebPageMetaDataFromCache(
        ?string $fileCache = null
    ): array
    {
        $metaDataJson = file_get_contents($fileCache);
        $metaData = (array)json_decode($metaDataJson, true);

        $metaData['url'] = null;

        if (array_key_exists('url', $metaData)) {
            $metaData['url'] = base64_decode((string)$metaData['url']);
        }

        if (!array_key_exists('title', $metaData)) {
            $metaData['title'] = '&nbsp;';
        }

        if (!array_key_exists('description', $metaData)) {
            $metaData['description'] = '&nbsp;';
        }

        if (!array_key_exists('image', $metaData)) {
            $metaData['image'] = '/assets/img/website.png';
        }

        return $metaData;
    }

    /**
     * @param array|null $urlParts
     *
     * @return string|null
     */
    private function _getLinkShortCode(?array $urlParts): ?string
    {
        $shortCode = null;
        $url = null;
        $metaData = null;

        if (!empty($urlParts)) {
            $urlParts = array_shift($urlParts);
        }

        if (!empty($url)) {
            $url = trim($url);

            $url = preg_replace(
                '/([^0-9a-z\/_=\-]+)$/su',
                '',
                $url
            );

            $metaData = $this->_getWebPageMetaData($url);
        }

        if (
            !empty($url) &&
            !empty($metaData) &&
            array_key_exists('title', $metaData)
        ) {
            $shortCode = sprintf(
                ' [Link:%s:"%s"] ',
                $url,
                $metaData['title']
            );
        }

        return $shortCode;
    }
}
