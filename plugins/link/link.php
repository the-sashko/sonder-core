<?php
/**
 * Plugin For Getting URL Links Metadata And Generating HTML
 */
class LinkPlugin
{
    const META_TAG_OG_TITLE_REGEX = '/^(.*?)\<meta([\s]+)'.
                                    'property=(\"|\')og\:title(\"|\')([\s]+)'.
                                    'content=(\"|\')(.*?)(\"|\')(.*?)$/su';

    const META_TAG_OG_TITLE_REGEX_ALT = '/^(.*?)\<meta([\s]+)'.
                                        'content=(\"|\')(.*?)\"([\s]+)'.
                                        'property=(\"|\')'.
                                        'og\:title(\"|\')(.*?)$/su';

    const META_TAG_TWITTER_TITLE_REGEX = '/^(.*?)\<meta([\s]+)'.
                                         'name=(\"|\')twitter\:title'.
                                         '(\"|\')([\s]+)content='.
                                         '(\"|\')(.*?)(\"|\')(.*?)$/su';

    const META_TAG_TWITTER_TITLE_REGEX_ALT = '/^(.*?)\<meta([\s]+)content='.
                                             '(\"|\')(.*?)\"([\s]+)'.
                                             'name=(\"|\')twitter\:title'.
                                             '(\"|\')(.*?)$/su';

    const META_TITLE_REGEX = '/^(.*?)\<title([\s]+|)\>'.
                             '(.*?)\<\/title\>(.*?)$/su';

    const H1_TITLE_REGEX = '/^(.*?)\<h1(.*?)\>(.*?)\<\/h1\>(.*?)$/su';

    const H1_TITLE_REGEX_ALT = '/^(.*?)\<h1(.*?)\>(.*?)\<\/h1\>(.*?)$/su';

    const MAIN_TITLE_REGEX = '/^(.*?)\<main(.*?)\>(.*?)\<\/main\>(.*?)$/su';

    const BODY_TITLE_REGEX = '/^(.*?)\<body(.*?)\>(.*?)\<\/body\>(.*?)$/su';

    const META_TAG_OG_DESCRIPTION_REGEX = '/^(.*?)\<meta([\s]+)property='.
                                          '(\"|\')og\:description'.
                                          '(\"|\')([\s]+)content='.
                                          '(\"|\')(.*?)(\"|\')(.*?)$/su';

    const META_TAG_OG_DESCRIPTION_REGEX_ALT = '/^(.*?)\<meta([\s]+)content='.
                                              '(\"|\')(.*?)\"([\s]+)property='.
                                              '(\"|\')og\:description'.
                                              '(\"|\')(.*?)$/su';

    const META_TAG_TWITTER_DESCRIPTION_REGEX = '/^(.*?)\<meta([\s]+)name='.
                                               '(\"|\')twitter\:description'.
                                               '(\"|\')([\s]+)content='.
                                               '(\"|\')(.*?)(\"|\')(.*?)$/su';

    const META_TAG_TWITTER_DESCRIPTION_REGEX_ALT = '/^(.*?)\<meta([\s]+)'.
                                                   'content=(\"|\')'.
                                                   '(.*?)\"([\s]+)name='.
                                                   '(\"|\')twitter\:'.
                                                   'description(\"|\')'.
                                                   '(.*?)$/su';

    const META_DESCRIPTION_REGEX = '/^(.*?)\<meta([\s]+)'.
                                   'name=(\"|\')description(\"|\''.
                                   ')([\s]+)content=(\"|\')(.*?)'.
                                   '(\"|\')(.*?)$/su';

    const META_DESCRIPTION_REGEX_ALT = '/^(.*?)\<meta([\s]+)'.
                                       'content=(\"|\')(.*?)\"([\s]+)'.
                                       'name=(\"|\')description(\"|\')'.
                                       '(.*?)$/su';

    const ARTICLE_DESCRIPTION_REGEX = '/^(.*?)\<article(.*?)\>(.*?)'.
                                      '\<\/article\>(.*?)$/su';

