<?php
/**
 * Plugin For Getting URL Links Metadata And Generating HTML
 */
class LinkPlugin
{
    /**
     * @var string Regexp For Open Gaph Title Tag
     */
    const META_TAG_OG_TITLE_REGEX = '/^(.*?)\<meta([\s]+)'.
                                    'property=(\"|\')og\:title(\"|\')([\s]+)'.
                                    'content=(\"|\')(.*?)(\"|\')(.*?)$/su';

    /**
     * @var string Alternative Regexp For Open Gaph Title Tag
     */
    const META_TAG_OG_TITLE_REGEX_ALT = '/^(.*?)\<meta([\s]+)'.
                                        'content=(\"|\')(.*?)\"([\s]+)'.
                                        'property=(\"|\')'.
                                        'og\:title(\"|\')(.*?)$/su';

    /**
     * @var string Regexp For Twitter Title Tag
     */
    const META_TAG_TWITTER_TITLE_REGEX = '/^(.*?)\<meta([\s]+)'.
                                         'name=(\"|\')twitter\:title'.
                                         '(\"|\')([\s]+)content='.
                                         '(\"|\')(.*?)(\"|\')(.*?)$/su';

    /**
     * @var string Alternative Regexp For Twitter Title Tag
     */
    const META_TAG_TWITTER_TITLE_REGEX_ALT = '/^(.*?)\<meta([\s]+)content='.
                                             '(\"|\')(.*?)\"([\s]+)'.
                                             'name=(\"|\')twitter\:title'.
                                             '(\"|\')(.*?)$/su';

    /**
     * @var string Regexp For Meta Tag Title
     */
    const META_TITLE_REGEX = '/^(.*?)\<title([\s]+|)\>'.
                             '(.*?)\<\/title\>(.*?)$/su';

    /**
     * @var string Regexp For H1 Tag
     */
    const H1_TITLE_REGEX = '/^(.*?)\<h1(.*?)\>(.*?)\<\/h1\>(.*?)$/su';

    /**
     * @var string Alternative Regexp For H1 Tag
     */
    const H1_TITLE_REGEX_ALT = '/^(.*?)\<h1(.*?)\>(.*?)\<\/h1\>(.*?)$/su';

    /**
     * @var string Alternative Regexp For Main Tag
     */
    const MAIN_TITLE_REGEX = '/^(.*?)\<main(.*?)\>(.*?)\<\/main\>(.*?)$/su';

    /**
     * @var string Alternative Regexp For Body Tag
     */
    const BODY_TITLE_REGEX = '/^(.*?)\<body(.*?)\>(.*?)\<\/body\>(.*?)$/su';

    /**
     * @var string Regexp For Open Gaph Description Tag
     */
    const META_TAG_OG_DESCRIPTION_REGEX = '/^(.*?)\<meta([\s]+)property='.
                                          '(\"|\')og\:description'.
                                          '(\"|\')([\s]+)content='.
                                          '(\"|\')(.*?)(\"|\')(.*?)$/su';

    /**
     * @var string Alternative Regexp For Open Gaph Description Tag
     */
    const META_TAG_OG_DESCRIPTION_REGEX_ALT = '/^(.*?)\<meta([\s]+)content='.
                                              '(\"|\')(.*?)\"([\s]+)property='.
                                              '(\"|\')og\:description'.
                                              '(\"|\')(.*?)$/su';

    /**
     * @var string Regexp For Twitter Description Tag
     */
    const META_TAG_TWITTER_DESCRIPTION_REGEX = '/^(.*?)\<meta([\s]+)name='.
                                               '(\"|\')twitter\:description'.
                                               '(\"|\')([\s]+)content='.
                                               '(\"|\')(.*?)(\"|\')(.*?)$/su';

    /**
     * @var string Alternative Regexp For Twitter Description Tag
     */
    const META_TAG_TWITTER_DESCRIPTION_REGEX_ALT = '/^(.*?)\<meta([\s]+)'.
                                                   'content=(\"|\')'.
                                                   '(.*?)\"([\s]+)name='.
                                                   '(\"|\')twitter\:'.
                                                   'description(\"|\')'.
                                                   '(.*?)$/su';

    /**
     * @var string Regexp For Meta Tag Description
     */
    const META_DESCRIPTION_REGEX = '/^(.*?)\<meta([\s]+)'.
                                   'name=(\"|\')description(\"|\')'.
                                   '([\s]+)content=(\"|\')(.*?)'.
                                   '(\"|\')(.*?)$/su';

