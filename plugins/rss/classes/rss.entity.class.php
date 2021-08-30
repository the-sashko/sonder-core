<?php

namespace Core\Plugins\Rss\Classes;

use Core\Plugins\Rss\Exceptions\RssEntityException;
use Core\Plugins\Rss\Exceptions\RssLinkException;
use Core\Plugins\Rss\Interfaces\IRssEntity;
use Core\Plugins\Rss\Interfaces\IRssLink;

class RssEntity implements IRssEntity
{
    const VALUES_CHANNEL_TITLE_KEY = 'channel_title';

    const VALUES_CHANNEL_LINK_KEY = 'channel_link';

    const VALUES_CHANNEL_DESCRIPTION_KEY = 'channel_description';

    const VALUES_LINKS_KEY = 'links';

    const XML_FILE_PATH = __DIR__ . '/../xml/entity.xml';

    /**
     * @var string|null
     */
    private ?string $_channel_title = null;

    /**
     * @var string|null
     */
    private ?string $_channel_link = null;

    /**
     * @var string|null
     */
    private ?string $_channel_description = null;

    /**
     * @var array|null
     */
    private ?array $_links = null;

    /**
     * @param array|null $values
     *
     * @throws RssEntityException
     * @throws RssLinkException
     */
    public function __construct(?array $values = null)
    {
        if (empty($values)) {
            throw new RssEntityException(
                RssEntityException::MESSAGE_ENTITY_VALUES_ARE_EMPTY,
                RssEntityException::CODE_ENTITY_VALUES_ARE_EMPTY
            );
        }

        if (!array_key_exists(static::VALUES_CHANNEL_TITLE_KEY, $values)) {
            throw new RssEntityException(
                RssEntityException::MESSAGE_ENTITY_TITLE_IS_EMPTY,
                RssEntityException::CODE_ENTITY_TITLE_IS_EMPTY
            );
        }

        if (!array_key_exists(static::VALUES_CHANNEL_LINK_KEY, $values)) {
            throw new RssEntityException(
                RssEntityException::MESSAGE_ENTITY_LINK_IS_EMPTY,
                RssEntityException::CODE_ENTITY_LINK_IS_EMPTY
            );
        }

        if (
            !array_key_exists(static::VALUES_CHANNEL_DESCRIPTION_KEY, $values)
        ) {
            throw new RssEntityException(
                RssEntityException::MESSAGE_ENTITY_DESCRIPTION_IS_EMPTY,
                RssEntityException::CODE_ENTITY_DESCRIPTION_IS_EMPTY
            );
        }

        if (!array_key_exists(static::VALUES_LINKS_KEY, $values)) {
            throw new RssEntityException(
                RssEntityException::MESSAGE_ENTITY_LINKS_ARE_EMPTY,
                RssEntityException::CODE_ENTITY_LINKS_ARE_EMPTY
            );
        }

        $this->_channel_title = $values[static::VALUES_CHANNEL_TITLE_KEY];

        $this->_channel_link = $values[static::VALUES_CHANNEL_LINK_KEY];

        $this->_channel_description = $values[
            static::VALUES_CHANNEL_DESCRIPTION_KEY
        ];

        $this->_links = $values[static::VALUES_LINKS_KEY];

        foreach ($this->_links as $linkKey => $link) {
            $this->_links[$linkKey] = new RssLink($link);
        }
    }

    /**
     * @return string|null
     *
     * @throws RssEntityException
     * @throws RssLinkException
     */
    public function getXML(): ?string
    {
        $channelTitle = $this->_getChannelTitle();
        $channelLink = $this->_getChannelLink();
        $channelDescription = $this->_getChannelDescription();

        if (
            empty($channelTitle) ||
            empty($channelLink) ||
            empty($channelDescription)
        ) {
            return null;
        }

        $linksXML = $this->_linksXML();

        if (empty($linksXML)) {
            return null;
        }

        $xmlFilePath = static::XML_FILE_PATH;

        if (!file_exists($xmlFilePath) || !is_file($xmlFilePath)) {
            throw new RssEntityException(
                RssEntityException::MESSAGE_ENTITY_XML_NOT_FOUND,
                RssEntityException::CODE_ENTITY_XML_NOT_FOUND
            );
        }

        $xmlContent = file_get_contents($xmlFilePath);

        return sprintf(
            $xmlContent,
            $channelTitle,
            $channelLink,
            $channelDescription,
            $linksXML
        );
    }

    /**
     * @return string|null
     */
    private function _getChannelTitle(): ?string
    {
        if (empty($this->_channel_title)) {
            return null;
        }

        return (string)$this->_channel_title;
    }

    /**
     * @return string|null
     */
    private function _getChannelLink(): ?string
    {
        if (empty($this->_channel_link)) {
            return null;
        }

        return (string)$this->_channel_link;
    }

    /**
     * @return string|null
     */
    private function _getChannelDescription(): ?string
    {
        if (empty($this->_channel_description)) {
            return null;
        }

        return (string)$this->_channel_description;
    }

    /**
     * @return IRssLink[]|null
     */
    private function _getLinks(): ?array
    {
        if (empty($this->_links)) {
            return null;
        }

        return (array)$this->_links;
    }

    /**
     * @return string|null
     *
     * @throws RssLinkException
     */
    private function _linksXML(): ?string
    {
        $links = $this->_getLinks();

        if (empty($links)) {
            return null;
        }

        $linksXML = '';

        foreach ($links as $link) {
            $linksXML = sprintf('%s%s', $linksXML, $link->getXML());
        }

        return $linksXML;
    }
}
