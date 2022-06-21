<?php

namespace Sonder\Plugins\Rss\Exceptions;

final class RssPluginException extends RssException
{
    final public const MESSAGE_PLUGIN_TITLE_IS_EMPTY = 'RSS Channel Title Is Empty';
    final public const MESSAGE_PLUGIN_LINK_IS_EMPTY = 'RSS Channel Link Is Empty';
    final public const MESSAGE_PLUGIN_IMAGE_IS_EMPTY = 'RSS Channel Image Is Empty';
    final public const MESSAGE_PLUGIN_DESCRIPTION_IS_EMPTY = 'RSS Channel Description Is Empty';
    final public const MESSAGE_PLUGIN_LINKS_ARE_EMPTY = 'RSS Channel Links Of Items Are Empty';
    final public const MESSAGE_PLUGIN_ENTITY_IS_NOT_SET = 'RSS Plugin Entity Is Not Set';
    final public const MESSAGE_PLUGIN_CAN_NOT_SAVE_FILE = 'Can Not Save RSS Values To File';
}
