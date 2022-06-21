<?php

namespace Sonder\Plugins\Upload\Exceptions;

final class UploadFileException extends UploadException
{
    final public const MESSAGE_FILE_NAME_IS_NOT_SET = 'Upload File Name Is Not Set';
    final public const MESSAGE_FILE_PATH_IS_NOT_SET = 'Upload File Path Is Not Set';
}
