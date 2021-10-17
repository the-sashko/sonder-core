<?php

namespace Sonder\Plugins\Rss\Interfaces;

interface IRssPlugin
{
    /**
     * @param string|null $channelTitle
     * @param string|null $channelLink
     * @param string|null $channelImage
     * @param string|null $channelDescription
     * @param array|null $links
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
     */
    public function save(?string $fileName = null): void;
}
