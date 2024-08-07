<?php

namespace Sonder\Plugins\Rss\Exceptions;

final class RssLinkException extends RssException
{
    final public const MESSAGE_LINK_VALUES_ARE_EMPTY = 'RSS Link Entity Values Are Empty';
    final public const MESSAGE_LINK_TITLE_IS_EMPTY = 'RSS Link Entity Title Is Empty';
    final public const MESSAGE_LINK_LINK_IS_EMPTY = 'RSS Link Entity Link Is Empty';
    final public const MESSAGE_LINK_TIMESTAMP_IS_EMPTY = 'RSS Link Entity Timestamp Is Empty';
    final public const MESSAGE_LINK_DESCRIPTION_IS_EMPTY = 'RSS Link Entity Description Is Empty';
    final public const MESSAGE_LINK_XML_NOT_FOUND = 'Not Found RSS Link Entity XML Template';
}
