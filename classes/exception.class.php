<?php
class CoreException extends \Exception
{
    const CODE_CORE_CONTROLLER_IS_NOT_SET        = 1001;
    const CODE_CORE_CONTROLLER_IS_NOT_EXIST      = 1002;
    const CODE_CORE_MODEL_IS_NOT_SET             = 1003;
    const CODE_CORE_MODEL_NOT_FOUND              = 1004;
    const CODE_CORE_ACTION_CONTROLLER_IS_NOT_SET = 1005;
    const CODE_CORE_INVALID_ACTION_CONTROLLER    = 1006;
    const CODE_CORE_ACTION_MODEL_IS_NOT_SET      = 1007;
    const CODE_CORE_MODEL_API_IS_NOT_SET         = 1008;
    const CODE_CORE_INVALID_API_ACTION_MODEL     = 1009;
    const CODE_CORE_PLUGIN_IS_NOT_SET            = 1010;
    const CODE_CORE_PLUGIN_IS_NOT_EXISTS         = 1011;
    const CODE_CORE_CONFIG_IS_NOT_EXISTS         = 1012;
    const CODE_CORE_HOOK_IS_NOT_EXISTS           = 1013;
    const CODE_CORE_HOOK_CLASS_IS_NOT_EXISTS     = 1014;
    const CODE_CORE_HOOK_METHOD_IS_NOT_EXISTS    = 1015;
    const CODE_CORE_PAGE_NOT_FOUND               = 1016;

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

    const MESSAGE_CORE_PLUGIN_IS_NOT_SET = 'Plugin Name Is Not Set';

    const MESSAGE_CORE_PLUGIN_IS_NOT_EXISTS = 'Plugin Is Not Exists';

    const MESSAGE_CORE_CONFIG_IS_NOT_EXISTS = 'Config Is Not Exists';

    const MESSAGE_CORE_HOOK_IS_NOT_EXISTS = 'Hook Is Not Exists';

    const MESSAGE_CORE_HOOK_CLASS_IS_NOT_EXISTS = 'Hook Class Is Not Exists';

    const MESSAGE_CORE_HOOK_METHOD_IS_NOT_EXISTS = 'Hook Method Is Not Exists';

    const MESSAGE_CORE_PAGE_NOT_FOUND = 'Page Not Found';
}
