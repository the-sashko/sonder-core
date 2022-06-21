<?php

namespace Sonder\Plugins\Rss\Exceptions;

final class RssEntityException extends RssException
{
    final public const MESSAGE_ENTITY_VALUES_ARE_EMPTY = 'RSS Plugin Entity Values Are Empty';
    final public const MESSAGE_ENTITY_TITLE_IS_EMPTY = 'RSS Plugin Entity Channel Title Is Empty';
    final public const MESSAGE_ENTITY_LINK_IS_EMPTY = 'RSS Plugin Entity Channel Link Is Empty';
    final public const MESSAGE_ENTITY_IMAGE_IS_EMPTY = 'RSS Plugin Entity Channel Image Is Empty';
    final public const MESSAGE_ENTITY_DESCRIPTION_IS_EMPTY = 'RSS Plugin Entity Channel Description Is Empty';
    final public const MESSAGE_ENTITY_LINKS_ARE_EMPTY = 'RSS Plugin Entity Links Of Items Are Empty';
    final public const MESSAGE_ENTITY_XML_NOT_FOUND = 'Not Found RSS Plugin Entity XML Template';
}
