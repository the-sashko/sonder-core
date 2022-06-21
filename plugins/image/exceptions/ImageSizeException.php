<?php

namespace Sonder\Plugins\Image\Exceptions;

final class ImageSizeException extends ImageException
{
    final public const MESSAGE_SIZE_VALUES_NOT_SET = 'Image Plugin Size Values Are Not Set';
    final public const MESSAGE_SIZE_VALUES_HAS_BAD_FORMAT = 'Image Plugin Size Has Bad Format';
    final public const MESSAGE_SIZE_HEIGHT_VALUE_IS_EMPTY = 'Image Plugin Size Values Height Is Empty';
    final public const MESSAGE_SIZE_WIDTH_VALUE_IS_EMPTY = 'Image Plugin Size Values Width Is Empty';
    final public const MESSAGE_SIZE_FULL_PREFIX_NOT_ALLOWED = 'Image Plugin Size File Prefix "full" Is Not Allowed';
}
