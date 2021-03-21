<?php
/**
 * Plugin For Generating Sitemaps From Links
 */
class SitemapPlugin
{
    /**
     * @var string Public Directory Path
     */
    const PUBLIC_DIR_PATH = __DIR__.'/../../../../public/';

    /**
     * @var string Default Name Of Sitemap File
     */
    const DEFAULT_FILE_NAME = 'sitemap';

    /**
     * @var string Default Frequency Value Of Sitemap Entry
     */
    const DEFAULT_FREQUENCY = 'hourly';

    /**
     * @var string Default Sitemap Host
     */
    const DEFAULT_HOST = 'http://localhost';

    /**
     * @var float Default Priority Value Of Sitemap Entry
     */
    const DEFAULT_PRIORITY = 0.5;

    /**
     * @var string Template Of Sitemap Index File
     */
    const SITEMAP_INDEX_TEMPLATE = '<?xml version="1.0" encoding="UTF-8"?>'.
                                   "\n".'<sitemapindex xmlns="'.
                                   'http://www.sitemaps.org/schemas/sitemap/'.
                                   '0.9">%s'."\n".'</sitemapindex>';

    /**
     * @var string Template Of Entry In Sitemap Index File
     */
    const SITEMAP_ITEM_TEMPLATE = '%s'."\n".
                                  '    <sitemap>'."\n".
                                  '        <loc>%s/xml/%s.xml</loc>'."\n".
                                  '        <lastmod>%s</lastmod>'."\n".
                                  '    </sitemap>';

    /**
     * @var string Template Of Sitemap File
     */
    const SITEMAP_URLS_TEMPLATE = '<?xml version="1.0" encoding="UTF-8"?>'.
                                  "\n".'<urlset xmlns="'.
                                  'http://www.sitemaps.org/schemas/sitemap/'.
                                  '0.9">%s'."\n".'</urlset>';

    /**
     * @var string Template Of Entry In Sitemap File
     */
    const SITEMAP_URL_TEMPLATE = '%s'."\n".
                                 '    <url>'."\n".
                                 '        <loc>%s</loc>'."\n".
                                 '        <changefreq>%s</changefreq>'."\n".
                                 '        <priority>%s</priority>'."\n".
                                 '    </url>';

    public function __construct()
    {
        $mainSitemapFilePath = sprintf(
            '%s/sitemap.xml',
            static::PUBLIC_DIR_PATH
        );

        if (
            file_exists($mainSitemapFilePath) &&
            is_file($mainSitemapFilePath)
        ) {
            unlink($mainSitemapFilePath);
        }

        $sitemapDirPath = sprintf('%s/xml', static::PUBLIC_DIR_PATH);

        if (!file_exists($sitemapDirPath) || !is_dir($sitemapDirPath)) {
            mkdir($sitemapDirPath, 0775, true);
        }

        foreach (scandir($sitemapDirPath) as $fileItem) {
            $sitemapFilePath = sprintf('%s/%s', $sitemapDirPath, $fileItem);

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
     * Create And Save Satemap From List Of Lnks
     *
     * @param string|null $fileName  Name Of Sitemap File
     * @param array|null  $links     List Of Site Links
     * @param string|null $frequency Sitemap Frequency Param
     * @param float|null  $priority  Sitemap Priority Param
     */
    public function saveLinksToSitemap(
        ?string $fileName  = null,
        ?array  $links     = null,
        ?string $frequency = null,
        ?float  $priority  = null
    ): void
    {
        if (empty($fileName)) {
            $fileName = static::DEFAULT_FILE_NAME;
        }

        if (empty($frequency)) {
            $frequency = static::DEFAULT_FREQUENCY;
        }

        if (empty($priority)) {
            $priority = static::DEFAULT_PRIORITY;
        }

        $sitemapXml = '';

        foreach ((array) $links as $link) {
            $sitemapXml = sprintf(
                static::SITEMAP_URL_TEMPLATE,
                $sitemapXml,
                $link,
                $frequency,
                $priority
            );
        }

        $sitemapXml      = sprintf(static::SITEMAP_URLS_TEMPLATE, $sitemapXml);
        $sitemapFilePath = $this->_getSitemapFilePath($fileName);

        file_put_contents($sitemapFilePath, $sitemapXml);
    }

    /**
     * Create And Save Satemap From List Of Sitemaps
     *
     * @param string|null $fileName Name Of Sitemap File
     * @param array|null  $sitemaps List Of Sitemaps
     * @param string|null $host     Current Host
     */
    public function saveSummarySitemap(
        ?string $fileName = null,
        ?array  $sitemaps = null,
        ?string $host     = null
    ): void
    {
        if (empty($fileName)) {
            $fileName = static::DEFAULT_FILE_NAME;
        }

        if (empty($host)) {
            $host = static::DEFAULT_HOST;
        }

        $sitemapXml = '';

        foreach ((array) $sitemaps as $sitemap) {
            $date = sprintf('%sT%s', date('Y-m-d'), date('H:i:s+00:00'));

            $sitemapXml = sprintf(
                static::SITEMAP_ITEM_TEMPLATE,
                $sitemapXml,
                $host,
                $sitemap,
                $date
            );
        }

        $sitemapXml = sprintf(static::SITEMAP_INDEX_TEMPLATE, $sitemapXml);

        $sitemapFilePath = $this->_getSitemapFilePath($fileName);

        file_put_contents($sitemapFilePath, $sitemapXml);
    }

    /**
     * Get Sitemap File Path
     *
     * @param string|null $fileName Name Of Sitemap File
     *
     * @return string Sitemap File Path
     */
    private function _getSitemapFilePath(?string $fileName = null): string
    {
        if (empty($fileName)) {
            $fileName = static::DEFAULT_FILE_NAME;
        }

        if ('sitemap' !== $fileName) {
            return sprintf(
                '%s/xml/%s.xml',
                static::PUBLIC_DIR_PATH,
                $fileName
            );
        }

        return sprintf('%s/sitemap.xml', static::PUBLIC_DIR_PATH);
    }
}
