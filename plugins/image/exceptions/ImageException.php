<?php

namespace Sonder\Plugins\Image\Exceptions;

use Exception;
use Throwable;

class ImageException extends Exception implements Throwable
{
    final public const CODE_PLUGIN_FILE_NAME_IS_NOT_SET = 1001;
    final public const CODE_PLUGIN_FILE_PATH_IS_NOT_SET = 1002;
    final public const CODE_PLUGIN_DIR_PATH_IS_NOT_SET = 1003;
    final public const CODE_PLUGIN_FILE_PREFIX_IS_EMPTY = 1004;
    final public const CODE_PLUGIN_SIZE_OBJECT_IS_EMPTY = 1005;
    final public const CODE_PLUGIN_VALUES_HAS_BAD_FORMAT = 1006;
    final public const CODE_PLUGIN_ORIGINAL_HEIGHT_IS_EMPTY = 1007;
    final public const CODE_PLUGIN_ORIGINAL_WIDTH_IS_EMPTY = 1008;
    final public const CODE_PLUGIN_IMAGE_OBJECT_IS_EMPTY = 1009;
    final public const CODE_PLUGIN_FILE_ALREADY_EXISTS = 1010;

    final public const CODE_SIZE_VALUES_NOT_SET = 2001;
    final public const CODE_SIZE_VALUES_HAS_BAD_FORMAT = 2002;
    final public const CODE_SIZE_HEIGHT_VALUE_IS_EMPTY = 2003;
    final public const CODE_SIZE_WIDTH_VALUE_IS_EMPTY = 2004;
    final public const CODE_SIZE_FULL_PREFIX_NOT_ALLOWED = 2005;
}
