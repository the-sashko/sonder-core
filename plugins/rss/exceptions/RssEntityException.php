<?php

namespace Core\Plugins\Rss\Exceptions;

class RssEntityException extends RssException
{
    const MESSAGE_ENTITY_VALUES_ARE_EMPTY = 'RSS Plugin Entity Values Are ' .
                                            'Empty';

    const MESSAGE_ENTITY_TITLE_IS_EMPTY = 'RSS Plugin Entity Channel Title ' .
                                          'Is Empty';

    const MESSAGE_ENTITY_LINK_IS_EMPTY = 'RSS Plugin Entity Channel Link Is ' .
                                         'Empty';

    const MESSAGE_ENTITY_DESCRIPTION_IS_EMPTY = 'RSS Plugin Entity Channel ' .
                                                'Description Is Empty';

    const MESSAGE_ENTITY_LINKS_ARE_EMPTY = 'RSS Plugin Entity Links Of Items ' .
                                          'Are Empty';

    const MESSAGE_ENTITY_XML_NOT_FOUND = 'Not Found RSS Plugin Entity XML ' .
                                         'Template';
}
