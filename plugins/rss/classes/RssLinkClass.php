<?php

namespace Sonder\Plugins\Rss\Classes;

use DateTime;
use DateTimeInterface;
use Exception;
use Sonder\Plugins\Rss\Exceptions\RssException;
use Sonder\Plugins\Rss\Exceptions\RssLinkException;
use Sonder\Plugins\Rss\Interfaces\IRssLink;

final class RssLink implements IRssLink
{
    const VALUES_TITLE_KEY = 'title';

    const VALUES_LINK_KEY = 'link';

    const VALUES_TIMESTAMP_KEY = 'timestamp';

    const VALUES_DESCRIPTION_KEY = 'description';

    const XML_FILE_PATH = __DIR__ . '/../xml/link.xml';

    private ?string $_title = null;

    private ?string $_link = null;

    private ?int $_timestamp = null;

    private ?string $_description = null;

    /**
     * @param array|null $values
     *
     * @throws RssLinkException
     */
    final public function __construct(?array $values = null)
    {
        if (empty($values)) {
            throw new RssLinkException(
                RssLinkException::MESSAGE_LINK_VALUES_ARE_EMPTY,
                RssException::CODE_LINK_VALUES_ARE_EMPTY
            );
        }

        if (!array_key_exists(RssLink::VALUES_TITLE_KEY, $values)) {
            throw new RssLinkException(
                RssLinkException::MESSAGE_LINK_TITLE_IS_EMPTY,
                RssException::CODE_LINK_TITLE_IS_EMPTY
            );
        }

        if (!array_key_exists(RssLink::VALUES_LINK_KEY, $values)) {
            throw new RssLinkException(
                RssLinkException::MESSAGE_LINK_LINK_IS_EMPTY,
                RssException::CODE_LINK_LINK_IS_EMPTY
            );
        }

        if (!array_key_exists(RssLink::VALUES_TIMESTAMP_KEY, $values)) {
            throw new RssLinkException(
                RssLinkException::MESSAGE_LINK_TIMESTAMP_IS_EMPTY,
                RssException::CODE_LINK_TIMESTAMP_IS_EMPTY
            );
        }

        if (!array_key_exists(RssLink::VALUES_DESCRIPTION_KEY, $values)) {
            throw new RssLinkException(
                RssLinkException::MESSAGE_LINK_DESCRIPTION_IS_EMPTY,
                RssException::CODE_LINK_DESCRIPTION_IS_EMPTY
            );
        }

        $this->_title = $values[RssLink::VALUES_TITLE_KEY];
        $this->_link = $values[RssLink::VALUES_LINK_KEY];
        $this->_timestamp = $values[RssLink::VALUES_TIMESTAMP_KEY];
        $this->_description = $values[RssLink::VALUES_DESCRIPTION_KEY];
    }

    /**
     * @return string|null
     *
     * @throws RssLinkException
     *
     * @throws Exception
     */
    final public function getXml(): ?string
    {
        $title = $this->_getTitle();
        $link = $this->_getLink();
        $timestamp = $this->_getTimestamp();
        $description = $this->_getDescription();

        if (
            empty($title) ||
            empty($link) ||
            empty($timestamp) ||
            empty($description)
        ) {
            return null;
        }

        $pubDate = new DateTime(date("Y-m-d H:i:s", $timestamp));
        $pubDate = $pubDate->format(DateTimeInterface::RFC822);

        $xmlFilePath = RssLink::XML_FILE_PATH;

        if (!file_exists($xmlFilePath) || !is_file($xmlFilePath)) {
            throw new RssLinkException(
                RssLinkException::MESSAGE_LINK_XML_NOT_FOUND,
                RssException::CODE_LINK_XML_NOT_FOUND
            );
        }

        $xmlContent = file_get_contents($xmlFilePath);

        return sprintf(
            $xmlContent,
            $title,
            $link,
            $pubDate,
            $description,
            $link
        );
    }

    /**
     * @return string|null
     */
    private function _getTitle(): ?string
    {
        return empty($this->_title) ? null : (string)$this->_title;
    }

    /**
     * @return string|null
     */
    private function _getLink(): ?string
    {
        return empty($this->_link) ? null : (string)$this->_link;
    }

    /**
     * @return int|null
     */
    private function _getTimestamp(): ?int
    {
        return empty($this->_timestamp) ? null : (int)$this->_timestamp;
    }

    /**
     * @return string|null
     */
    private function _getDescription(): ?string
    {
        return empty($this->_description) ? null : (string)$this->_description;
    }
}
