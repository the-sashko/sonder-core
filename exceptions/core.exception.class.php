<?php
class CoreException extends \Exception
{
    const CODE_CORE_ROUTER_FILE_NOT_FOUND        = 1001;
    const CODE_CORE_CONTROLLER_IS_NOT_SET        = 1002;
    const CODE_CORE_CONTROLLER_IS_NOT_EXIST      = 1003;
    const CODE_CORE_MODEL_IS_NOT_SET             = 1004;
    const CODE_CORE_MODEL_NOT_FOUND              = 1005;
    const CODE_CORE_ACTION_CONTROLLER_IS_NOT_SET = 1006;
    const CODE_CORE_INVALID_ACTION_CONTROLLER    = 1007;
    const CODE_CORE_ACTION_MODEL_IS_NOT_SET      = 1008;
    const CODE_CORE_MODEL_API_IS_NOT_SET         = 1009;
    const CODE_CORE_INVALID_API_ACTION_MODEL     = 1010;
    const CODE_CORE_PLUGIN_IS_NOT_EXISTS         = 1011;
    const CODE_CORE_CONFIG_IS_NOT_EXISTS         = 1012;

    const MESSAGE_CORE_ROUTER_FILE_NOT_FOUND = 'Router File Not Found';

    const MESSAGE_CORE_CONTROLLER_IS_NOT_SET = 'Controller Is Not Set';

    const MESSAGE_CORE_CONTROLLER_IS_NOT_EXIST = 'Controller Is Not Exist';

    const MESSAGE_CORE_MODEL_IS_NOT_SET = 'Model Is Not Set';

    const MESSAGE_CORE_MODEL_NOT_FOUND = 'Model Not Found';

    const MESSAGE_CORE_ACTION_CONTROLLER_IS_NOT_SET = 'Action Controller Is '.
                                                     'Not Set';

    const MESSAGE_CORE_INVALID_ACTION_CONTROLLER =' Invalid Action Of '.
                                                   'Controller';

    const MESSAGE_CORE_ACTION_MODEL_IS_NOT_SET = 'Action Model Is Not Set';

    const MESSAGE_CORE_MODEL_API_IS_NOT_SET = 'Model API Object Is Not Set';

    const MESSAGE_CORE_INVALID_API_ACTION_MODEL = 'Invalid API Action Of '.
                                                  'Model';

    const MESSAGE_CORE_PLUGIN_IS_NOT_EXISTS = 'Plugin Is Not Exists';

    const MESSAGE_CORE_CONFIG_IS_NOT_EXISTS = 'Config Is Not Exists';
}
