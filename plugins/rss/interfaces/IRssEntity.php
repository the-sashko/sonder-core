<?php

namespace Core\Plugins\Rss\Interfaces;

use Core\Plugins\Rss\Exceptions\RssEntityException;

interface IRssEntity
{
    /**
     * @return string|null
     *
     * @throws RssEntityException
     */
    public function getXML(): ?string;
}
