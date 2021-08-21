<?php

use Core\Plugins\Link\Classes\Parser;

/**
 * Plugin For Getting URL Links Metadata And Generating HTML
 */
class LinkPlugin
{
    /**
     * @var string Regexp For Link Shortcode
     */
    const SHORTCODE_REGEXP = '/\[Link\:(.*?)\:\"(.*?)\"\]/su';

    /**
     * @var string Path To Cache Directory
     */
    const CACHE_DIR_PATH = __DIR__ . '/../../../res/cache/link';

    /**
     * @var Parser|null Parser Instance
     */
    private $_parser = null;

    public function __construct()
    {
        $this->_parser = new Parser();
    }

    /**
     * Replace URL Links In Text By Shortcodes
     *
     * @param string|null $text Input Plain Text Value
     *
     * @return string|null Output Text Value With Shortcodes
     */
    public function parseLinkUrls(?string $text = null): ?string
    {
        if (empty($text)) {
            return null;
        }

        $text = preg_replace_callback(
            '/(https|http)\:\/\/(.*?)(\s|$)/su',
            [
                $this,
                '_getLinkShortCode'
            ],
            $text
        );

        return $text;
    }

    /**
     * Replace URL Links Shortcodes In Text By HTML Tags
     *
     * @param string|null $text
     * @param bool $displayIcons
     *
     * @return string|null
     */
    public function parseLinkShortCodes(
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
            static::SHORTCODE_REGEXP,
            '<a href="$1"' .
            'target="_blank" rel="nofollow" class="external_link">' .
            $icon . '$2' .
            '</a>',
            $text
        );
    }

    /**
     * Get Web Page Meta Data
     *
     * @param string|null $url Web Page URL
     *
     * @return array|null Meta Data Of Web Page
     */
    private function _getWebPageMetaData(?string $url = null): ?array
    {
        if (empty($url)) {
            return null;
        }

        $cacheFilePath = sprintf(
            '%s/%s_%s.json',
            static::CACHE_DIR_PATH,
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
     * Saving Web Page Meta Data To Cache
     *
     * @param array $metaData Meta Data Of Web Page
     * @param string $cacheFilePath Cache File Path
     */
    private function _saveWebPageMetaDataToCache(
        array  $metaData,
        string $cacheFilePath
    ): void
    {
        file_put_contents($cacheFilePath, json_encode($metaData));
    }

    /**
     * Get Web Page Meta Data From Cache
     *
     * @param string|null $fileCache Web Page Cache File Path
     *
     * @return array Web Page Web Page Meta Data
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
     * Get Web Page Link Shortcode From Rergexp URL Parts
     *
     * @param array|null $urlParts Rergexp URL Parts
     *
     * @return string|null Web Page Link Shortcode
     */
    private function _getLinkShortCode(?array $urlParts = null): ?string
    {
        $shortCode = null;
        $url = null;
        $metaData = null;

        if (!empty($urlParts)) {
            $urlParts = array_shift($urlParts);
        }

        if (!empty($url)) {
            $url = trim($url);
            $url = preg_replace('/([^0-9a-z\/_=\-]+)$/su', '', $url);

            $metaData = $this->_getWebPageMetaData($url);
        }

        if (
            !empty($url) &&
            !empty($metaData) &&
            array_key_exists('title', $metaData)
        ) {
            $shortCode = sprintf(' [Link:%s:"%s"] ', $url, $metaData['title']);
        }

        return $shortCode;
    }
}
