<?php

namespace Sonder\Plugins\Link\Classes;

final class Parser
{
    const META_TAG_OG_TITLE_REGEX = '/^(.*?)\<meta([\s]+)' .
    'property=(\"|\')og\:title(\"|\')([\s]+)' .
    'content=(\"|\')(.*?)(\"|\')(.*?)$/su';

    const META_TAG_OG_TITLE_REGEX_ALT = '/^(.*?)\<meta([\s]+)' .
    'content=(\"|\')(.*?)\"([\s]+)' .
    'property=(\"|\')' .
    'og\:title(\"|\')(.*?)$/su';

    const META_TAG_TWITTER_TITLE_REGEX = '/^(.*?)\<meta([\s]+)' .
    'name=(\"|\')twitter\:title' .
    '(\"|\')([\s]+)content=' .
    '(\"|\')(.*?)(\"|\')(.*?)$/su';

    const META_TAG_TWITTER_TITLE_REGEX_ALT = '/^(.*?)\<meta([\s]+)content=' .
    '(\"|\')(.*?)\"([\s]+)' .
    'name=(\"|\')twitter\:title' .
    '(\"|\')(.*?)$/su';

    const META_TITLE_REGEX = '/^(.*?)\<title([\s]+|)\>' .
    '(.*?)\<\/title\>(.*?)$/su';

    const H1_TITLE_REGEX = '/^(.*?)\<h1(.*?)\>(.*?)\<\/h1\>(.*?)$/su';

    const H1_TITLE_REGEX_ALT = '/^(.*?)\<h1(.*?)\>(.*?)\<\/h1\>(.*?)$/su';

    const MAIN_TITLE_REGEX = '/^(.*?)\<main(.*?)\>(.*?)\<\/main\>(.*?)$/su';

    const BODY_TITLE_REGEX = '/^(.*?)\<body(.*?)\>(.*?)\<\/body\>(.*?)$/su';

    const META_TAG_OG_DESCRIPTION_REGEX = '/^(.*?)\<meta([\s]+)property=' .
    '(\"|\')og\:description' .
    '(\"|\')([\s]+)content=' .
    '(\"|\')(.*?)(\"|\')(.*?)$/su';

    const META_TAG_OG_DESCRIPTION_REGEX_ALT = '/^(.*?)\<meta([\s]+)content=' .
    '(\"|\')(.*?)\"([\s]+)property=' .
    '(\"|\')og\:description' .
    '(\"|\')(.*?)$/su';

    const META_TAG_TWITTER_DESCRIPTION_REGEX = '/^(.*?)\<meta([\s]+)name=' .
    '(\"|\')twitter\:description' .
    '(\"|\')([\s]+)content=' .
    '(\"|\')(.*?)(\"|\')(.*?)$/su';

    const META_TAG_TWITTER_DESCRIPTION_REGEX_ALT = '/^(.*?)\<meta([\s]+)' .
    'content=(\"|\')' .
    '(.*?)\"([\s]+)name=' .
    '(\"|\')twitter\:' .
    'description(\"|\')' .
    '(.*?)$/su';

    const META_DESCRIPTION_REGEX = '/^(.*?)\<meta([\s]+)' .
    'name=(\"|\')description(\"|\')' .
    '([\s]+)content=(\"|\')(.*?)' .
    '(\"|\')(.*?)$/su';

    const META_DESCRIPTION_REGEX_ALT = '/^(.*?)\<meta([\s]+)' .
    'content=(\"|\')(.*?)\"([\s]+)' .
    'name=(\"|\')description(\"|\')' .
    '(.*?)$/su';

    const ARTICLE_DESCRIPTION_REGEX = '/^(.*?)\<article(.*?)\>(.*?)' .
    '\<\/article\>(.*?)$/su';

    const MAIN_DESCRIPTION_REGEX = '/^(.*?)\<main(.*?)\>(.*?)\<\/main\>' .
    '(.*?)$/su';

    const P_DESCRIPTION_REGEX = '/^(.*?)\<p(.*?)\>(.*?)\<\/p\>(.*?)$/su';

    const BODY_DESCRIPTION_REGEX = '/^(.*?)\<body(.*?)\>(.*?)\<\/body\>' .
    '(.*?)$/su';

