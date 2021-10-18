<?php

namespace Sonder\Plugins\Rss\Classes;

use Sonder\Plugins\Rss\Exceptions\RssEntityException;
use Sonder\Plugins\Rss\Exceptions\RssException;
use Sonder\Plugins\Rss\Exceptions\RssLinkException;
use Sonder\Plugins\Rss\Interfaces\IRssEntity;

final class RssEntity implements IRssEntity
{
    const VALUES_CHANNEL_TITLE_KEY = 'channel_title';

    const VALUES_CHANNEL_LINK_KEY = 'channel_link';

    const VALUES_CHANNEL_IMAGE_KEY = 'channel_image';

    const VALUES_CHANNEL_DESCRIPTION_KEY = 'channel_description';

    const VALUES_LINKS_KEY = 'links';

    const XML_FILE_PATH = __DIR__ . '/../xml/entity.xml';

    /**
     * @var string|mixed|null
     */
    private ?string $_channel_title = null;

    /**
     * @var string|mixed|null
     */
    private ?string $_channel_link = null;

    /**
     * @var string|mixed|null
     */
    private ?string $_channel_image = null;

    /**
     * @var string|mixed|null
     */
    private ?string $_channel_description = null;

    /**
     * @var array|mixed|null
     */
    private ?array $_links = null;

    /**
     * @param array|null $values
     * @throws RssEntityException
     * @throws RssLinkException
     */
    final public function __construct(?array $values = null)
    {
        if (empty($values)) {
            throw new RssEntityException(
                RssEntityException::MESSAGE_ENTITY_VALUES_ARE_EMPTY,
                RssException::CODE_ENTITY_VALUES_ARE_EMPTY
            );
        }

        if (
            !array_key_exists(
                RssEntity::VALUES_CHANNEL_TITLE_KEY,
                $values
            )
        ) {
            throw new RssEntityException(
                RssEntityException::MESSAGE_ENTITY_TITLE_IS_EMPTY,
                RssException::CODE_ENTITY_TITLE_IS_EMPTY
            );
        }

        if (
            !array_key_exists(
                RssEntity::VALUES_CHANNEL_LINK_KEY,
                $values
            )
        ) {
            throw new RssEntityException(
                RssEntityException::MESSAGE_ENTITY_LINK_IS_EMPTY,
                RssException::CODE_ENTITY_LINK_IS_EMPTY
            );
        }

        if (
            !array_key_exists(
                RssEntity::VALUES_CHANNEL_IMAGE_KEY,
                $values
            )
        ) {
            throw new RssEntityException(
                RssEntityException::MESSAGE_ENTITY_IMAGE_IS_EMPTY,
                RssException::CODE_ENTITY_IMAGE_IS_EMPTY
            );
        }

        if (
            !array_key_exists(
                RssEntity::VALUES_CHANNEL_DESCRIPTION_KEY,
                $values
            )
        ) {
            throw new RssEntityException(
                RssEntityException::MESSAGE_ENTITY_DESCRIPTION_IS_EMPTY,
                RssException::CODE_ENTITY_DESCRIPTION_IS_EMPTY
            );
        }

        if (!array_key_exists(RssEntity::VALUES_LINKS_KEY, $values)) {
            throw new RssEntityException(
                RssEntityException::MESSAGE_ENTITY_LINKS_ARE_EMPTY,
                RssException::CODE_ENTITY_LINKS_ARE_EMPTY
            );
        }

        $this->_channel_title = $values[RssEntity::VALUES_CHANNEL_TITLE_KEY];

        $this->_channel_link = $values[RssEntity::VALUES_CHANNEL_LINK_KEY];

        $this->_channel_image = $values[RssEntity::VALUES_CHANNEL_IMAGE_KEY];

        $this->_channel_description = $values[RssEntity::VALUES_CHANNEL_DESCRIPTION_KEY];

        $this->_links = $values[RssEntity::VALUES_LINKS_KEY];

        foreach ($this->_links as $linkKey => $link) {
            $this->_links[$linkKey] = new RssLink($link);
        }
    }

    /**
     * @return string|null
     *
     * @throws RssEntityException
     */
    final public function getXml(): ?string
    {
        $channelTitle = $this->_getChannelTitle();
        $channelLink = $this->_getChannelLink();
        $channelImage = $this->_getChannelImage();
        $channelDescription = $this->_getChannelDescription();

        if (
            empty($channelTitle) ||
            empty($channelLink) ||
            empty($channelImage) ||
            empty($channelDescription)
        ) {
            return null;
        }

        $linksXML = $this->_linksXML();

        if (empty($linksXML)) {
            return null;
        }

        $xmlFilePath = RssEntity::XML_FILE_PATH;

        if (!file_exists($xmlFilePath) || !is_file($xmlFilePath)) {
            throw new RssEntityException(
                RssEntityException::MESSAGE_ENTITY_XML_NOT_FOUND,
                RssException::CODE_ENTITY_XML_NOT_FOUND
            );
        }

        $xmlContent = file_get_contents($xmlFilePath);

        return sprintf(
            $xmlContent,
            $channelTitle,
            $channelLink,
            $channelDescription,
            $channelImage,
            $channelTitle,
            $channelLink,
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
    private function _getChannelImage(): ?string
    {
        if (empty($this->_channel_image)) {
            return null;
        }

        return (string)$this->_channel_image;
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
     * @return array|null
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
