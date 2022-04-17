<?php

namespace Sonder\Plugins\Rss\Interfaces;

interface IRssEntity
{
    /**
     * @return string|null
     */
    public function getXml(): ?string;
}
