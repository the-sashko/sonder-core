<?php

namespace Core\Plugins\Upload\Exceptions;

class UploadFileException extends UploadException
{
    const MESSAGE_FILE_NAME_IS_NOT_SET = 'Upload File Name Is Not Set';

    const MESSAGE_FILE_PATH_IS_NOT_SET = 'Upload File Path Is Not Set';
}
