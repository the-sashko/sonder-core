<?php
namespace Core\Plugins\Annotation\Exceptions;

class AnnotationPluginException extends AnnotationException
{
    const MESSAGE_PLUGIN_CLASS_IS_EMPTY = 'Annotation Plugin Class Name Is '.
                                          'Not Set';

    const MESSAGE_PLUGIN_METHOD_NAME_IS_EMPTY = 'Annotation Plugin Method Is '.
                                                'Not Set';

    const MESSAGE_PLUGIN_ANNOTATION_IS_EMPTY = 'Annotation Plugin Annotation '.
                                               'Name Is Not Set';
}
