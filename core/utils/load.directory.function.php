<?php

namespace Sonder\Core\Utils;

function loadDirectory(?string $directory = null): void
{
    $filePattern = sprintf('%s%s*', $directory, DIRECTORY_SEPARATOR);

    foreach ((array)glob($filePattern) as $fileItem) {
        if ($fileItem == __FILE__) {
            continue;
        }

        if (is_dir($fileItem)) {
            loadDirectory($fileItem);

            continue;
        }

        if (preg_match('/^(.*?)\.php$/sui', $fileItem)) {
            include_once $fileItem;
        }
    }
}
