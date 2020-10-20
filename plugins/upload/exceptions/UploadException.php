<?php
namespace Core\Plugins\Upload\Exceptions;

class UploadException extends \Exception
{
    const CODE_PLUGIN_SETTINGS_ARE_NOT_SET   = 1001;
    const CODE_PLUGIN_FILE_HAS_BAD_FORMAT    = 1002;
    const CODE_PLUGIN_FILE_HAS_BAD_EXTENSION = 1003;
    const CODE_PLUGIN_FILE_TOO_LARGE         = 1004;
    const CODE_PLUGIN_FILE_UPLOAD_ERROR      = 1005;
    const CODE_PLUGIN_FILE_SAVE_ERROR        = 1006;

    const CODE_SETTINGS_DIR_PATH_IS_EMPTY    = 2001;
    const CODE_SETTINGS_EXTENSIONS_IS_EMPTY  = 2002;

    const CODE_FILE_NAME_IS_NOT_SET          = 3001;
    const CODE_FILE_PATH_IS_NOT_SET          = 3002;
}