    const META_TAG_OG_IMAGE_REGEX = '/^(.*?)\<meta([\s]+)property=(\"|\')' .
    'og\:image(\"|\')([\s]+)' .
    'content=(\"|\')(.*?)(\"|\')(.*?)$/su';

    const META_TAG_OG_IMAGE_REGEX_ALT = '/^(.*?)\<meta([\s]+)content=' .
    '(\"|\')(.*?)\"([\s]+)property=' .
    '(\"|\')og\:image(\"|\')(.*?)$/su';

    const META_TAG_TWITTER_IMAGE_REGEX = '/^(.*?)\<meta([\s]+)name=(\"|\')' .
    'twitter\:image(\"|\')([\s]+)' .
    'content=(\"|\')(.*?)(\"|\')' .
    '(.*?)$/su';

    const META_TAG_TWITTER_IMAGE_REGEX_ALT = '/^(.*?)\<meta([\s]+)content=' .
    '(\"|\')(.*?)\"([\s]+)name=' .
    '(\"|\')twitter\:image(\"|\')' .
    '(.*?)$/su';

    const LINK_IMAGE_REGEX = '/^(.*?)\<link([\s]+)rel=(\"|\')' .
    'image_src(\"|\')([\s]+)href=(\"|\')' .
    '(.*?)(\"|\')(.*?)$/su';

    const LINK_IMAGE_REGEX_ALT = '/^(.*?)\<link([\s]+)href=(\"|\')' .
    '(.*?)\"([\s]+)rel=(\"|\')' .
    'image_src(\"|\')(.*?)$/su';

    const IMG_IMAGE_REGEX_ALT = '/^(.*?)\<img(.*?)src=(\"|\')(.*?)(\"|\')' .
    '(.*?)\>(.*?)$/su';

    /**
     * @param string|null $url
     *
     * @return string
     */
    final public function getPageContent(?string $url = null): string
    {
        $html = $this->_getPageHtmlFromCurl($url);

        $html = (string)mb_convert_encoding($html, 'UTF-8');
        $html = htmlspecialchars_decode($html);

        return $this->_removePageHtmlTags($html);
    }

    /**
     * @param string|null $html
     * @param string|null $url
     *
     * @return string|null
     */
    final public function getPageTitle(
        ?string $html = null,
        ?string $url = null
    ): ?string
    {
        $title = $this->_getPageTitleFromMetaTags($html);

        if (empty(trim((string)$title))) {
            $title = $this->_getPageTitleFromBody($html);
        }

        if (empty(trim((string)$title))) {
            $title = $this->_getPageTitleFromUrl($url);
        }

        return $this->_normalizeTitle($title);
    }

    /**
     * @param string|null $html
     *
     * @return string
     */
    final public function getPageDescription(?string $html = null): string
    {
        $description = $this->_getPageDescriptionFromMetaTags($html);

        if (empty($description)) {
            $description = $this->_getPageDescriptionFromBody($html);
        }

        return $this->_normalizeDescription($description);
    }

    /**
     * @param string|null $html
     * @param string|null $url
     *
     * @return string|null
     */
    final public function getPageImage(
        ?string $html = null,
        ?string $url = null
    ): ?string
    {
        if (empty($html) || empty($url)) {
            return null;
        }

        $image = $this->_getPageImageFromMetaTags($html);

        if (empty($image)) {
            $image = $this->_getPageImageFromBody($html);
        }

        return $this->_normalizeImage($image, $url);
    }

    /**
     * @param string|null $url
     *
     * @return string
     */
    private function _getPageHtmlFromCurl(?string $url = null): string
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

