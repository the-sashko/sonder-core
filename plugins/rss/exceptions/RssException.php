<?php

namespace Sonder\Plugins\Rss\Exceptions;

use Exception;
use Throwable;

class RssException extends Exception implements Throwable
{
    final public const CODE_PLUGIN_TITLE_IS_EMPTY = 1001;
    final public const CODE_PLUGIN_LINK_IS_EMPTY = 1002;
    final public const CODE_PLUGIN_IMAGE_IS_EMPTY = 1003;
    final public const CODE_PLUGIN_DESCRIPTION_IS_EMPTY = 1004;
    final public const CODE_PLUGIN_LINKS_ARE_EMPTY = 1005;
    final public const CODE_PLUGIN_ENTITY_IS_NOT_SET = 1006;
    final public const CODE_PLUGIN_CAN_NOT_SAVE_FILE = 1007;

    final public const CODE_ENTITY_VALUES_ARE_EMPTY = 2001;
    final public const CODE_ENTITY_TITLE_IS_EMPTY = 2002;
    final public const CODE_ENTITY_LINK_IS_EMPTY = 2003;
    final public const CODE_ENTITY_IMAGE_IS_EMPTY = 2004;
    final public const CODE_ENTITY_DESCRIPTION_IS_EMPTY = 2005;
    final public const CODE_ENTITY_LINKS_ARE_EMPTY = 2006;
    final public const CODE_ENTITY_XML_NOT_FOUND = 2007;

    final public const CODE_LINK_VALUES_ARE_EMPTY = 3001;
    final public const CODE_LINK_TITLE_IS_EMPTY = 3002;
    final public const CODE_LINK_LINK_IS_EMPTY = 3003;
    final public const CODE_LINK_TIMESTAMP_IS_EMPTY = 3004;
    final public const CODE_LINK_DESCRIPTION_IS_EMPTY = 3005;
    final public const CODE_LINK_XML_NOT_FOUND = 3006;
}
