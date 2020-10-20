<?php
namespace Core\Plugins\Image\Exceptions;

class ImagePluginException extends ImageException
{
    const MESSAGE_PLUGIN_FILE_NAME_IS_NOT_SET = 'Image Plugin File Name Is '.
                                                'Not Set';

    const MESSAGE_PLUGIN_FILE_PATH_IS_NOT_SET = 'Image Plugin File Path Is '.
                                                'Not Set';

    const MESSAGE_PLUGIN_DIR_PATH_IS_NOT_SET = 'Image Plugin Directory Path '.
                                               'Is Not Set';

    const MESSAGE_PLUGIN_FILE_PREFIX_IS_EMPTY = 'Image Plugin File Prefix Is '.
                                                'Not Set';

    const MESSAGE_PLUGIN_SIZE_OBJECT_IS_EMPTY = 'Image Plugin Size Object Is '.
                                                'Not Set';

    const MESSAGE_PLUGIN_ORIGINAL_HEIGHT_IS_EMPTY = 'Image Plugin Original '.
                                                    'Height Is Not Set';

    const MESSAGE_PLUGIN_ORIGINAL_WIDTH_IS_EMPTY = 'Image Plugin Original '.
                                                   'Width Is Not Set';

    const MESSAGE_PLUGIN_IMAGE_OBJECT_IS_EMPTY = 'Image Plugin Size Object '.
                                                 'Is Not Set';

    const MESSAGE_PLUGIN_FILE_ALREADY_EXISTS = 'File Already Exists';
}
