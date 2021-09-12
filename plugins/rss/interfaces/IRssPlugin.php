<?php

namespace Core\Plugins\Rss\Interfaces;

use Core\Plugins\Rss\Exceptions\RssPluginException;

interface IRssPlugin
{
    /**
     * @param string|null $channelTitle
     * @param string|null $channelLink
     * @param string|null $channelImage
     * @param string|null $channelDescription
     * @param array|null $links
     *
     * @throws RssEntityException
     * @throws RssLinkException
     * @throws RssPluginException
     */
    public function load(
        ?string $channelTitle = null,
        ?string $channelLink = null,
        ?string $channelImage = null,
        ?string $channelDescription = null,
        ?array  $links = null
    ): void;

    /**
     * @param string|null $fileName
     *
     * @throws RssPluginException
     */
    public function save(?string $fileName = null): void;
}
