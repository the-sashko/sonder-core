<?php

namespace Sonder\Plugins\Theme;

use Exception;

final class ThemePluginException extends Exception
{
    const CODE_PLUGIN_THEME_PATH_NOT_SET_ERROR = 1001;
    const CODE_PLUGIN_THEME_NOT_FOUND_ERROR = 1002;
    const CODE_PLUGIN_PUBLIC_DIR_NOT_FOUND_ERROR = 1003;
    const CODE_PLUGIN_META_FILE_NOT_FOUND_ERROR = 1004;
    const CODE_PLUGIN_ASSETS_FILE_MISSING_ERROR = 1005;
    const CODE_PLUGIN_CLI_DIR_MISSING_ERROR = 1006;

    const MESSAGE_PLUGIN_THEME_PATH_NOT_SET_ERROR = 'Theme Plugin\'s Theme ' .
    'Path Is Not Set';

    const MESSAGE_PLUGIN_THEME_NOT_FOUND_ERROR = 'Theme Plugin\'s Theme ' .
    'Path Is Not Exists';

    const MESSAGE_PLUGIN_PUBLIC_DIR_NOT_FOUND_ERROR = 'Public Dir Is Not ' .
    'Exists';

    const MESSAGE_PLUGIN_META_FILE_NOT_FOUND_ERROR = 'Theme meta.json File ' .
    'Is Not Exists';

    const MESSAGE_PLUGIN_ASSETS_FILE_MISSING_ERROR = 'Theme Assets File "%s" ' .
    'Is Not Exists';

    const MESSAGE_PLUGIN_CLI_DIR_MISSING_ERROR = 'CLI Dir Is Not Found';
}
