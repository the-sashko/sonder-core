<?php

namespace Sonder\Plugins\Rss\Interfaces;

interface IRssLink
{
    /**
     * @return string|null
     */
    public function getXml(): ?string;
}
