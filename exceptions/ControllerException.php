<?php

namespace Sonder\Exceptions;

use Attribute;
use Sonder\Core\Interfaces\ICoreException;

#[AppException]
#[ICoreException]
#[Attribute(Attribute::TARGET_CLASS)]
final class ControllerException extends AppException
{
    final public const MESSAGE_CONTROLLER_REQUEST_IS_EMPTY = 'Request Object Is Empty';
    final public const MESSAGE_CONTROLLER_RESPONSE_IS_EMPTY = 'Request Object Is Empty';
    final public const MESSAGE_CONTROLLER_VIEW_PAGE_IS_NOT_SET = 'View Page Is Not Set';
    final public const MESSAGE_CONTROLLER_FRONTEND_THEME_IS_NOT_SET = 'Frontend Theme Is Not Set';
}
