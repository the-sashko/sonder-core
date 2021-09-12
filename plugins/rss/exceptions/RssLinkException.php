<?php

namespace Core\Plugins\Rss\Exceptions;

class RssLinkException extends RssException
{
    const MESSAGE_LINK_VALUES_ARE_EMPTY = 'RSS Link Entity Values Are Empty';

    const MESSAGE_LINK_TITLE_IS_EMPTY = 'RSS Link Entity Title Is Empty';

    const MESSAGE_LINK_LINK_IS_EMPTY = 'RSS Link Entity Link Is Empty';

    const MESSAGE_LINK_TIMESTAMP_IS_EMPTY = 'RSS Link Entity Timestamp Is ' .
    'Empty';

    const MESSAGE_LINK_DESCRIPTION_IS_EMPTY = 'RSS Link Entity Description ' .
    'Is Empty';

    const MESSAGE_LINK_XML_NOT_FOUND = 'Not Found RSS Link Entity XML Template';
}
