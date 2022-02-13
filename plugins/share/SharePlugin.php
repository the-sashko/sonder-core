<?php

namespace Sonder\Plugins;

use Sonder\Plugins\Share\Exceptions\ShareException;
use Sonder\Plugins\Share\Exceptions\SharePluginException;
use Sonder\Plugins\Share\Interfaces\ISharePlatform;
use Sonder\Plugins\Share\Interfaces\ISharePlugin;

final class SharePlugin implements ISharePlugin
{
    /**
     * @var ISharePlatform|null
     */
    private ?ISharePlatform $_platform = null;

    /**
     * @param string|null $platform
     * @param array|null $credentials
     *
     * @throws SharePluginException
     */
    final public function __construct(
        ?string $platform = null,
        ?array  $credentials = null
    )
    {
        if (empty($platform)) {
            throw new SharePluginException(
                SharePluginException::MESSAGE_PLATFORM_IS_NOT_SET,
                ShareException::CODE_PLATFORM_IS_NOT_SET
            );
        }

        if (empty($credentials)) {
            throw new SharePluginException(
                SharePluginException::MESSAGE_CREDENTIALS_IS_NOT_SET,
                ShareException::CODE_CREDENTIALS_IS_NOT_SET
            );
        }

        $this->_setPlatform($platform, $credentials);
    }

    /**
     * @param string|null $message
     *
     * @return bool
     *
     * @throws SharePluginException
     */
    final public function send(?string $message = null): bool
    {
        if (empty($message)) {
            throw new SharePluginException(
                SharePluginException::MESSAGE_MESSAGE_IS_NOT_SET,
                ShareException::CODE_MESSAGE_IS_NOT_SET
            );
        }

        if (empty($this->_platform)) {
            throw new SharePluginException(
                SharePluginException::MESSAGE_PLATFORM_IS_NOT_SET,
                ShareException::CODE_PLATFORM_IS_NOT_SET
            );
        }

        return $this->_platform->send($message);
    }

    /**
     * @param string $platform
     * @param array $credentials
     *
     * @throws SharePluginException
     */
    private function _setPlatform(string $platform, array $credentials): void
    {
        $platformClass = sprintf('%sPlatform', $platform);

        if (!class_exists($platformClass)) {
            throw new SharePluginException(
                SharePluginException::MESSAGE_PLATFORM_NOT_EXISTS,
                ShareException::CODE_PLATFORM_NOT_EXISTS
            );
        }

        $this->_platform = new $platformClass($credentials);
    }
}
