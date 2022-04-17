<?php

namespace Sonder\Plugins;

/**
 * === Main List Of Shortcodes ===
 *
 * [s]Text[/s]             - Strikethrough text
 * [b]Text[/b]             - Bold Text
 * [i]Text[/i]             - Italic Text
 * [spoiler]Text[/spoiler] - Hidden Text
 * [u]Text[/u]             - Text With Underline
 * [q]Text[/q]             - Quote
 *
 * === Internal Shortcodes (Used By Other Plugins) ===
 *
 * [Reply:<Post ID>]               - Link To Other Post
 * [YouTube:<Video ID>]            - YouTube Player
 * [Link:<URL>:"<Title>"]          - Link
 * [Link:<URL>:"<Title>":Internal] - Internal Link (Without rel="nofollow")
 *
 * === Alternative Syntax ===
 *
 * DELTextDEL               Equal [s]Text[/s]
 * ~~Text~~                 Equal [s]Text[/s]
 * *Text*                   Equal [b]Text[/b]
 * **Text**                 Equal [b]Text[/b]
 * >Text                    Equal [q]Text[/q]
 * [url=<URL>]<Title>[/url] Equal [Link:<URL>:"<Title>"]
 * >><Post ID>              Equal [Reply:<Post ID>]
 */
final class MarkupPlugin
{
    /**
     * @param string|null $text
     *
     * @return string|null
     */
    final public function normalizeSyntax(?string $text = null): ?string
    {
        if (empty($text)) {
            return null;
        }

        $text = str_replace('&gt;', '>', $text);
        $text = preg_replace('/~~(.*?)~~/su', '[s]$1[/s]', $text);
        $text = preg_replace('/DEL(.*?)DEL/su', '[s]$1[/s]', $text);
        $text = preg_replace('/\*\*(.*?)\*\*/su', '[b]$1[/b]', $text);
        $text = preg_replace('/\*(.*?)\*/su', '[i]$1[/i]', $text);

        $text = preg_replace(
            '/%%(.*?)%%/su',
            '[spoiler]$1[/spoiler]',
            $text
        );

        $text = preg_replace(
            '/%%(.*?)%%/su',
            '[spoiler]$1[/spoiler]',
            $text
        );

        $text = preg_replace(
            '/\[([^]]+)]\(([^)]+)\)/su',
            '[Link:$2:"$1"]',
            $text
        );

        $text = preg_replace(
            '/\[url=([^]]+)]([^]]+)\[\/url]/su',
            '[Link:$1:"$2"]',
            $text
        );

        $text = preg_replace(
            '/\[url=([^]]+)]\[\/url]/su',
            '[Link:$1:"$2"]',
            $text
        );

        $text = preg_replace(
            '/\[Link:\/([^:]+):\"([^\"]+)\"]/su',
            '[Link:/$1:"$2":Internal]',
            $text
        );

        $text = preg_replace('/>>([0-9]+)/su', "[Reply:$1]", $text);
        $text = preg_replace('/>(.*?)\n/su', "\n[q]$1[/q]\n", $text);
        $text = preg_replace('/>(.*?)$/su', "\n[q]$1[/q]", $text);
        $text = preg_replace('/\[q]/su', "[q]&gt;", $text);
        $text = preg_replace('/\[q]&gt;([\s]+)/su', "[q]&gt;", $text);
        $text = preg_replace('/\[\/q]([\s]+)\[q]/su', "\n", $text);
        $text = preg_replace('/([\s]+)\[q]/su', "\n[q]", $text);
        $text = preg_replace('/([\s]+)\[\/q]/su', "[/q]\n", $text);
        $text = preg_replace('/\[\/q]([\s]+)/su', "[/q]\n", $text);

        return str_replace('>', '&gt;', $text);
    }

    /**
     * @param string|null $text
     *
     * @return string|null
     */
    final public function markup2html(?string $text = null): ?string
    {
        if (empty($text)) {
            return null;
        }

        $text = preg_replace(
            '/\[s](.*?)\[\/s]/su',
            '<s>$1</s>',
            $text
        );

        $text = preg_replace(
            '/\[b](.*?)\[\/b]/su',
            '<strong>$1</strong>',
            $text
        );

        $text = preg_replace('/\[i](.*?)\[\/i]/su', '<i>$1</i>', $text);

        $text = preg_replace(
            '/\[spoiler](.*?)\[\/spoiler]/su',
            '<span class="spoiler">$1</span>',
            $text
        );

        $text = preg_replace(
            '/\[u](.*?)\[\/u]/su',
            '<span class="utag">$1</span>',
            $text
        );

        $text = preg_replace('/([\s]+)\[q]/su', '[q]', $text);
        $text = preg_replace('/\[q]([\s]+)/su', '[q]', $text);
        $text = preg_replace('/([\s]+)\[\/q]/su', '[/q]', $text);
        $text = preg_replace('/\[\/q]([\s]+)/su', '[/q]', $text);
        $text = preg_replace('/\[q]/su', '[q]<p>', $text);
        $text = preg_replace('/\[\/q]/su', '</p>[/q]', $text);

        $text = preg_replace(
            '/\[q](.*?)\[\/q]/su',
            '<blockquote>$1</blockquote>',
            $text
        );

        $text = preg_replace('/\n+/su', '</p><p>', $text);
        $text = sprintf('<p>%s</p>', $text);

        return $this->parseLinkShortCode($text);
    }

    /**
     * @param string|null $text
     *
     * @return string|null
     */
    final public function normalizeText(?string $text = null): ?string
    {
        if (empty($text)) {
            return null;
        }

        $text = preg_replace('/\n+/su', "<br>", $text);
        $text = preg_replace('/\s+/su', ' ', $text);
        $text = preg_replace('/<br>\s/su', '<br>', $text);
        $text = preg_replace('/\s<br>/su', '<br>', $text);
        $text = preg_replace('/(^\s|\s$)/su', '', $text);
        $text = preg_replace('/(^<br>|<br>$)/su', '', $text);
        $text = preg_replace('/<br>/su', "\n", $text);

        return preg_replace('/\n+/su', "\n", $text);
    }

    /**
     * @param string|null $text
     * @param int $sectionId
     *
     * @return string|null
     */
    final public function parseReplyShortCode(
        ?string $text = null,
        int     $sectionId = 0
    ): ?string
    {
        if (empty($text)) {
            return null;
        }

        return preg_replace(
            '/\[Reply:([0-9]+)]/su',
            '<a
                href="#"
                class="card_snippet_link card_snippet_link_without_level"
                data-id="$1"
                data-section="' . $sectionId . '">
                >>$1
            </a>',
            $text);
    }

    /**
     * @param string|null $text
     *
     * @return string|null
     */
    final public function parseLinkShortCode(?string $text = null): ?string
    {
        if (empty($text)) {
            return null;
        }

        $text = preg_replace(
            '/\[Link:([^:]+):\"([^\"]+)\":Internal]/su',
            '<a href="$1">$2</a>',
            $text
        );

        return preg_replace(
            '/\[Link:([^\"]+):\"([^\"]+)\"]/su',
            '<a href="$1" rel="nofollow">$2</a>',
            $text
        );
    }
}
