<?php
/**
 * Plugin For Generating Sitemaps From Links
 */
class SitemapPlugin
{
    const PUBLIC_DIR = __DIR__.'/../../../../public/';
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
     * Get Translation String From Dictionary
     *
     * @param string $word Input String Value
     * @param string $word Input String Value
     * @param string $word Input String Value
     * @param string $word Input String Value
     */
    public function saveLinksToSitemap(
        string $sitemapName = '',
        array $links = [],
        string $frequency = 'hourly',
        float $priority = 0.5
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
     * Get Translation String From Dictionary
     *
     * @param string $word Input String Value
     *
     * @return string Translated String Value
     */
    public function saveSummarySitemap(
        string $sitemapName = 'sitemap',
        array $sitemaps = []
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
     * Get Translation String From Dictionary
     *
     * @param string $word Input String Value
     *
     * @return string Translated String Value
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