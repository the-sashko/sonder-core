<?php

namespace Core\Plugins\Rss\Exceptions;

class RssPluginException extends RssException
{
    const MESSAGE_PLUGIN_TITLE_IS_EMPTY = 'RSS Channel Title Is Empty';

    const MESSAGE_PLUGIN_LINK_IS_EMPTY = 'RSS Channel Link Is Empty';

    const MESSAGE_PLUGIN_IMAGE_IS_EMPTY = 'RSS Channel Image Is Empty';

    const MESSAGE_PLUGIN_DESCRIPTION_IS_EMPTY = 'RSS Channel Description Is ' .
    'Empty';

    const MESSAGE_PLUGIN_LINKS_ARE_EMPTY = 'RSS Channel Links Of Items Are ' .
    'Empty';

    const MESSAGE_PLUGIN_ENTITY_IS_NOT_SET = 'RSS Plugin Entity Is Not Set';

    const MESSAGE_PLUGIN_CAN_NOT_SAVE_FILE = 'Can Not Save RSS Values To File';
}
