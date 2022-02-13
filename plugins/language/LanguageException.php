<?php

namespace Sonder\Plugins\Language\Exceptions;

use Exception;

final class LanguageException extends Exception
{
    const INVALID_LANGUAGE_FORMAT = 1000;
    const LANGUAGE_IS_NOT_SUPPORTED = 1001;
    const PO_FILE_IS_NOT_SET = 1002;
    const PO_FILE_NOT_EXISTS = 1003;
    const MO_FILE_IS_NOT_SET = 1004;
    const CAN_NOT_PREPARE_DICTIONARY_FILE = 1004;
}
