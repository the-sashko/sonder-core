<?php

namespace Sonder\Plugins\Upload\Interfaces;

interface IUploadSaver
{
    /**
     * @param array $groupFiles
     *
     * @return array
     */
    public function saveByGroup(array $groupFiles = []): array;
}
