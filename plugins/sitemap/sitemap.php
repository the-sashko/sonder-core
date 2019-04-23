<?php
/**
 * Plugin For Generating Sitemaps From Links
 */
class SitemapPlugin
{
    /**
     * @var string Public Directory Path
     */
    const PUBLIC_DIR = __DIR__.'/../../../../public/';

    /**
     * @var string Sitemap Directory Path
     */
    const SITEMAP_DIR = __DIR__.'/../../../../public/xml/';

    public function __construct()
    {
        if (
            !file_exists(static::SITEMAP_DIR) ||
            !is_dir(static::SITEMAP_DIR)
        ) {
            mkdir(static::SITEMAP_DIR);
            chmod(static::SITEMAP_DIR, 0775);
        }

        foreach (scandir(static::SITEMAP_DIR) as $fileItem) {
            if (
                $fileItem!='.' &&
                $fileItem!='..' &&
                is_file(static::SITEMAP_DIR.$fileItem)
            ) {
                unlink(static::SITEMAP_DIR.$fileItem);
            }
        }

        if (
            file_exists(__DIR__.'/../../../../public/sitemap.xml') &&
            is_file(__DIR__.'/../../../../public/sitemap.xml')
        ) {
            unlink(__DIR__.'/../../../../public/sitemap.xml');
        }
    }

    /**
     * Create And Save Satemap From List Of Lnks
     *
     * @param string $sitemapName Name Of Sitemap
     * @param array  $links       List Of Site Links
     * @param string $frequency   Sitemap Frequency Param
     * @param float  $priority    Sitemap Priority Param
     */
    public function saveLinksToSitemap(
        string $sitemapName = '',
        array  $links       = [],
        string $frequency   = 'hourly',
        float  $priority    = 0.5
    ) : void
    {
        $sitemapXML = '<urlset>';

        foreach ($links as $link) {
            $sitemapDate = date('Y-m-d').'T'.date('H:i:s+00:00');
            $sitemapXML = "{$sitemapXML}
                <url>
                    <loc>{$link}</loc>
                    <changefreq>{$frequency}</changefreq>
                    <priority>{$priority}</priority>
                </url>
            ";
        }

        $sitemapXML = $sitemapXML.'</urlset>';

        $sitemapFilePath = $this->_getSitemapFilePath($sitemapName);

        file_put_contents($sitemapFilePath, $sitemapXML);
    }

    /**
     * Create And Save Satemap From List Of Sitemaps
     *
     * @param string $sitemapName Name Of Sitemap
     * @param array  $links       List Of Sitemaps
     */
    public function saveSummarySitemap(
        string $sitemapName = 'sitemap',
        array  $sitemaps    = []
    ) : void
    {
        $sitemapXML = '<sitemapindex>';

        foreach ($sitemaps as $sitemap) {
            $sitemapDate = date('Y-m-d').'T'.date('H:i:s+00:00');

            $sitemapXML = "{$sitemapXML}
                <sitemap>
                    <loc>/xml/{$sitemap}.xml</loc>
                    <lastmod>{$sitemapDate}</lastmod>
                </sitemap>
            ";
        }

        $sitemapXML = $sitemapXML.'</sitemapindex>';

        $sitemapFilePath = $this->_getSitemapFilePath($sitemapName);

        file_put_contents($sitemapFilePath, $sitemapXML);
    }

    /**
     * Get Sitemap File Path
     *
     * @param string $sitemapName Name Of Sitemap File
     *
     * @return string Sitemap File Path
     */
    private function _getSitemapFilePath(
        string $sitemapName = 'sitemap'
    ) : string
    {
        if ($sitemapName != 'sitemap') {
            return static::SITEMAP_DIR.$sitemapName.'.xml';
        }

        return static::PUBLIC_DIR.'/sitemap.xml';
    }
}
?>
