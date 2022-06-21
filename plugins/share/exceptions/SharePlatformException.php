<?php

namespace Sonder\Plugins\Share\Exceptions;

final class SharePlatformException extends ShareException
{
    final public const MESSAGE_MESSAGE_HAS_BAD_FORMAT = 'Share Message Has Bad Format';
    final public const MESSAGE_INVALID_CREDENTIALS = 'Share Credentials Has Bad Format';
    final public const MESSAGE_REMOTE_ERROR = 'Share Platform Remote Error';
}
