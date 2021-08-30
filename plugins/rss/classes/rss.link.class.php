<?php

namespace Core\Plugins\Rss\Classes;

use Core\Plugins\Rss\Exceptions\RssLinkException;
use Core\Plugins\Rss\Interfaces\IRssLink;

class RssLink implements IRssLink
{
    const VALUES_TITLE_KEY = 'title';

    const VALUES_LINK_KEY = 'link';

    const VALUES_DESCRIPTION_KEY = 'description';

    const XML_FILE_PATH = __DIR__ . '/../xml/link.xml';

    /**
     * @var string|null
     */
    private ?string $_title = null;

    /**
     * @var string|null
     */
    private ?string $_link = null;

    /**
     * @var string|null
     */
    private ?string $_description = null;

    /**
     * @param array|null $values
     *
     * @throws RssLinkException
     */
    public function __construct(?array $values = null)
    {
        if (empty($values)) {
            throw new RssLinkException(
                RssLinkException::MESSAGE_LINK_VALUES_ARE_EMPTY,
                RssLinkException::CODE_LINK_VALUES_ARE_EMPTY
            );
        }

        if (!array_key_exists(static::VALUES_TITLE_KEY, $values)) {
            throw new RssLinkException(
                RssLinkException::MESSAGE_LINK_TITLE_IS_EMPTY,
                RssLinkException::CODE_LINK_TITLE_IS_EMPTY
            );
        }

        if (!array_key_exists(static::VALUES_LINK_KEY, $values)) {
            throw new RssLinkException(
                RssLinkException::MESSAGE_LINK_LINK_IS_EMPTY,
                RssLinkException::CODE_LINK_LINK_IS_EMPTY
            );
        }

        if (!array_key_exists(static::VALUES_DESCRIPTION_KEY, $values)) {
            throw new RssLinkException(
                RssLinkException::MESSAGE_LINK_DESCRIPTION_IS_EMPTY,
                RssLinkException::CODE_LINK_DESCRIPTION_IS_EMPTY
            );
        }

        $this->_title = $values[static::VALUES_TITLE_KEY];
        $this->_link = $values[static::VALUES_LINK_KEY];
        $this->_description = $values[static::VALUES_DESCRIPTION_KEY];
    }

    /**
     * @return string|null
     *
     * @throws RssLinkException
     */
    public function getXML(): ?string
    {
        $title = $this->_getTitle();
        $link = $this->_getLink();
        $description = $this->_getDescription();

        if (empty($title) || empty($link) || empty($description)) {
            return null;
        }

        $xmlFilePath = static::XML_FILE_PATH;

        if (!file_exists($xmlFilePath) || !is_file($xmlFilePath)) {
            throw new RssLinkException(
                RssLinkException::MESSAGE_LINK_XML_NOT_FOUND,
                RssLinkException::CODE_LINK_XML_NOT_FOUND
            );
        }

        $xmlContent = file_get_contents($xmlFilePath);

        return sprintf($xmlContent, $title, $link, $description, $link);
    }

    /**
     * @return string|null
     */
    private function _getTitle(): ?string
    {
        return empty($this->_title) ? null : (string) $this->_title;
    }

    /**
     * @return string|null
     */
    private function _getLink(): ?string
    {
        return empty($this->_link) ? null : (string) $this->_link;
    }

    /**
     * @return string|null
     */
    private function _getDescription(): ?string
    {
        return empty($this->_description) ? null : (string) $this->_description;
    }
}
