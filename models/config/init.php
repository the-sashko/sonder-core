<?php

use function Sonder\Core\Utils\loadDirectory;

loadDirectory(__DIR__ . '/interfaces');
loadDirectory(__DIR__ . '/enums');

require_once __DIR__ . '/exceptions/ConfigException.php';

loadDirectory(__DIR__ . '/exceptions');
loadDirectory(__DIR__ . '/vo');

require_once __DIR__ . '/ConfigModel.php';