    /**
     * @var string Alternative Regexp For Meta Tag Description
     */
    const META_DESCRIPTION_REGEX_ALT = '/^(.*?)\<meta([\s]+)'.
                                       'content=(\"|\')(.*?)\"([\s]+)'.
                                       'name=(\"|\')description(\"|\')'.
                                       '(.*?)$/su';

    /**
     * @var string Regexp For Article Tag
     */
    const ARTICLE_DESCRIPTION_REGEX = '/^(.*?)\<article(.*?)\>(.*?)'.
                                      '\<\/article\>(.*?)$/su';

    /**
     * @var string Regexp For Main Tag
     */
    const MAIN_DESCRIPTION_REGEX = '/^(.*?)\<main(.*?)\>(.*?)\<\/main\>'.
                                   '(.*?)$/su';

    /**
     * @var string Regexp For P Tag
     */
    const P_DESCRIPTION_REGEX = '/^(.*?)\<p(.*?)\>(.*?)\<\/p\>(.*?)$/su';

    /**
     * @var string Regexp For Body Tag
     */
    const BODY_DESCRIPTION_REGEX = '/^(.*?)\<body(.*?)\>(.*?)\<\/body\>'.
                                   '(.*?)$/su';

    /**
     * @var string Regexp For Open Gaph Image Tag
     */
    const META_TAG_OG_IMAGE_REGEX = '/^(.*?)\<meta([\s]+)property=(\"|\')'.
                                    'og\:image(\"|\')([\s]+)'.
                                    'content=(\"|\')(.*?)(\"|\')(.*?)$/su';

    /**
     * @var string Alternative Regexp For Open Gaph Image Tag
     */
    const META_TAG_OG_IMAGE_REGEX_ALT = '/^(.*?)\<meta([\s]+)content='.
                                        '(\"|\')(.*?)\"([\s]+)property='.
                                        '(\"|\')og\:image(\"|\')(.*?)$/su';

    /**
     * @var string Regexp For Twitter Image Tag
     */
    const META_TAG_TWITTER_IMAGE_REGEX = '/^(.*?)\<meta([\s]+)name=(\"|\')'.
                                         'twitter\:image(\"|\')([\s]+)'.
                                         'content=(\"|\')(.*?)(\"|\')'.
                                         '(.*?)$/su';
    /**
     * @var string Alternative Regexp For Twitter Image Tag
     */
    const META_TAG_TWITTER_IMAGE_REGEX_ALT = '/^(.*?)\<meta([\s]+)content='.
                                             '(\"|\')(.*?)\"([\s]+)name='.
                                             '(\"|\')twitter\:image(\"|\')'.
                                             '(.*?)$/su';

    /**
     * @var string Regexp For Image Link Tag
     */
    const LINK_IMAGE_REGEX = '/^(.*?)\<link([\s]+)rel=(\"|\')'.
                             'image_src(\"|\')([\s]+)href=(\"|\')'.
                             '(.*?)(\"|\')(.*?)$/su';

    /**
     * @var string Alternative Regexp For Image Link Tag
     */
    const LINK_IMAGE_REGEX_ALT = '/^(.*?)\<link([\s]+)href=(\"|\')'.
                                 '(.*?)\"([\s]+)rel=(\"|\')'.
                                 'image_src(\"|\')(.*?)$/su';

    /**
     * @var string Alternative Regexp For Image Tag
     */
    const IMG_IMAGE_REGEX_ALT = '/^(.*?)\<img(.*?)src=(\"|\')(.*?)(\"|\')'.
                                '(.*?)\>(.*?)$/su';

    /**
     * @var string Regexp For Link Shortcode
     */
    const SHORTCODE_REGEXP = '/\[Link\:(.*?)\:\"(.*?)\"\]/su';

