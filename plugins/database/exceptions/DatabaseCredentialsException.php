<?php
namespace Core\Plugins\Database\Exceptions;

class DatabaseCredentialsException extends DatabaseException
{
    const MESSAGE_CREDENTIALS_CONFIG_DATA_IS_EMPTY = 'Database Config Data '.
                                                     'Is Empty';

    const MESSAGE_CREDENTIALS_DB_TYPE_IS_NOT_SET = 'Database Type Is Not Set';

    const MESSAGE_CREDENTIALS_DB_NAME_IS_NOT_SET = 'Database Name Is Not Set';

    const MESSAGE_CREDENTIALS_DSN_IS_EMPTY = 'Database DSN Is Empty';
}
