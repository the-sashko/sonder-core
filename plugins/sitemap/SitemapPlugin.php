<?php

namespace Sonder\Plugins;

final class SitemapPlugin
{
    private const PUBLIC_DIR_PATH = __DIR__ . '/../../../../public/';

    private const DEFAULT_FILE_NAME = 'sitemap';

    private const DEFAULT_FREQUENCY = 'hourly';

    private const DEFAULT_HOST = 'http://localhost';

    private const DEFAULT_PRIORITY = 0.5;

    private const SITEMAP_INDEX_TEMPLATE = '<?xml version="1.0" encoding="UTF-8"?>' .
    "\n" . '<sitemapindex xmlns="' .
    'https://www.sitemaps.org/schemas/sitemap/' .
    '0.9">%s' . "\n" . '</sitemapindex>';

    private const SITEMAP_ITEM_TEMPLATE = '%s' . "\n" .
    '    <sitemap>' . "\n" .
    '        <loc>%s/xml/%s.xml</loc>' . "\n" .
    '        <lastmod>%s</lastmod>' . "\n" .
    '    </sitemap>';

    private const SITEMAP_URLS_TEMPLATE = '<?xml version="1.0" encoding="UTF-8"?>' .
    "\n" . '<urlset xmlns="' .
    'https://www.sitemaps.org/schemas/sitemap/' .
    '0.9">%s' . "\n" . '</urlset>';

    private const SITEMAP_URL_TEMPLATE = '%s' . "\n" .
    '    <url>' . "\n" .
    '        <loc>%s</loc>' . "\n" .
    '        <changefreq>%s</changefreq>' . "\n" .
    '        <priority>%s</priority>' . "\n" .
    '    </url>';

    final public function __construct()
    {
        $publicDirPath = $this->_getPublicDirPath();

        $mainSitemapFilePath = sprintf('%s/sitemap.xml', $publicDirPath);

        if (
            file_exists($mainSitemapFilePath) &&
            is_file($mainSitemapFilePath)
        ) {
            unlink($mainSitemapFilePath);
        }

        $sitemapDirPath = sprintf('%s/xml', $publicDirPath);

        if (!file_exists($sitemapDirPath) || !is_dir($sitemapDirPath)) {
            mkdir($sitemapDirPath, 0775, true);
        }

        foreach (scandir($sitemapDirPath) as $fileItem) {
            $sitemapFilePath = sprintf(
                '%s/%s',
                $sitemapDirPath,
                $fileItem
            );

            if (
                '.' !== $fileItem &&
                '..' !== $fileItem &&
                is_file($sitemapFilePath)
            ) {
                unlink($sitemapFilePath);
            }
        }
    }

    /**
     * @param string|null $fileName
     * @param array|null $links
     * @param string|null $frequency
     * @param float|null $priority
     */
    final public function saveLinksToSitemap(
        ?string $fileName = null,
        ?array  $links = null,
        ?string $frequency = null,
        ?float  $priority = null
    ): void
    {
        if (empty($fileName)) {
            $fileName = SitemapPlugin::DEFAULT_FILE_NAME;
        }

        if (empty($frequency)) {
            $frequency = SitemapPlugin::DEFAULT_FREQUENCY;
        }

        if (empty($priority)) {
            $priority = SitemapPlugin::DEFAULT_PRIORITY;
        }

        $sitemapXml = '';

        foreach ((array)$links as $link) {
            $sitemapXml = sprintf(
                SitemapPlugin::SITEMAP_URL_TEMPLATE,
                $sitemapXml,
                $link,
                $frequency,
                $priority
            );
        }

        $sitemapXml = sprintf(
            SitemapPlugin::SITEMAP_URLS_TEMPLATE,
            $sitemapXml
        );

        $sitemapFilePath = $this->_getSitemapFilePath($fileName);

        file_put_contents($sitemapFilePath, $sitemapXml);
    }

    /**
     * @param string|null $fileName
     * @param array|null $sitemaps
     * @param string|null $host
     */
    final public function saveSummarySitemap(
        ?string $fileName = null,
        ?array  $sitemaps = null,
        ?string $host = null
    ): void
    {
        if (empty($fileName)) {
            $fileName = SitemapPlugin::DEFAULT_FILE_NAME;
        }

        if (empty($host)) {
            $host = SitemapPlugin::DEFAULT_HOST;
        }

        $sitemapXml = '';

        foreach ((array)$sitemaps as $sitemap) {
            $date = sprintf(
                '%sT%s',
                date('Y-m-d'),
                date('H:i:s+00:00')
            );

            $sitemapXml = sprintf(
                SitemapPlugin::SITEMAP_ITEM_TEMPLATE,
                $sitemapXml,
                $host,
                $sitemap,
                $date
            );
        }

        $sitemapXml = sprintf(
            SitemapPlugin::SITEMAP_INDEX_TEMPLATE,
            $sitemapXml
        );

        $sitemapFilePath = $this->_getSitemapFilePath($fileName);

        file_put_contents($sitemapFilePath, $sitemapXml);
    }

    /**
     * @param string|null $fileName
     *
     * @return string
     */
    private function _getSitemapFilePath(?string $fileName = null): string
    {
        $publicDirPath = $this->_getPublicDirPath();

        if (empty($fileName)) {
            $fileName = $publicDirPath;
        }

        if ('sitemap' !== $fileName) {
            return sprintf('%s/xml/%s.xml', $publicDirPath, $fileName);
        }

        return sprintf('%s/sitemap.xml', $publicDirPath);
    }

    private function _getPublicDirPath(): string
    {
        if (defined('APP_PUBLIC_DIR_PATH')) {
            return APP_PUBLIC_DIR_PATH;
        }

        return SitemapPlugin::PUBLIC_DIR_PATH;
    }
}
