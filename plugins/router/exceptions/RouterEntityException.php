<?php
namespace Core\Plugins\Router\Exceptions;

class RouterEntityException extends RouterException
{
    const MESSAGE_ENTITY_ROUTE_IS_NOT_SET = 'Roter Plugin Entity Route Is '.
                                            'Not Set';

    const MESSAGE_ENTITY_CONTROLLER_IS_NOT_SET = 'Roter Plugin Entity '.
                                                 'Controller Is Not Set';

    const MESSAGE_ENTITY_METHOD_IS_NOT_SET = 'Roter Plugin Entity Method Is '.
                                             'Not Set';
}
