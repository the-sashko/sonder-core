<?php
class AppException extends Exception
{
    const CONTROLLER_IS_NOT_SET        = 101;
    const CONTROLLER_IS_NOT_EXIST      = 102;
    const CONTROLLER_ACTION_IS_NOT_SET = 103;
    const INVALID_CONTROLLER_ACTION    = 104;
}
