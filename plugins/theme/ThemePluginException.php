<?php

namespace Sonder\Plugins\Theme;

use Exception;
use Throwable;

final class ThemePluginException extends Exception implements Throwable
{
    final public const MESSAGE_PLUGIN_THEME_PATH_NOT_SET_ERROR = 'Theme Plugin\'s Theme Path Is Not Set';
    final public const MESSAGE_PLUGIN_THEME_NOT_FOUND_ERROR = 'Theme Plugin\'s Theme Path Not Exists';
    final public const MESSAGE_PLUGIN_PUBLIC_DIR_NOT_FOUND_ERROR = 'Public Dir Not Exists';
    final public const MESSAGE_PLUGIN_META_FILE_NOT_FOUND_ERROR = 'Theme meta.json File Not Exists';
    final public const MESSAGE_PLUGIN_ASSETS_FILE_MISSING_ERROR = 'Theme Assets File "%s" Not Exists';
    final public const MESSAGE_PLUGIN_CLI_DIR_MISSING_ERROR = 'CLI Dir Is Not Found';
    
    final public const CODE_PLUGIN_THEME_PATH_NOT_SET_ERROR = 1001;
    final public const CODE_PLUGIN_THEME_NOT_FOUND_ERROR = 1002;
    final public const CODE_PLUGIN_PUBLIC_DIR_NOT_FOUND_ERROR = 1003;
    final public const CODE_PLUGIN_META_FILE_NOT_FOUND_ERROR = 1004;
    final public const CODE_PLUGIN_ASSETS_FILE_MISSING_ERROR = 1005;
    final public const CODE_PLUGIN_CLI_DIR_MISSING_ERROR = 1006;
}
