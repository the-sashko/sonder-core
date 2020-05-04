<?php
class LanguageException extends Exception {
    const INVALID_LANGUAGE_FORMAT   = 100;
    const LANGUAGE_IS_NOT_SUPPORTED = 101;
    const PO_FILE_IS_NOT_SET        = 102;
    const PO_FILE_IS_NOT_EXISTS     = 103;
    const MO_FILE_IS_NOT_SET        = 104;
    const MO_FILE_IS_NOT_EXISTS     = 105;
}
