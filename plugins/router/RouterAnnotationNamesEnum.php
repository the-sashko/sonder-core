<?php

namespace Sonder\Plugins\Router;

use Sonder\Plugins\Router\Interfaces\IRouterAnnotationNamesEnum;

enum RouterAnnotationNamesEnum: string implements IRouterAnnotationNamesEnum
{
    case AREA = 'area';
    case ROUTE = 'route';
    case URL_PARAMS = 'url_params';
    case NO_CACHE = 'no_cache';
}
