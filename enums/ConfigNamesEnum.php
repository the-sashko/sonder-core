<?php

namespace Sonder\Enums;

use Sonder\Core\Interfaces\ICoreEnum;
use Sonder\Interfaces\IConfigNamesEnum;

#[ICoreEnum]
#[IConfigNamesEnum]
enum ConfigNamesEnum: string implements IConfigNamesEnum
{
    case CRYPT = 'crypt';
    case DATABASE = 'database';
    case HOOKS = 'hooks';
    case LOCALE = 'locale';
    case MAIN = 'main';
    case PAGES = 'pages';
    case PUSH = 'push';
    case REDIS = 'redis';
    case SEO = 'seo';
    case SHARE = 'share';
    case SMS = 'sms';
}