    const MAIN_DESCRIPTION_REGEX = '/^(.*?)\<main(.*?)\>(.*?)\<\/main\>'.
                                   '(.*?)$/su';

    const P_DESCRIPTION_REGEX = '/^(.*?)\<p(.*?)\>(.*?)\<\/p\>(.*?)$/su';

    const BODY_DESCRIPTION_REGEX = '/^(.*?)\<body(.*?)\>(.*?)\<\/body\>'.
                                   '(.*?)$/su';

    const META_TAG_OG_IMAGE_REGEX = '/^(.*?)\<meta([\s]+)property=(\"|\')'.
                                    'og\:image(\"|\')([\s]+)'.
                                    'content=(\"|\')(.*?)(\"|\')(.*?)$/su';

    const META_TAG_OG_IMAGE_REGEX_ALT = '/^(.*?)\<meta([\s]+)content='.
                                        '(\"|\')(.*?)\"([\s]+)property='.
                                        '(\"|\')og\:image(\"|\')(.*?)$/su';

    const META_TAG_TWITTER_IMAGE_REGEX = '/^(.*?)\<meta([\s]+)name=(\"|\')'.
                                         'twitter\:image(\"|\')([\s]+)'.
                                         'content=(\"|\')(.*?)(\"|\')'.
                                         '(.*?)$/su';

    const META_TAG_TWITTER_IMAGE_REGEX_ALT = '/^(.*?)\<meta([\s]+)content='.
                                             '(\"|\')(.*?)\"([\s]+)name='.
                                             '(\"|\')twitter\:image(\"|\')'.
                                             '(.*?)$/su';

    const LINK_IMAGE_REGEX = '/^(.*?)\<link([\s]+)rel=(\"|\')'.
                             'image_src(\"|\')([\s]+)href=(\"|\')'.
                             '(.*?)(\"|\')(.*?)$/su';

    const LINK_IMAGE_REGEX_ALT = '/^(.*?)\<link([\s]+)href=(\"|\')'.
                                 '(.*?)\"([\s]+)rel=(\"|\')'.
                                 'image_src(\"|\')(.*?)$/su';

