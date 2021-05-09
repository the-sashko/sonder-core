<?php
namespace Core\Plugins\Router\Exceptions;

class RouterPluginException extends RouterException
{
    const MESSAGE_PLUGIN_URL_IS_NOT_SET = 'Router Plugin URL Is Not Set';

    const MESSAGE_PLUGIN_ANNOTATION_IS_NOT_SET = 'Annotation Plugin Is Not '.
                                                 'Set';

    const MESSAGE_PLUGIN_CACHE_IS_NOT_SET = 'Router Plugin Cache Is Not Set';

    const MESSAGE_PLUGIN_CONTROLLER_IS_NOT_SET = 'Router Plugin Controller '.
                                                 'Class Is Not Set';
}
