<?php

use function Sonder\Core\Utils\loadDirectory;

loadDirectory(__DIR__ . '/interfaces');
loadDirectory(__DIR__ . '/values_objects');
loadDirectory(__DIR__ . '/forms');

require_once __DIR__ . '/UserStore.php';
require_once __DIR__ . '/UserModel.php';