    /**
     * @var string Path To Cache Directory
     */
    const CACHE_DIR_PATH = __DIR__.'/../../../res/cache/link';

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
     * @param string|null $text Input Plain Text Value With Shortcodes
     *
     * @return string|null Output Text Value With HTML Tags
     */
    public function parseLinkShortCodes(?string $text = null): ?string
    {
        if (empty($text)) {
            return null;
        }

        return preg_replace(
            static::SHORTCODE_REGEXP,
            '<a href="$1"'.
            'target="_blank" rel="nofollow" class="external_link">'.
            '<i class="fas fa-link"></i>&nbsp;$2'.
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

        $html        = $this->_getPageContent($url);
        $title       = $this->_getPageTitle($html, $url);
        $description = $this->_getPageDescription($html);
        $image       = $this->_getPageImage($html, $url);

        $metaData = [
            'url'         => $url,
            'title'       => $title,
            'description' => $description,
            'image'       => $image
        ];

        $this->_saveWebPageMetaDataToCache($metaData, $cacheFilePath);

        return $metaData;
    }

    /**
     * Saving Web Page Meta Data To Cache
     *
     * @param array  $metaData      Meta Data Of Web Page
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
     * Get Web Page Main Image Link
     *
     * @param string|null $html Web Page HTML
     * @param string|null $url Web Page URL
     *
     * @return string|null Web Page Main Image
     */
    private function _getPageImage(
        ?string $html = null,
        ?string $url  = null
    ): ?string
    {
        if (empty($html)) {
            return null;
        }

        if (empty($url)) {
            return null;
        }

        $image = $this->_getPageImageFromMetaTags($html);

        if (empty($image) || empty($image)) {
            //$image = _getPageImageFromBody($html);
        }

        return $this->_normalizeImage($image, $url);
    }

    /**
     * Get Web Page Main Image Link From Meta Tags
     *
     * @param string|null $html Web Page HTML
     *
     * @return string|null Web Page Main Image Link
     */
    private function _getPageImageFromMetaTags(?string $html = null): ?string
    {
        if ($this->_isTagExists('META_TAG_OG_IMAGE_REGEX', $html)) {
            return $this->_parseHtmlTag('META_TAG_OG_IMAGE_REGEX', 7, $html);
        }

        if ($this->_isTagExists('META_TAG_OG_IMAGE_REGEX_ALT', $html)) {
            return $this->_parseHtmlTag(
                'META_TAG_OG_IMAGE_REGEX_ALT',
                4,
                $html
            );
        }

        if ($this->_isTagExists('META_TAG_TWITTER_IMAGE_REGEX', $html)) {
            return $this->_parseHtmlTag(
                'META_TAG_TWITTER_IMAGE_REGEX',
                7,
                $html
            );
        }

        if ($this->_isTagExists('META_TAG_TWITTER_IMAGE_REGEX_ALT', $html)) {
            return $this->_parseHtmlTag(
                'META_TAG_TWITTER_IMAGE_REGEX_ALT',
                4,
                $html
            );
        }

        if ($this->_isTagExists('LINK_IMAGE_REGEX', $html)) {
            return $this->_parseHtmlTag('LINK_IMAGE_REGEX', 7, $html);
        }

        if ($this->_isTagExists('LINK_IMAGE_REGEX_ALT', $html)) {
            return $this->_parseHtmlTag('LINK_IMAGE_REGEX_ALT', 4, $html);
        }

        return null;
    }

    /**
     * Get Web Page Main Image Link From HTML Body
     *
     * @param string|null $html Web Page HTML
     *
     * @return string|null Web Page Main Image Link
     */
    private function _getPageImageFromBody(?string $html = null): ?string
    {
        if ($this->_isTagExists('IMG_IMAGE_REGEX_ALT', $html)) {
            return $this->_parseHtmlTag('IMG_IMAGE_REGEX_ALT', 4, $html);
        }

        return null;
    }

    /**
     * Check Image Link Format
     *
     * @param string|null $image Image Link
     *
     * @return bool Is Image Link Has Valid Format
     */
    private function _isImageValid(?string $image = null): bool
    {
        if (empty($image)) {
            return false;
        }

        if (preg_match(
            '/^(.*?)\.((jpg)|(jpeg)|(bmp)|(gif)|(png))$/su',
            $image
        )) {
            return true;
        }

        return false;
    }

    /**
     * Change Web Page Main Image Link To Valid Format
     *
     * @param string|null $image Web Page Main Image Link
     * @param string|null $url   Web Page URL
     *
     * @return string Nomalized Web Page Main Image Link
     */
    private function _normalizeImage(
        ?string $image = null,
        ?string $url   = null
    ): string
    {
        $url   = (string) $url;
        $image = (string) $image;

        $image = trim($image);

        if (!$this->_isImageValid($image)) {
            return '/assets/img/website.png';
        }

        if (preg_match('/^\/\/(.*?)$/su', $image)) {
            $protocol = $this->_getProtocol($url);

            return sprintf('%s:%s', $protocol, $image);
        }

        if (preg_match('/^\/(.*?)$/su', $image)) {
            $protocol = $this->_getProtocol($url);
            $domain   = $this->_getDomain($url);

            return sprintf('%s://%s/%s', $protocol, $domain, $image);
        }

        if (!preg_match('/^http(s|)\:\/\/(.*?)$/su', $image)) {
            $urlChunks = explode('#', $url);
            $url       = array_shift($urlChunks);

            $urlChunks = explode('&', $url);
            $url       = array_shift($urlChunks);

            $urlChunks = explode('?', $url);
            $url       = array_shift($urlChunks);

            $url = preg_replace('/^(.*?)\/$/su', '$1', $url);

            return sprintf('%s/%s', $url, $image);
        }

        return $image;
    }

    /**
     * Get Web Page Description From HTML
     *
     * @param string|null $html Web Page HTML
     *
     * @return string Web Page Description
     */
    private function _getPageDescription(?string $html = null): string
    {
        $description = $this->_getPageDescriptionFromMetaTags($html);

        if (empty($description)) {
            $description = $this->_getPageDescriptionFromBody($html);
        }

        return $this->_normalizeDescription($description);
    }

    /**
     * Get Web Page Description From HTML Body
     *
     * @param string|null $html Web Page HTML
     *
     * @return string|null Web Page Description
     */
    private function _getPageDescriptionFromBody(?string $html = null): ?string
    {
        if ($this->_isTagExists('ARTICLE_DESCRIPTION_REGEX', $html)) {
            return $this->_parseHtmlTag('ARTICLE_DESCRIPTION_REGEX', 3, $html);
        }

        if ($this->_isTagExists('MAIN_DESCRIPTION_REGEX', $html)) {
            return $this->_parseHtmlTag('MAIN_DESCRIPTION_REGEX', 3, $html);
        }

        if ($this->_isTagExists('P_DESCRIPTION_REGEX', $html)) {
            return $this->_parseHtmlTag('P_DESCRIPTION_REGEX', 3, $html);
        }

        if ($this->_isTagExists('BODY_DESCRIPTION_REGEX', $html)) {
            return $this->_parseHtmlTag('BODY_DESCRIPTION_REGEX', 3, $html);
        }

        return $html;
    }

    /**
     * Get Web Page Description From HTML Body
     *
     * @param string|null $html Web Page HTML
     *
     * @return string|null Web Page Description
     */
    private function _getPageDescriptionFromMetaTags(
        ?string $html = null
    ): ?string
    {
        if ($this->_isTagExists('META_TAG_OG_DESCRIPTION_REGEX', $html)) {
            return $this->_parseHtmlTag(
                'META_TAG_OG_DESCRIPTION_REGEX',
                7,
                $html
            );
        }

        if ($this->_isTagExists('META_TAG_OG_DESCRIPTION_REGEX_ALT', $html)) {
            return $this->_parseHtmlTag(
                'META_TAG_OG_DESCRIPTION_REGEX_ALT',
                4,
                $html
            );
        }

        if ($this->_isTagExists('META_TAG_TWITTER_DESCRIPTION_REGEX', $html)) {
            return $this->_parseHtmlTag(
                'META_TAG_TWITTER_DESCRIPTION_REGEX',
                7,
                $html
            );
        }

        if ($this->_isTagExists(
            'META_TAG_TWITTER_DESCRIPTION_REGEX_ALT',
            $html
        )) {
            return $this->_parseHtmlTag(
                'META_TAG_TWITTER_DESCRIPTION_REGEX_ALT',
                4,
                $html
            );
        }

        if ($this->_isTagExists('META_DESCRIPTION_REGEX', $html)) {
            return $this->_parseHtmlTag('META_DESCRIPTION_REGEX', 7, $html);
        }

        if ($this->_isTagExists('META_DESCRIPTION_REGEX_ALT', $html)) {
            return $this->_parseHtmlTag(
                'META_DESCRIPTION_REGEX_ALT',
                4,
                $html
            );
        }

        return null;
    }

    /**
     * Get Web Page Title From HTML
     *
     * @param string|null $html Web Page HTML
     * @param string|null $url  Web Page URL
     *
     * @return string|null Web Page Title
     */
    private function _getPageTitle(
        ?string $html = null,
        ?string $url  = null
    ): ?string
    {
        $title = $this->_getPageTitleFromMetaTags($html);

        if (empty(trim((string) $title))) {
            $title = $this->_getPageTitleFromBody($html);
        }

        if (empty(trim((string) $title))) {
            $title = $this->_getPageTitleFromURL($url);
        }

        return $this->_normalizeTitle($title);
    }

    /**
     * Change Web Page Title To Valid Format
     *
     * @param string|null $title Web Page Title
     *
     * @return string Nomalized Web Page Title
     */
    private function _normalizeTitle(?string $title = null): string
    {
        $title = strip_tags((string) $title);
        $title = htmlspecialchars_decode($title);
        $title = preg_replace('/\s+/su', ' ', $title);
        $title = preg_replace('/(^\s)|(\s$)/su', '', $title);

        if (strlen($title) > 128) {
            $title = (string) mb_substr($title, 0, 128).'[…]';
        }

        $title = htmlspecialchars($title);
        $title = addslashes($title);

        return $title;
    }

    /**
     * Change Web Page Description To Valid Format
     *
     * @param string|null $description Web Page Description
     *
     * @return string Nomalized Web Page Description
     */
    private function _normalizeDescription(?string $description = null): string
    {
        $description = strip_tags((string) $description);
        $description = htmlspecialchars_decode($description);
        $description = preg_replace('/\s+/su', ' ', $description);
        $description = preg_replace('/(^\s)|(\s$)/su', '', $description);

        if (strlen($description) > 256) {
            $description = (string) mb_substr($description, 0, 256).'[…]';
        }

        $description = htmlspecialchars($description);
        $description = addslashes($description);

        return $description;
    }

    /**
     * Get Web Page Title From URL
     *
     * @param string|null $url Web Page URL
     *
     * @return string Web Page Title
     */
    private function _getPageTitleFromURL(?string $url = null): string
    {
        $title = $this->_getDomain($url);

        if (preg_match('/^www\.(.*?)$/', $title)) {
            return preg_replace('/^www\.(.*?)$/', '', $title);
        }

        return $title;
    }

    /**
     * Get Web Page Domain From URL
     *
     * @param string|null $url Web Page URL
     *
     * @return string Web Page Domain
     */
    private function _getDomain(?string $url = null): string
    {
        $domain = preg_replace(
            '/^(http|https)\:\/\/(.*?)(\/(.*?)$|$)/su',
            '$1://$2',
            (string) $url
        );

        $domainChunks = explode('#', $domain);
        $domain       = array_shift($domainChunks);

        $domainChunks = explode('&', $domain);
        $domain       = array_shift($domainChunks);

        $domainChunks = explode('?', $domain);
        $domain       = array_shift($domainChunks);

        $domainChunks = explode('/', $domain);
        $domain       = array_shift($domainChunks);

        return $domain;
    }

    /**
     * Get Web Page HTTP Protocol From URL
     *
     * @param string|null $url Web Page URL
     *
     * @return string Web Page HTTP Protocol
     */
    private function _getProtocol(?string $url = null): string
    {
        if (preg_match('/^https\:\/\/(.*?)$/su', (string) $url)) {
            return 'https';
        }

        return 'http';
    }

    /**
     * Get Web Page Title From Meta Tags
     *
     * @param string|null $html Web Page HTML
     *
     * @return string|null Web Page Title
     */
    private function _getPageTitleFromMetaTags(?string $html = null): ?string
    {
        if ($this->_isTagExists('META_TAG_OG_TITLE_REGEX', $html)) {
            return $this->_parseHtmlTag('META_TAG_OG_TITLE_REGEX', 7, $html);
        }

        if ($this->_isTagExists('META_TAG_OG_TITLE_REGEX_ALT', $html)) {
            return $this->_parseHtmlTag(
                'META_TAG_OG_TITLE_REGEX_ALT',
                4,
                $html
            );
        }

        if ($this->_isTagExists('META_TAG_TWITTER_TITLE_REGEX', $html)) {
            return $this->_parseHtmlTag(
                'META_TAG_TWITTER_TITLE_REGEX',
                7,
                $html
            );
        }

        if ($this->_isTagExists('META_TAG_TWITTER_TITLE_REGEX_ALT', $html)) {
            return $this->_parseHtmlTag(
                'META_TAG_TWITTER_TITLE_REGEX_ALT',
                4,
                $html
            );
        }

        if ($this->_isTagExists('META_TITLE_REGEX', $html)) {
            return $this->_parseHtmlTag('META_TITLE_REGEX', 3, $html);
        }

        return null;
    }

    /**
     * Get Web Page Title From HTML Body
     *
     * @param string|null $html Web Page HTML
     *
     * @return string|null Web Page Title
     */
    private function _getPageTitleFromBody(?string $html = null): ?string
    {
        if ($this->_isTagExists('H1_TITLE_REGEX', $html)) {
            return $this->_parseHtmlTag('H1_TITLE_REGEX', 3, $html);
        }

        if ($this->_isTagExists('H1_TITLE_REGEX_ALT', $html)) {
            return $this->_parseHtmlTag('H1_TITLE_REGEX_ALT', 3, $html);
        }

        if ($this->_isTagExists('MAIN_TITLE_REGEX', $html)) {
            return $this->_parseHtmlTag('MAIN_TITLE_REGEX', 3, $html);
        }

        if ($this->_isTagExists('BODY_TITLE_REGEX', $html)) {
            return $this->_parseHtmlTag('BODY_TITLE_REGEX', 3, $html);
        }

        return null;
    }

    /**
     * Get Web Page HTML From URL
     *
     * @param string|null $url Web Page URL
     *
     * @return string Web Page HTML
     */
    private function _getPageContent(?string $url = null): string
    {
        $html = $this->_getPageHTMLFromCurl($url);

        $html = (string) mb_convert_encoding($html, 'UTF-8');
        $html = htmlspecialchars_decode($html);
        $html = $this->_removePageHTMLTags($html);

        return $html;
    }

    /**
     * Removing Extra Tags From Web Page HTML
     *
     * @param string|null $html Web Page HTML
     *
     * @return string $html Web Page HTML Without Extra Tags
     */
    private function _removePageHTMLTags(?string $html = null): string
    {
        $html = preg_replace('/\<script(.*?)\>(.*?)\<\/script\>/su',
            '',
            (string) $html
        );

        $html = preg_replace('/\<script(.*?)\>/su', '', $html);

        $html = preg_replace(
            '/\<noscript(.*?)\>(.*?)\<\/noscript\>/su',
            '',
            $html
        );

        $html = preg_replace('/\<noscript(.*?)\>/su', '', $html);
        $html = preg_replace('/\<style(.*?)\>(.*?)\<\/style\>/su', '', $html);
        $html = preg_replace('/\<style(.*?)\>/su', '', $html);

        return $html;
    }

    /**
     * Get Web Page HTML From Curl Request
     *
     * @param string|null $url Web Page URL
     *
     * @return string Web Page HTML
     */
    private function _getPageHTMLFromCurl(?string $url = null): string
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_COOKIESESSION, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 5);
        curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $html = curl_exec($curl);

