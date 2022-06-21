<?php

namespace Sonder\Plugins\Database\Exceptions;

final class DatabaseCredentialsException extends DatabaseException
{
    final public const MESSAGE_CREDENTIALS_CONFIG_DATA_IS_EMPTY = 'Database Config Data Is Empty';
    final public const MESSAGE_CREDENTIALS_DB_TYPE_IS_NOT_SET = 'Database Type Is Not Set';
    final public const MESSAGE_CREDENTIALS_DB_NAME_IS_NOT_SET = 'Database Name Is Not Set';
    final public const MESSAGE_CREDENTIALS_DSN_IS_EMPTY = 'Database DSN Is Empty';
}
