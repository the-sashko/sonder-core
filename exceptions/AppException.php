<?php

namespace Sonder\Exceptions;

use Attribute;
use Sonder\Core\Interfaces\ICoreException;

#[ICoreException]
#[Attribute(Attribute::TARGET_CLASS)]
class AppException extends BaseException implements ICoreException
{
    final public const CODE_CORE_MODEL_NOT_EXISTS = 1001;
    final public const CODE_CORE_PLUGIN_NOT_EXISTS = 1002;

    final public const CODE_ENDPOINT_RESPONSE_IS_EMPTY = 2001;
    final public const CODE_ENDPOINT_CONTROLLER_IS_NOT_SET = 2002;
    final public const CODE_ENDPOINT_INVALID_CONTROLLER_METHOD = 2003;

    final public const CODE_MIDDLEWARE_CONTROLLER_IS_NOT_SET = 3001;
    final public const CODE_MIDDLEWARE_METHOD_IS_NOT_SET = 3002;
    final public const CODE_MIDDLEWARE_CSRF_RUNNING_BEFORE_SESSION = 3003;
    final public const CODE_MIDDLEWARE_USER_RUNNING_BEFORE_SESSION = 3004;
    final public const CODE_MIDDLEWARE_ROUTING_TYPE_IS_NOT_SUPPORTED = 3005;

    final public const CODE_HOOK_VALUE_NOT_EXISTS = 4001;

    final public const CODE_API_URL_HAS_BAD_FORMAT = 5001;
    final public const CODE_API_CAN_NOT_RETRIEVE_MODEL = 5003;
    final public const CODE_API_NOT_SUPPORTED_API_CALLS = 5004;
    final public const CODE_API_INVALID_CRUD_ACTION = 5005;
    final public const CODE_API_INVALID_HTTP_METHOD = 5006;
    final public const CODE_API_METHOD_IS_NOT_SUPPORTED = 5007;

    final public const CODE_VALUES_OBJECT_EMPTY_VALUE_NAME = 6001;
    final public const CODE_VALUES_OBJECT_VALUE_NOT_FOUND = 6002;

    final public const CODE_REQUEST_HOST_NOT_SET = 7001;
    final public const CODE_REQUEST_UNSUPPORTED_HTTP_METHOD = 7002;

    final public const CODE_CACHE_CAN_NOT_SAVE_VALUES = 8001;

    final public const CODE_CONFIG_INVALID_CONFIG_NAME = 9001;
    final public const CODE_CONFIG_VALUE_NAME_IS_NOT_SET = 9002;
    final public const CODE_CONFIG_NOT_EXISTS = 9003;
    final public const CODE_CONFIG_CONFIG_IS_EMPTY = 9004;
    final public const CODE_CONFIG_CONFIG_FILE_HAS_BAD_FORMAT = 9005;
    final public const CODE_CONFIG_CONFIG_HAS_NOT_VALUE = 9006;

    final public const CODE_CONTROLLER_REQUEST_IS_EMPTY = 10001;
    final public const CODE_CONTROLLER_RESPONSE_IS_EMPTY = 10002;
    final public const CODE_CONTROLLER_VIEW_PAGE_IS_NOT_SET = 10003;
    final public const CODE_CONTROLLER_FRONTEND_THEME_IS_NOT_SET = 10004;

    final public const CODE_MODEL_VALUES_OBJECT_CLASS_NOT_EXISTS = 11001;
    final public const CODE_MODEL_SIMPLE_VALUES_OBJECT_CLASS_NOT_EXISTS = 11002;
}
