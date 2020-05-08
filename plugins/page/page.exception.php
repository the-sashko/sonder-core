<?php
class PageException extends Exception
{
    const STATIC_PAGE_NAME_IS_NOT_SET           = 100;
    const TEMPLATE_FOR_STATIC_PAGE_IS_NOT_EXIST = 101;
    const STATIC_PAGE_FILE_HAS_BAD_FORMAT       = 102;
}
