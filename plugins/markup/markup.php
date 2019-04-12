<?php
/**
 * Plugin For Generating HTML From Shortcodes Or Markdown Like Markup Syntax
 *
 * === Main List Of Shortcodes ===
 *
 * [s]Text[/s]             - Striked Text
 * [b]Text[/b]             - Bold Text
 * [i]Text[/i]             - Italic Text
 * [spoiler]Text[/spoiler] - Hidden Text
 * [u]Text[/u]             - Text With Underline
 * [q]Text[/q]             - Quote
 *
 * === Internal Shortcodes (Used By Other Plugins) ===
 *
 * [Reply:<Post ID>]      - Link to other post
 * [YouTube:<Video ID>]   - YouTube player
 * [Link:<URL>:"<Title>"] - Link
 *
 * === Alternative Syntax ===
 *
 * DELTextDEL  Equal [s]Text[/s]
 * ~~Text~~    Equal [s]Text[/s]
 * *Text*      Equal [b]Text[/b]
 * **Text**    Equal [b]Text[/b]
 * >Text       Equal [q]Text[/q]
 * >><Post ID> Equal [Reply:<Post ID>]
*/
class MarkupPlugin
{
    /**
     * Replace Alternative Syntax By Shortcodes
     *
     * @param string $text Input Text Value
     *
     * @return string Output Text Value
     */
    public function normalizeSyntax(string $text = '') : string
    {
        $text = str_replace('&gt;', '>', $text);
        $text = preg_replace('/~~(.*?)~~/su', '[s]$1[/s]', $text);
        $text = preg_replace('/DEL(.*?)DEL/su', '[s]$1[/s]', $text);
        $text = preg_replace('/\*\*(.*?)\*\*/su', '[b]$1[/b]', $text);
        $text = preg_replace('/\*(.*?)\*/su', '[i]$1[/i]', $text);
        $text = preg_replace(
            '/\%\%(.*?)\%\%/su',
            '[spoiler]$1[/spoiler]',
            $text
        );
        $text = preg_replace(
            '/\%\%(.*?)\%\%/su',
            '[spoiler]$1[/spoiler]',
            $text
        );
        $text = preg_replace('/\>\>([0-9]+)/su', "[Reply:$1]", $text);
        $text = preg_replace('/\>(.*?)\n/su', "\n[q]$1[/q]\n", $text);
        $text = preg_replace('/\>(.*?)$/su', "\n[q]$1[/q]", $text);
        $text = preg_replace('/\[q\]/su', "[q]&gt;", $text);
        $text = preg_replace('/\[q\]\&gt\;([\s]+)/su', "[q]&gt;", $text);
        $text = preg_replace('/\[\/q\]([\s]+)\[q\]/su', "\n", $text);
        $text = preg_replace('/([\s]+)\[q\]/su', "\n[q]", $text);
        $text = preg_replace('/([\s]+)\[\/q\]/su', "[/q]\n", $text);
        $text = preg_replace('/\[\/q\]([\s]+)/su', "[/q]\n", $text);
        $text = str_replace('>', '&gt;', $text);

        return $text;
    }

    /**
     * Convert Shortcodes Into HTML Tags
     *
     * @param string $text Input Plain Text Value
     *
     * @return string Output HTML Text Value
     */
    public function markup2HTML(string $text = '') : string
    {
        $text = preg_replace(
            '/\[s\](.*?)\[\/s\]/su',
            '<strike>$1</strike>',
            $text
        );
        $text = preg_replace(
            '/\[b\](.*?)\[\/b\]/su',
            '<strong>$1</strong>',
            $text
        );
        $text = preg_replace('/\[i\](.*?)\[\/i\]/su', '<i>$1</i>', $text);
        $text = preg_replace(
            '/\[spoiler\](.*?)\[\/spoiler\]/su',
            '<span class="spoiler">$1</span>',
            $text
        );
        $text = preg_replace(
            '/\[u\](.*?)\[\/u\]/su',
            '<span class="utag">$1</span>',
            $text
        );
        $text = preg_replace('/([\s]+)\[q\]/su', '[q]', $text);
        $text = preg_replace('/\[q\]([\s]+)/su', '[q]', $text);
        $text = preg_replace('/([\s]+)\[\/q\]/su', '[/q]', $text);
        $text = preg_replace('/\[\/q\]([\s]+)/su', '[/q]', $text);
        $text = preg_replace('/\[q\]/su', '[q]<p>', $text);
        $text = preg_replace('/\[\/q\]/su', '</p>[/q]', $text);
        $text = preg_replace(
            '/\[q\](.*?)\[\/q\]/su',
            '<blockquote>$1</blockquote>',
            $text
        );
        $text = preg_replace('/\n+/su', '</p><p>', $text);

        return $text;
    }

    /**
     * Remove extra spaces
     *
     * @param string $text Input Text Value
     *
     * @return string Output Text Value
     */
    public function normalizeText(string $text = '') : string
    {
        $text = preg_replace('/\n+/su', "<br>", $text);
        $text = preg_replace('/\s+/su', ' ', $text);
        $text = preg_replace('/\<br\>\s/su', '<br>', $text);
        $text = preg_replace('/\s\<br\>/su', '<br>', $text);
        $text = preg_replace('/(^\s|\s$)/su', '', $text);
        $text = preg_replace('/(^\<br\>|\<br\>$)/su', '', $text);
        $text = preg_replace('/\<br\>/su', "\n", $text);
        $text = preg_replace('/\n+/su', "\n", $text);

        return $text;
    }

    /**
     * Convert Reply Shortcode Into HTML Tag
     *
     * @param string $text      Input Text Value
     * @param int    $sectionID ID Of Site Section
     *
     * @return string Output Text Value
     */
    public function parseReplyShortCode(
        string $text = '',
        int $sectionID = 0
    ) : string
    {
        $text = preg_replace(
            '/\[Reply\:([0-9]+)\]/su',
            '<a
                href="#"
                class="card_snippet_link card_snippet_link_without_level"
                data-id="$1"
                data-section="'.$sectionID.'">
                >>$1
            </a>',
            $text);

        return $text;
    }
}
?>