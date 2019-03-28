<?php
class BreadcrumbsPlugin
{
    const BREADCRUMBS_SEPARATOR = '»';

    public function getHTML(array $pagePath = []) : string
    {
        $separator = static::BREADCRUMBS_SEPARATOR;

        if (count($pagePath) > 0) {
            $html = '<a href="/">'._t('Main Page').'</a>';

            foreach ($pagePath as $uri => $title) {
                $html = "{$html}<span>{$separator}</span>";

                if ($uri != '#') {
                    $html = "{$html}<a href=\"{$uri}\">{$title}</a>";
                } else {
                    $html = "{$html}<span>{$title}</span>";
                }
            }
        } else {
            $html = '<span>'._t('Main Page').'</span>';
        }

        return "<nav class=\"breadcrumbs\">{$html}</nav>";
    }
}
?>