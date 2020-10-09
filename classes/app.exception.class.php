<?php
class AppException extends Exception
{
    const CODE_APP_ROUTER_FILE_NOT_FOUND        = 1001;
    const CODE_APP_CONTROLLER_IS_NOT_SET        = 1002;
    const CODE_APP_CONTROLLER_IS_NOT_EXIST      = 1003;
    const CODE_APP_MODEL_IS_NOT_SET             = 1004;
    const CODE_APP_MODEL_NOT_FOUND              = 1005;
    const CODE_APP_ACTION_CONTROLLER_IS_NOT_SET = 1006;
    const CODE_APP_INVALID_ACTION_CONTROLLER    = 1007;
    const CODE_APP_ACTION_MODEL_IS_NOT_SET      = 1008;
    const CODE_APP_MODEL_API_IS_NOT_SET         = 1009;
    const CODE_APP_INVALID_API_ACTION_MODEL     = 1010;

    const MESSAGE_APP_ROUTER_FILE_NOT_FOUND = 'Router File Not Found';

    const MESSAGE_APP_CONTROLLER_IS_NOT_SET = 'Controller Is Not Set';

    const MESSAGE_APP_CONTROLLER_IS_NOT_EXIST = 'Controller Is Not Exist';

    const MESSAGE_APP_MODEL_IS_NOT_SET = 'Model Is Not Set';

    const MESSAGE_APP_MODEL_NOT_FOUND = 'Model Not Found';

    const MESSAGE_APP_ACTION_CONTROLLER_IS_NOT_SET = 'Action Controller Is '.
                                                     'Not Set';

    const MESSAGE_APP_INVALID_ACTION_CONTROLLER =' Invalid Action Of '.
                                                   'Controller';

    const MESSAGE_APP_ACTION_MODEL_IS_NOT_SET = 'Action Model Is Not Set';

    const MESSAGE_APP_MODEL_API_IS_NOT_SET = 'Model API Object Is Not Set';

    const MESSAGE_APP_INVALID_API_ACTION_MODEL = 'Invalid API Action Of Model';
}
