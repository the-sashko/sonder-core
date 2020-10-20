<?php

namespace Core\Plugins\Upload\Exceptions;

class UploadSettingsException extends UploadException
{
    const MESSAGE_SETTINGS_DIR_PATH_IS_EMPTY = 'Upload Plugin Setting Upload '.
                                               'Directory Value Is Not Set';

    const MESSAGE_SETTINGS_EXTENSIONS_IS_EMPTY = 'Upload Plugin Setting List '.
                                                 'Of Allowed Extensions Is '.
                                                 'Not Set';
}
