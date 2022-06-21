<?php

namespace Sonder\Plugins\Upload\Exceptions;

use Exception;
use Throwable;

class UploadException extends Exception implements Throwable
{
    final public const CODE_PLUGIN_FILE_HAS_BAD_FORMAT = 1001;

    final public const CODE_SETTINGS_DIR_PATH_IS_EMPTY = 2001;
    final public const CODE_SETTINGS_EXTENSIONS_IS_EMPTY = 2002;

    final public const CODE_FILE_NAME_IS_NOT_SET = 3001;
    final public const CODE_FILE_PATH_IS_NOT_SET = 3002;

    final public const CODE_SAVER_SETTINGS_ARE_NOT_SET = 4001;
    final public const CODE_SAVER_FILE_HAS_BAD_EXTENSION = 4003;
    final public const CODE_SAVER_FILE_TOO_LARGE = 4004;
    final public const CODE_SAVER_FILE_UPLOAD_ERROR = 4005;
    final public const CODE_SAVER_FILE_SAVE_ERROR = 4006;
}
