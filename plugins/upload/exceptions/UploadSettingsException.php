<?php

namespace Sonder\Plugins\Upload\Exceptions;

final class UploadSettingsException extends UploadException
{
    final public const MESSAGE_SETTINGS_DIR_PATH_IS_EMPTY = 'Upload Plugin Setting Upload Directory Value Is Not Set';
    final public const MESSAGE_SETTINGS_EXTENSIONS_IS_EMPTY = 'Upload Plugin Setting List Of Allowed Extensions Is Not Set';
}
