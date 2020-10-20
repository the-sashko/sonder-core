<?php
namespace Core\Plugins\Upload\Exceptions;

class UploadPluginException extends UploadException
{
    const MESSAGE_PLUGIN_SETTINGS_ARE_NOT_SET = 'Upload Plugin Settings Are '.
                                                'Not Set';

    const MESSAGE_PLUGIN_FILE_HAS_BAD_FORMAT = 'Upload File Has Bad Format';

    const MESSAGE_PLUGIN_FILE_HAS_BAD_EXTENSION = 'Upload File Has Bad '.
                                                  'Extension';

    const MESSAGE_PLUGIN_FILE_TOO_LARGE = 'Upload File Is Too Large';

    const MESSAGE_PLUGIN_FILE_UPLOAD_ERROR = 'File Uploading Error';

    const MESSAGE_PLUGIN_FILE_SAVE_ERROR = 'File Saving Error';
}