        return (string)$html;
    }

    /**
     * @param string|null $html
     *
     * @return string
     */
    private function _removePageHtmlTags(?string $html = null): string
    {
        $html = preg_replace('/<script(.*?)>(.*?)<\/script>/su',
            '',
            (string)$html
        );

        $html = preg_replace('/<script(.*?)>/su', '', $html);

        $html = preg_replace(
            '/<noscript(.*?)>(.*?)<\/noscript>/su',
            '',
            $html
        );

        $html = preg_replace(
            '/<noscript(.*?)>/su',
            '',
            $html
        );

        $html = preg_replace(
            '/<style(.*?)>(.*?)<\/style>/su',
            '',
            $html
        );

        return preg_replace('/<style(.*?)>/su', '', $html);
    }

    /**
     * @param string|null $html
     *
     * @return string|null
     */
    private function _getPageTitleFromMetaTags(?string $html = null): ?string
    {
        if (
            $this->_isTagExists(Parser::META_TAG_OG_TITLE_REGEX, $html)
        ) {
            return $this->_parseHtmlTag(
                Parser::META_TAG_OG_TITLE_REGEX,
                7,
                $html
            );
        }

        if (
            $this->_isTagExists(
                Parser::META_TAG_OG_TITLE_REGEX_ALT,
                $html
            )
        ) {
            return $this->_parseHtmlTag(
                Parser::META_TAG_OG_TITLE_REGEX_ALT,
                4,
                $html
            );
        }

        if (
            $this->_isTagExists(
                Parser::META_TAG_TWITTER_TITLE_REGEX,
                $html
            )
        ) {
            return $this->_parseHtmlTag(
                Parser::META_TAG_TWITTER_TITLE_REGEX,
                7,
                $html
            );
        }

        if ($this->_isTagExists(
            Parser::META_TAG_TWITTER_TITLE_REGEX_ALT, $html)
        ) {
            return $this->_parseHtmlTag(
                Parser::META_TAG_TWITTER_TITLE_REGEX_ALT,
                4,
                $html
            );
        }

        if ($this->_isTagExists(Parser::META_TITLE_REGEX, $html)) {
            return $this->_parseHtmlTag(
                Parser::META_TITLE_REGEX,
                3,
                $html
            );
        }

        return null;
    }

    /**
     * @param string|null $html
     *
     * @return string|null
     */
    private function _getPageTitleFromBody(?string $html = null): ?string
    {
        if ($this->_isTagExists(Parser::H1_TITLE_REGEX, $html)) {
            return $this->_parseHtmlTag(
                Parser::H1_TITLE_REGEX,
                3,
                $html
            );
        }

        if ($this->_isTagExists(Parser::H1_TITLE_REGEX_ALT, $html)) {
            return $this->_parseHtmlTag(
                Parser::H1_TITLE_REGEX_ALT,
                3,
                $html
            );
        }

        if ($this->_isTagExists(Parser::MAIN_TITLE_REGEX, $html)) {
            return $this->_parseHtmlTag(
                Parser::MAIN_TITLE_REGEX,
                3,
                $html
            );
        }

        if ($this->_isTagExists(Parser::BODY_TITLE_REGEX, $html)) {
            return $this->_parseHtmlTag(
                Parser::BODY_TITLE_REGEX,
                3,
                $html
            );
        }

        return null;
    }

    /**
     * @param string|null $url
     *
     * @return string
     */
    private function _getPageTitleFromUrl(?string $url = null): string
    {
        $title = $this->_getDomain($url);

        if (preg_match('/^www\.(.*?)$/', $title)) {
            $title = preg_replace(
                '/^www\.(.*?)$/',
                '',
                $title
            );
        }

        return $title;
    }

    /**
     * @param string|null $title
     *
     * @return string
     */
    private function _normalizeTitle(?string $title = null): string
    {
        $title = strip_tags((string)$title);
        $title = htmlspecialchars_decode($title);
        $title = preg_replace('/\s+/su', ' ', $title);
        $title = preg_replace('/(^\s)|(\s$)/su', '', $title);

        if (strlen($title) > 128) {
            $title = mb_substr($title, 0, 128) . '[…]';
        }

        $title = htmlspecialchars($title);

        return addslashes($title);
    }

    /**
     * @param string|null $html
     *
     * @return string|null
     */
    private function _getPageDescriptionFromMetaTags(
        ?string $html = null
    ): ?string
    {
        if ($this->_isTagExists(
            Parser::META_TAG_OG_DESCRIPTION_REGEX, $html)
        ) {
            return $this->_parseHtmlTag(
                Parser::META_TAG_OG_DESCRIPTION_REGEX,
                7,
                $html
            );
        }

        if ($this->_isTagExists(
            Parser::META_TAG_OG_DESCRIPTION_REGEX_ALT, $html)
        ) {
            return $this->_parseHtmlTag(
                Parser::META_TAG_OG_DESCRIPTION_REGEX_ALT,
                4,
                $html
            );
        }

        if ($this->_isTagExists(
            Parser::META_TAG_TWITTER_DESCRIPTION_REGEX, $html)
        ) {
            return $this->_parseHtmlTag(
                Parser::META_TAG_TWITTER_DESCRIPTION_REGEX,
                7,
                $html
            );
        }

        if ($this->_isTagExists(
            Parser::META_TAG_TWITTER_DESCRIPTION_REGEX_ALT,
            $html
        )) {
            return $this->_parseHtmlTag(
                Parser::META_TAG_TWITTER_DESCRIPTION_REGEX_ALT,
                4,
                $html
            );
        }

        if ($this->_isTagExists(Parser::META_DESCRIPTION_REGEX, $html)) {
            return $this->_parseHtmlTag(
                Parser::META_DESCRIPTION_REGEX,
                7,
                $html
            );
        }

        if ($this->_isTagExists(
            Parser::META_DESCRIPTION_REGEX_ALT, $html)
        ) {
            return $this->_parseHtmlTag(
                Parser::META_DESCRIPTION_REGEX_ALT,
                4,
                $html
            );
        }

        return null;
    }

    /**
     * @param string|null $html
     *
     * @return string|null
     */
    private function _getPageDescriptionFromBody(?string $html = null): ?string
    {
        if ($this->_isTagExists(
            Parser::ARTICLE_DESCRIPTION_REGEX, $html)
        ) {
            return $this->_parseHtmlTag(
                Parser::ARTICLE_DESCRIPTION_REGEX,
                3,
                $html
            );
        }

        if ($this->_isTagExists(Parser::MAIN_DESCRIPTION_REGEX, $html)) {
            return $this->_parseHtmlTag(
                Parser::MAIN_DESCRIPTION_REGEX,
                3,
                $html
            );
        }

        if ($this->_isTagExists(Parser::P_DESCRIPTION_REGEX, $html)) {
            return $this->_parseHtmlTag(
                Parser::P_DESCRIPTION_REGEX,
                3,
                $html
            );
        }

        if ($this->_isTagExists(Parser::BODY_DESCRIPTION_REGEX, $html)) {
            return $this->_parseHtmlTag(
                Parser::BODY_DESCRIPTION_REGEX,
                3,
                $html
            );
        }

        return $html;
    }

    /**
     * @param string|null $description
     *
     * @return string
     */
    private function _normalizeDescription(?string $description = null): string
    {
        $description = strip_tags((string)$description);
        $description = htmlspecialchars_decode($description);

        $description = preg_replace(
            '/\s+/su',
            ' ',
            $description
        );

        $description = preg_replace(
            '/(^\s)|(\s$)/su',
            '',
            $description
        );

        if (strlen($description) > 256) {
            $description = mb_substr($description, 0, 256) . '[…]';
        }

        $description = htmlspecialchars($description);

        return addslashes($description);
    }

    /**
     * @param string|null $html
     *
     * @return string|null
     */
    private function _getPageImageFromMetaTags(?string $html = null): ?string
    {
        if (
            $this->_isTagExists(Parser::META_TAG_OG_IMAGE_REGEX, $html)
        ) {
            return $this->_parseHtmlTag(
                Parser::META_TAG_OG_IMAGE_REGEX,
                7,
                $html
            );
        }

        if ($this->_isTagExists(
            Parser::META_TAG_OG_IMAGE_REGEX_ALT, $html)
        ) {
            return $this->_parseHtmlTag(
                Parser::META_TAG_OG_IMAGE_REGEX_ALT,
                4,
                $html
            );
        }

        if (
            $this->_isTagExists(
                Parser::META_TAG_TWITTER_IMAGE_REGEX,
                $html
            )
        ) {
            return $this->_parseHtmlTag(
                Parser::META_TAG_TWITTER_IMAGE_REGEX,
                7,
                $html
            );
        }

        if ($this->_isTagExists(Parser::META_TAG_TWITTER_IMAGE_REGEX_ALT,
            $html)) {
            return $this->_parseHtmlTag(
                Parser::META_TAG_TWITTER_IMAGE_REGEX_ALT,
                4,
                $html
            );
        }

        if ($this->_isTagExists(Parser::LINK_IMAGE_REGEX, $html)) {
            return $this->_parseHtmlTag(
                Parser::LINK_IMAGE_REGEX,
                7,
                $html
            );
        }

        if ($this->_isTagExists(Parser::LINK_IMAGE_REGEX_ALT, $html)) {
            return $this->_parseHtmlTag(
                Parser::LINK_IMAGE_REGEX_ALT,
                4,
                $html
            );
        }

        return null;
    }

    /**
     * @param string|null $html
     *
     * @return string|null
     */
    private function _getPageImageFromBody(?string $html = null): ?string
    {
        $image = null;

        if ($this->_isTagExists(Parser::IMG_IMAGE_REGEX_ALT, $html)) {
            $image = $this->_parseHtmlTag(
                Parser::IMG_IMAGE_REGEX_ALT,
                4,
                $html
            );
        }

        return $image;
    }

    /**
     * @param string|null $image
     * @param string|null $url
     *
     * @return string
     */
    private function _normalizeImage(
        ?string $image = null,
        ?string $url = null
    ): string
    {
        $url = (string)$url;
        $image = (string)$image;

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
            $domain = $this->_getDomain($url);

            return sprintf('%s://%s/%s', $protocol, $domain, $image);
        }

        if (!preg_match('/^http(s|):\/\/(.*?)$/su', $image)) {
            $urlChunks = explode('#', $url);
            $url = array_shift($urlChunks);

            $urlChunks = explode('&', $url);
            $url = array_shift($urlChunks);

            $urlChunks = explode('?', $url);
            $url = array_shift($urlChunks);

            $url = preg_replace('/^(.*?)\/$/su', '$1', $url);

            return sprintf('%s/%s', $url, $image);
        }

        return $image;
    }

    /**
     * @param string|null $regexp
     * @param string|null $html
     *
     * @return bool
     */
    private function _isTagExists(
        ?string $regexp = null,
        ?string $html = null
    ): bool
    {
        if (empty($regexp) || empty($html)) {
            return false;
        }

        return preg_match($regexp, $html);
    }

    /**
     * @param string|null $regexp
     * @param int|null $partNumber
     * @param string|null $html
     *
     * @return string|null
     */
    private function _parseHtmlTag(
        ?string $regexp = null,
        ?int    $partNumber = null,
        ?string $html = null
    ): ?string
    {
        if (empty($regexp)) {
            return null;
        }

        if (empty($partNumber) || empty($html)) {
            return null;
        }

        $part = sprintf('$%s', $partNumber);

        return (string)preg_replace($regexp, $part, $html);
    }

    /**
     * @param string|null $url
     *
     * @return string
     */
    private function _getProtocol(?string $url = null): string
    {
        $protocol = 'http';

        if (preg_match('/^https:\/\/(.*?)$/su', (string)$url)) {
            $protocol = 'https';
        }

        return $protocol;
    }

    /**
     * @param string|null $url
     *
     * @return string
     */
    private function _getDomain(?string $url = null): string
    {
        $domain = preg_replace(
            '/^(http|https):\/\/(.*?)(\/(.*?)$|$)/su',
            '$1://$2',
            (string)$url
        );

        $domainChunks = explode('#', $domain);
        $domain = array_shift($domainChunks);

        $domainChunks = explode('&', $domain);
        $domain = array_shift($domainChunks);

        $domainChunks = explode('?', $domain);
        $domain = array_shift($domainChunks);

        $domainChunks = explode('/', $domain);

        return array_shift($domainChunks);
    }

    /**
     * @param string|null $image
     *
     * @return bool
     */
    private function _isImageValid(?string $image = null): bool
    {
        return !empty($image) &&
            preg_match(
                '/^(.*?)\.((jpg)|(jpeg)|(bmp)|(gif)|(png))$/su',
                $image
            );
    }
}
