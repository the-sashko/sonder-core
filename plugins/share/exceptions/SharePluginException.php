<?php

namespace Sonder\Plugins\Share\Exceptions;

final class SharePluginException extends ShareException
{
    const MESSAGE_PLATFORM_IS_NOT_SET = 'Share Platform Is Not Set';

    const MESSAGE_CREDENTIALS_IS_NOT_SET = 'Credentials Of Share Platform Is ' .
    'Not Set';

    const MESSAGE_MESSAGE_IS_NOT_SET = 'Share Message Is Not Set';

    const MESSAGE_PLATFORM_IS_NOT_EXISTS = 'Share Platform Is Not Exists';
}
