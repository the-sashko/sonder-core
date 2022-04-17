<?php

namespace Sonder\Plugins\Upload\Exceptions;

final class UploadSaverException extends UploadException
{
    const MESSAGE_SAVER_SETTINGS_ARE_NOT_SET = 'Upload Plugin Settings Are ' .
    'Not Set';

    const MESSAGE_SAVER_FILE_HAS_BAD_EXTENSION = 'Upload File Has Bad ' .
    'Extension';

    const MESSAGE_SAVER_FILE_TOO_LARGE = 'Upload File Is Too Large';

    const MESSAGE_SAVER_FILE_UPLOAD_ERROR = 'File Uploading Error';

    const MESSAGE_SAVER_FILE_SAVE_ERROR = 'File Saving Error';
}
