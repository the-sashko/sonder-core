<?php

namespace Sonder\Plugins\Share\Exceptions;

final class SharePlatformException extends ShareException
{
    const MESSAGE_MESSAGE_HAS_BAD_FORMAT = 'Share Message Has Bad Format';
    const MESSAGE_INVALID_CREDENTIALS = 'Share Credentials Has Bad Format';
    const MESSAGE_REMOTE_ERROR = 'Share Platform Remote Error';
}
