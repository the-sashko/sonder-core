<?php

namespace Sonder\Plugins\Language;

use Exception;
use Throwable;

final class LanguageException extends Exception implements Throwable
{
    final public const INVALID_LANGUAGE_FORMAT = 1000;
    final public const LANGUAGE_IS_NOT_SUPPORTED = 1001;
    final public const PO_FILE_IS_NOT_SET = 1002;
    final public const PO_FILE_NOT_EXISTS = 1003;
    final public const MO_FILE_IS_NOT_SET = 1004;
    final public const CAN_NOT_PREPARE_DICTIONARY_FILE = 1004;
}