    const IMG_IMAGE_REGEX_ALT = '/^(.*?)\<img(.*?)src=(\"|\')(.*?)(\"|\')'.
                                '(.*?)\>(.*?)$/su';

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    public function parseLinkURL(string $text = '') : string
    {
        $text = preg_replace_callback(
            '/(https|http)\:\/\/(.*?)(\s|$)/su',
            [
                $this,
                '_makeLinkShortCode'
            ],
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
    public function parseLinkShortCode(string $text = '') : string
    {
        return  preg_replace(
            '/\[Link\:(.*?)\:\"(.*?)\"\]/su',
            '<a href="$1"'.
            'target="_blank" rel="nofollow" class="post_external_link">'.
            '<i class="fas fa-link"></i>&nbsp;$2'.
            '</a>',
            $text
        );
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _getWebPageMetaData(string $url = '') : array
    {
        $cacheFile = __DIR__.'/cache/'.hash('sha512',$url).'_'.
                     hash('md5',$url).'.dat';

        if (file_exists($cacheFile) && is_file($cacheFile)) {
            return $this->_getWebPageMetaDataFromCache($cacheFile);
        }

        $pageHTML = $this->_getPageContent($url);
        $title = $this->_getPageTitle($pageHTML, $url);
        $description = $this->_getPageDescription($pageHTML);
        $image = $this->_getPageImage($pageHTML);

        $metaData = [
            'url'         => $url,
            'title'       => $title,
            'description' => $description,
            'image'       => $image
        ];

        $this->_saveWebPageMetaDataToCache($metaData, $cacheFile);

        return $metaData;
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _saveWebPageMetaDataToCache(
        array $metaData = [],
        string $cacheFile = ''
    ) : void
    {
        $metaData['url'] = base64_encode($metaData['url']);
        file_put_contents($urlCacheFile, json_encode($metaDataJSON));
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _getPageImage(
        string $pageHTML = '',
        string $url = ''
    ) : string
    {
        $image = $this->_getPageImageFromMetaTags($pageHTML);

        if (trim($image) < 5) {
            $image = _getPageImageFromBody($pageHTML);
        }

        return $this->_normalizeImage($image, $url);
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _getPageImageFromMetaTags(string $html = '') : string
    {
        if ($this->_isTagExists('META_TAG_OG_IMAGE_REGEX', $html)) {
            return $this->_parseHTMLTag('META_TAG_OG_IMAGE_REGEX', 7, $html);
        }

        if ($this->_isTagExists('META_TAG_OG_IMAGE_REGEX_ALT', $html)) {
            return $this->_parseHTMLTag(
                'META_TAG_OG_IMAGE_REGEX_ALT',
                4,
                $html
            );
        }

        if ($this->_isTagExists('META_TAG_TWITTER_IMAGE_REGEX', $html)) {
            return $this->_parseHTMLTag(
                'META_TAG_TWITTER_IMAGE_REGEX',
                7,
                $html
            );
        }

        if ($this->_isTagExists('META_TAG_TWITTER_IMAGE_REGEX_ALT', $html)) {
            return $this->_parseHTMLTag(
                'META_TAG_TWITTER_IMAGE_REGEX_ALT',
                4,
                $html
            );
        }

        if ($this->_isTagExists('LINK_IMAGE_REGEX', $html)) {
            return $this->_parseHTMLTag('LINK_IMAGE_REGEX', 7, $html);
        }

        if ($this->_isTagExists('LINK_IMAGE_REGEX_ALT', $html)) {
            return $this->_parseHTMLTag('LINK_IMAGE_REGEX_ALT', 4, $html);
        }

        return '';
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _getPageImageFromBody(string $html = '') : string
    {
        if ($this->_isTagExists('IMG_IMAGE_REGEX_ALT', $html)) {
            return $this->_parseHTMLTag('IMG_IMAGE_REGEX_ALT', 4, $html);
        }

        return '';
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _isImageValid(string $image = '') : bool
    {
        return strlen($image) >= 5 &&
        preg_match('/^(.*?)\.((jpg)|(jpeg)|(bmp)|(gif)|(png))$/su', $image);
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _normalizeImage(
        string $image = '',
        string $url = ''
    ) : string
    {
        $image = trim($image);

        if (!$this->_isImageValid($image)) {
            return '/assets/img/website.png';
        }

        if (preg_match('/^\/\/(.*?)$/su',$image)) {
            $protocol = $this->_getProtocol($url);
            return "{$protocol}:{$image}";
        }

        if (preg_match('/^\/(.*?)$/su',$image)) {
            $protocol = $this->_getProtocol($url);
            $domain = $this->_getDomain($url);
            return  "{$protocol}://{$domain}/{$image}";
        }

        if (!preg_match('/^http(s|)\:\/\/(.*?)$/su',$image)) {
            $url = exeplode('#', $url)[0];
            $url = exeplode('&', $url)[0];
            $url = exeplode('?', $url)[0];

            if (!preg_match('/^(.*?)\/$/su', $url)) {
                $url = $url.'/';
            }

            return "{$url}{$image}";
        }

        return $image;
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _getPageDescription(string $pageHTML = '') : string
    {
        $description = $this->_getPageDescriptionFromMetaTags($pageHTML);

        if (trim($description) < 3) {
            $description = $this->_getPageDescriptionFromBody($pageHTML);
        }

        return $this->_normalizeDescription($description);
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _getPageDescriptionFromBody(
        string $html = ''
    ) : string
    {
        if ($this->_isTagExists('ARTICLE_DESCRIPTION_REGEX', $html)) {
            return $this->_parseHTMLTag('ARTICLE_DESCRIPTION_REGEX', 3, $html);
        }

        if ($this->_isTagExists('MAIN_DESCRIPTION_REGEX', $html)) {
            return $this->_parseHTMLTag('MAIN_DESCRIPTION_REGEX', 3, $html);
        }

        if ($this->_isTagExists('P_DESCRIPTION_REGEX', $html)) {
            return $this->_parseHTMLTag('P_DESCRIPTION_REGEX', 3, $html);
        }

        if ($this->_isTagExists('BODY_DESCRIPTION_REGEX', $html)) {
            return $this->_parseHTMLTag('BODY_DESCRIPTION_REGEX', 3, $html);
        }

        return $html;
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _getPageDescriptionFromMetaTags(
        string $pageHTML = ''
    ) : string
    {
        if ($this->_isTagExists('META_TAG_OG_DESCRIPTION_REGEX', $html)) {
            return $this->_parseHTMLTag(
                'META_TAG_OG_DESCRIPTION_REGEX',
                7,
                $html
            );
        }

        if ($this->_isTagExists('META_TAG_OG_DESCRIPTION_REGEX_ALT', $html)) {
            return $this->_parseHTMLTag(
                'META_TAG_OG_DESCRIPTION_REGEX_ALT',
                4,
                $html
            );
        }

        if ($this->_isTagExists('META_TAG_TWITTER_DESCRIPTION_REGEX', $html)) {
            return $this->_parseHTMLTag(
                'META_TAG_TWITTER_DESCRIPTION_REGEX',
                7,
                $html
            );
        }

        if ($this->_isTagExists(
            'META_TAG_TWITTER_DESCRIPTION_REGEX_ALT',
            $html
        )) {
            return $this->_parseHTMLTag(
                'META_TAG_TWITTER_DESCRIPTION_REGEX_ALT',
                4,
                $html
            );
        }

        if ($this->_isTagExists('META_DESCRIPTION_REGEX', $html)) {
            return $this->_parseHTMLTag('META_DESCRIPTION_REGEX', 7, $html);
        }

        if ($this->_isTagExists('META_DESCRIPTION_REGEX_ALT',$html)) {
            return $this->_parseHTMLTag(
                'META_DESCRIPTION_REGEX_ALT',
                4,
                $html
            );
        }

        return '';
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _getPageTitle(
        string $pageHTML = '',
        string $url = ''
    ) : string
    {
        $title = $this->_getPageTitleFromMetaTags($pageHTML);

        if (strlen(trim($title)) < 3) {
            $title = $this->_getPageTitleFromBody($pageHTML);
        }

        if (strlen(trim($title)) < 3) {
            $title = $this->_getPageTitleFromURL($url);
        }

        return $this->_normalizeTitle($title);
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _normalizeTitle(string $title = '') : string
    {
        $title = strip_tags($title);
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
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _normalizeDescription(string $description = '') : string
    {
        $description = strip_tags($description);
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
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _getPageTitleFromURL(string $url = '') : string
    {
        $title = $this->_getDomain($url);

        if (preg_match('/^www\.(.*?)$/', $title)) {
            return preg_replace('/^www\.(.*?)$/', '', $title);
        }

        return $title;
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _getDomain(string $url = '') : string
    {
        $domain = preg_replace(
            '/^(http|https)\:\/\/(.*?)(\/(.*?)$|$)/su',
            '$1://$2',
            $url
        );

        $domain = explode('#', $domain)[0];
        $domain = explode('&', $domain)[0];
        $domain = explode('?', $domain)[0];
        $domain = explode('/', $domain)[0];

        return $domain;
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _getProtocol(string $url = '') : string
    {
        if (preg_match('/^https\:\/\/(.*?)$/su', $url)) {
            return 'https';
        }

        return 'http';
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _getPageTitleFromMetaTags(string $html = '') : string
    {
        if ($this->_isTagExists('META_TAG_OG_TITLE_REGEX', $html)) {
            return $this->_parseHTMLTag('META_TAG_OG_TITLE_REGEX', 7, $html);
        }

        if ($this->_isTagExists('META_TAG_OG_TITLE_REGEX_ALT', $html)) {
            return $this->_parseHTMLTag(
                'META_TAG_OG_TITLE_REGEX_ALT',
                4,
                $html
            );
        }

        if ($this->_isTagExists('META_TAG_TWITTER_TITLE_REGEX', $html)) {
            return $this->_parseHTMLTag(
                'META_TAG_TWITTER_TITLE_REGEX',
                7,
                $html
            );
        }

        if ($this->_isTagExists('META_TAG_TWITTER_TITLE_REGEX_ALT', $html)) {
            return $this->_parseHTMLTag(
                'META_TAG_TWITTER_TITLE_REGEX_ALT',
                4,
                $html
            );
        }

        if ($this->_isTagExists('META_TITLE_REGEX', $html)) {
            return $this->_parseHTMLTag('META_TITLE_REGEX', 3, $html);
        }

        return '';
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _getPageTitleFromBody(string $pageHTML = '') : string
    {
        if ($this->_isTagExists('H1_TITLE_REGEX', $html)) {
            return $this->_parseHTMLTag('H1_TITLE_REGEX', 3, $html);
        }

        if ($this->_isTagExists('H1_TITLE_REGEX_ALT', $html)) {
            return $this->_parseHTMLTag('H1_TITLE_REGEX_ALT', 3, $html);
        }

        if ($this->_isTagExists('MAIN_TITLE_REGEX', $html)) {
            return $this->_parseHTMLTag('MAIN_TITLE_REGEX', 3, $html);
        }

        if ($this->_isTagExists('BODY_TITLE_REGEX', $html)) {
            return $this->_parseHTMLTag('BODY_TITLE_REGEX', 3, $html);
        }

        return '';
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _getPageContent(string $url = '') : string
    {
        $pageHTML = _getPageHTMLFromCurl($url);

        $pageHTML = (string) mb_convert_encoding($pageHTML,'UTF-8');
        $pageHTML = htmlspecialchars_decode($pageHTML);
        $pageHTML = $this->_removePageHTMLTags($pageHTML);

        return $pageHTML;
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _removePageHTMLTags(string $html = '') : string
    {
        $html = preg_replace('/\<script(.*?)\>(.*?)\<\/script\>/su',
            '',
            $html
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
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _getPageHTMLFromCurl(string $url = '') : string
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_COOKIESESSION, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 5);
        curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $pageHTML = curl_exec($curl);

        curl_close($curl);

        return $pageHTML;
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _getWebPageMetaDataFromCache(
        string $fileCache = ''
    ) : array
    {
        $metaDataJSON = file_get_contents($fileCache);
        $metaData = json_decode($metaDataJSON,true);

        if (array_key_exists('url', $metaData)) {
            $metaData['url'] = base64_decode($metaData['url']);
        } else {
            $metaData['url'] = '#';
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
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _makeLinkShortCode(array $URLParts = []) : string
    {
        $shortCode = '';

        if (count($URLParts) > 0 && strlen($URLParts[0]) > 0) {
            $url = $URLParts[0];
            $url = trim($url);
            $url = preg_replace('/([^0-9a-z\/_=\-]+)$/su','',$url);
            $metaData = $this->_getWebPageMetaData($url);
        }

        $shortCode = " [Link:{$url}:\"{$metaData['title']}\"] ";

        return $shortCode;
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _parseHTMLTag(
        string $regexp = '',
        int $partNumber = 1,
        string $html = ''
    ) : string
    {
        if (strlen($regexp) < 1) {
            return '';
        }

        $regexp = static::$regexp;

        if ($partNumber < 1) {
            return '';
        }

        $part = '$'.$partNumber;

        if (strlen($html) < 8) {
            return '';
        }

        return (string) preg_replace($regexp, $part, $html);
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _isTagExists(
        string $regexp = '',
        string $html = ''
    ) : bool
    {
        if (strlen($regexp) < 1) {
            return false;
        }

        $regexp = static::$regexp;

        if (strlen($html) < 8) {
            return false;
        }

        return preg_match($regexp, $html);
    }
}
?>