        curl_close($curl);

        return (string) $html;
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
        $metaData     = (array) json_decode($metaDataJson, true);

        $metaData['url'] = null;

        if (array_key_exists('url', $metaData)) {
            $metaData['url'] = base64_decode((string) $metaData['url']);
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
        $url       = null;
        $metaData  = null;

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

    /**
     * Parsing HTML Tag Value By Regexp Rule
     *
     * @param string|null $regexp     Regexp Rule Name
     * @param int|null    $partNumber Number Part Of HTML Tag
     * @param string|null $html       Web Page HTML
     *
     * @return string|null Output Value Of HTML Tag
     */
    private function _parseHtmlTag(
        ?string $regexp     = null,
        ?int    $partNumber = null,
        ?string $html       = null
    ): ?string
    {
        if (empty($regexp)) {
            return null;
        }

        // $regexp = static::$regexp;

        if (empty($partNumber)) {
            return null;
        }

        if (empty($html)) {
            return null;
        }

        $part = sprintf('$\d', $partNumber);

        return (string) preg_replace($regexp, $part, $html);
    }

    /**
     * Chack Is Tag Exist In HTML By Regexp Rule
     *
     * @param string|null $regexp     Regexp Rule Name
     * @param string|null $html       Web Page HTML
     *
     * @return bool Is Tag Exist In HTML
     */
    private function _isTagExists(
        ?string $regexp = null,
        ?string $html   = null
    ): bool
    {
        if (empty($regexp)) {
            return false;
        }

        if (empty($html)) {
            return false;
        }

        // $regexp = static::$regexp;

        return preg_match($regexp, $html);
    }
}
