<?php

namespace Core\Plugins\Rss\Interfaces;

use Core\Plugins\Rss\Exceptions\RssLinkException;

interface IRssLink
{
    /**
     * @return string|null
     *
     * @throws RssLinkException
     */
    public function getXML(): ?string;
}
