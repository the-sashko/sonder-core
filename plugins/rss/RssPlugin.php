<?php

namespace Sonder\Plugins;

use Sonder\Plugins\Rss\Classes\RssEntity;
use Sonder\Plugins\Rss\Exceptions\RssEntityException;
use Sonder\Plugins\Rss\Exceptions\RssException;
use Sonder\Plugins\Rss\Exceptions\RssLinkException;
use Sonder\Plugins\Rss\Exceptions\RssPluginException;
use Sonder\Plugins\Rss\Interfaces\IRssEntity;
use Sonder\Plugins\Rss\Interfaces\IRssPlugin;

final class RssPlugin implements IRssPlugin
{
    const DEFAULT_FILE_NAME = 'rss';

    const RSS_DIR_PATH = __DIR__ . '/../../../../public/xml';

    /**
     * @var IRssEntity|null
     */
    private ?IRssEntity $_entity = null;

    /**
     * @param string|null $channelTitle
     * @param string|null $channelLink
     * @param string|null $channelImage
     * @param string|null $channelDescription
     * @param array|null $links
     *
     * @throws RssEntityException
     * @throws RssPluginException
     * @throws RssLinkException
     */
    public function load(
        ?string $channelTitle = null,
        ?string $channelLink = null,
        ?string $channelImage = null,
        ?string $channelDescription = null,
        ?array  $links = null
    ): void
    {
        if (empty($channelTitle)) {
            throw new RssPluginException(
                RssPluginException::MESSAGE_PLUGIN_TITLE_IS_EMPTY,
                RssException::CODE_PLUGIN_TITLE_IS_EMPTY
            );
        }

        if (empty($channelLink)) {
            throw new RssPluginException(
                RssPluginException::MESSAGE_PLUGIN_LINK_IS_EMPTY,
                RssException::CODE_PLUGIN_LINK_IS_EMPTY
            );
        }

        if (empty($channelImage)) {
            throw new RssPluginException(
                RssPluginException::MESSAGE_PLUGIN_IMAGE_IS_EMPTY,
                RssException::CODE_PLUGIN_IMAGE_IS_EMPTY
            );
        }

        if (empty($channelDescription)) {
            throw new RssPluginException(
                RssPluginException::MESSAGE_PLUGIN_DESCRIPTION_IS_EMPTY,
                RssException::CODE_PLUGIN_DESCRIPTION_IS_EMPTY
            );
        }

        if (empty($links)) {
            throw new RssPluginException(
                RssPluginException::MESSAGE_PLUGIN_LINKS_ARE_EMPTY,
                RssException::CODE_PLUGIN_LINKS_ARE_EMPTY
            );
        }

        $values = [
            'channel_title' => $channelTitle,
            'channel_link' => $channelLink,
            'channel_image' => $channelImage,
            'channel_description' => $channelDescription,
            'links' => $links
        ];

        $this->_entity = new RssEntity($values);
    }

    /**
     * @param string|null $fileName
     *
     * @throws RssPluginException
     */
    public function save(?string $fileName = null): void
    {
        if (empty($this->_entity)) {
            throw new RssPluginException(
                RssPluginException::MESSAGE_PLUGIN_ENTITY_IS_NOT_SET,
                RssException::CODE_PLUGIN_ENTITY_IS_NOT_SET
            );
        }

        if (empty($fileName)) {
            $fileName = RssPlugin::DEFAULT_FILE_NAME;
        }

        $rssDirPath = RssPlugin::RSS_DIR_PATH;

        if (defined('APP_PUBLIC_DIR_PATH')) {
            $rssDirPath = sprintf('%s/xml', APP_PUBLIC_DIR_PATH);
        }

        if (!file_exists($rssDirPath) || !is_dir($rssDirPath)) {
            mkdir($rssDirPath, 0755, true);
        }

        $rssFilePath = sprintf('%s/%s.xml', $rssDirPath, $fileName);

        if (file_exists($rssFilePath) && is_file($rssFilePath)) {
            unlink($rssFilePath);
        }

        $rssContent = $this->_entity->getXml();

        file_put_contents($rssFilePath, $rssContent);
        chmod($rssFilePath, 0755);

        if (!file_exists($rssFilePath) || !is_file($rssFilePath)) {
            throw new RssPluginException(
                RssPluginException::MESSAGE_PLUGIN_CAN_NOT_SAVE_FILE,
                RssException::CODE_PLUGIN_CAN_NOT_SAVE_FILE
            );
        }
    }
}
