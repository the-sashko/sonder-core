<?php

use Core\Plugins\Rss\Classes\RssEntity;
use Core\Plugins\Rss\Exceptions\RssEntityException;
use Core\Plugins\Rss\Exceptions\RssPluginException;
use Core\Plugins\Rss\Interfaces\IRssEntity;
use Core\Plugins\Rss\Interfaces\IRssPlugin;

class RssPlugin implements IRssPlugin
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
     * @param string|null $channelDescription
     * @param array|null $links
     *
     * @throws RssPluginException
     */
    public function load(
        ?string $channelTitle = null,
        ?string $channelLink = null,
        ?string $channelDescription = null,
        ?array  $links = null
    ): void
    {
        if (empty($channelTitle)) {
            throw new RssPluginException(
                RssPluginException::MESSAGE_PLUGIN_TITLE_IS_EMPTY,
                RssPluginException::CODE_PLUGIN_TITLE_IS_EMPTY
            );
        }

        if (empty($channelLink)) {
            throw new RssPluginException(
                RssPluginException::MESSAGE_PLUGIN_LINK_IS_EMPTY,
                RssPluginException::CODE_PLUGIN_LINK_IS_EMPTY
            );
        }

        if (empty($channelDescription)) {
            throw new RssPluginException(
                RssPluginException::MESSAGE_PLUGIN_DESCRIPTION_IS_EMPTY,
                RssPluginException::CODE_PLUGIN_DESCRIPTION_IS_EMPTY
            );
        }

        if (empty($links)) {
            throw new RssPluginException(
                RssPluginException::MESSAGE_PLUGIN_LINKS_ARE_EMPTY,
                RssPluginException::CODE_PLUGIN_LINKS_ARE_EMPTY
            );
        }

        $values = [
            'channel_title' => $channelTitle,
            'channel_link' => $channelLink,
            'channel_description' => $channelDescription,
            'links' => $links
        ];

        $this->_entity = new RssEntity($values);
    }

    /**
     * @param string|null $fileName
     *
     * @throws RssPluginException
     * @throws RssEntityException
     */
    public function save(?string $fileName = null): void
    {
        if (empty($this->_entity)) {
            throw new RssPluginException(
                RssPluginException::MESSAGE_PLUGIN_ENTITY_IS_NOT_SET,
                RssPluginException::CODE_PLUGIN_ENTITY_IS_NOT_SET
            );
        }

        if (empty($fileName)) {
            $fileName = static::DEFAULT_FILE_NAME;
        }

        if (
            !file_exists(static::RSS_DIR_PATH) ||
            !is_dir(static::RSS_DIR_PATH)
        ) {
            mkdir(static::RSS_DIR_PATH, 0755, true);
        }

        $rssFilePath = sprintf(
            '%s/%s.xml',
            static::RSS_DIR_PATH,
            $fileName
        );

        if (file_exists($rssFilePath) && is_file($rssFilePath)) {
            unlink($rssFilePath);
        }

        $rssContent = $this->_entity->getXML();

        file_put_contents($rssFilePath, $rssContent);
        chmod($rssFilePath, 0755);

        if (!file_exists($rssFilePath) || !is_file($rssFilePath)) {
            throw new RssPluginException(
                RssPluginException::MESSAGE_PLUGIN_CAN_NOT_SAVE_FILE,
                RssPluginException::CODE_PLUGIN_CAN_NOT_SAVE_FILE
            );
        }
    }
}
