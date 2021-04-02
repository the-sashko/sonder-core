<?php
namespace Core\Plugins\Upload\Interfaces;

interface IUploadSaver
{
    public function saveByGroup(array $groupFiles = []): array;
}
