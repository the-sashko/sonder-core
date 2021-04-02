<?php
namespace Core\Plugins\Upload\Exceptions;

class UploadException extends \Exception
{
    const CODE_PLUGIN_FILE_HAS_BAD_FORMAT   = 1001;

    const CODE_SETTINGS_DIR_PATH_IS_EMPTY   = 2001;
    const CODE_SETTINGS_EXTENSIONS_IS_EMPTY = 2002;

    const CODE_FILE_NAME_IS_NOT_SET         = 3001;
    const CODE_FILE_PATH_IS_NOT_SET         = 3002;

    const CODE_SAVER_SETTINGS_ARE_NOT_SET   = 4001;
    const CODE_SAVER_FILE_HAS_BAD_EXTENSION = 4003;
    const CODE_SAVER_FILE_TOO_LARGE         = 4004;
    const CODE_SAVER_FILE_UPLOAD_ERROR      = 4005;
    const CODE_SAVER_FILE_SAVE_ERROR        = 4006;
}
