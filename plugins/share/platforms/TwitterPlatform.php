<?php

namespace Sonder\Plugins\Share\Platforms;

use Codebird\Codebird;
use Sonder\Plugins\Share\Exceptions\ShareException;
use Sonder\Plugins\Share\Exceptions\SharePlatformException;
use Sonder\Plugins\Share\Interfaces\ISharePlatform;

final class TwitterPlatform extends AbstractPlatform implements ISharePlatform
{
    private const MESSAGE_MAX_LENGTH = 140;

    /**
     * @var object|null
     */
    private ?object $_codebird = null;

    /**
     * @param string $message
     *
     * @return bool
     *
     * @throws SharePlatformException
     */
    final public function send(string $message): bool
    {
        $this->_validateMessage($message);

        if (null === $this->_codebird) {
            $this->_setCodebirdInstance();
        }

        $status = sprintf('status=%s', $message);
        $response = (array)$this->_codebird->statuses_update($status);

        return $this->_validateResponse($response);
    }

    /**
     * @throws SharePlatformException
     */
    private function _setCodebirdInstance(): void
    {
        $consumerKey = $this->_getConsumerKey();
        $consumerSecret = $this->_getConsumerSecret();
        $accessToken = $this->_getAccessToken();
        $accessSecret = $this->_getAccessSecret();

        Codebird::setConsumerKey($consumerKey, $consumerSecret);

        $this->_codebird = Codebird::getInstance();

        $this->_codebird->setToken($accessToken, $accessSecret);
    }

    /**
     * @return string
     *
     * @throws SharePlatformException
     */
    private function _getConsumerKey(): string
    {
        if (
            empty($this->_credentials) ||
            !array_key_exists('consumer', $this->_credentials) ||
            !is_array($this->_credentials['consumer']) ||
            !array_key_exists('key', $this->_credentials['consumer']) ||
            empty($this->_credentials['consumer']['key'])
        ) {
            throw new SharePlatformException(
                SharePlatformException::MESSAGE_INVALID_CREDENTIALS,
                ShareException::CODE_INVALID_CREDENTIALS
            );
        }

        return (string)$this->_credentials['consumer']['key'];
    }

    /**
     * @return string
     *
     * @throws SharePlatformException
     */
    private function _getConsumerSecret(): string
    {
        if (
            empty($this->_credentials) ||
            !array_key_exists('consumer', $this->_credentials) ||
            !is_array($this->_credentials['consumer']) ||
            !array_key_exists('secret', $this->_credentials['consumer']) ||
            empty($this->_credentials['consumer']['secret'])
        ) {
            throw new SharePlatformException(
                SharePlatformException::MESSAGE_INVALID_CREDENTIALS,
                ShareException::CODE_INVALID_CREDENTIALS
            );
        }

        return (string)$this->_credentials['consumer']['secret'];
    }

    /**
     * @return string
     *
     * @throws SharePlatformException
     */
    private function _getAccessToken(): string
    {
        if (
            empty($this->_credentials) ||
            !array_key_exists('access', $this->_credentials) ||
            !is_array($this->_credentials['access']) ||
            !array_key_exists('token', $this->_credentials['access']) ||
            empty($this->_credentials['access']['token'])
        ) {
            throw new SharePlatformException(
                SharePlatformException::MESSAGE_INVALID_CREDENTIALS,
                ShareException::CODE_INVALID_CREDENTIALS
            );
        }

        return (string)$this->_credentials['access']['token'];
    }

    /**
     * @return string
     *
     * @throws SharePlatformException
     */
    private function _getAccessSecret(): string
    {
        if (
            empty($this->_credentials) ||
            !array_key_exists('access', $this->_credentials) ||
            !is_array($this->_credentials['access']) ||
            !array_key_exists('secret', $this->_credentials['access']) ||
            empty($this->_credentials['access']['secret'])
        ) {
            throw new SharePlatformException(
                SharePlatformException::MESSAGE_INVALID_CREDENTIALS,
                ShareException::CODE_INVALID_CREDENTIALS
            );
        }

        return (string)$this->_credentials['access']['secret'];
    }

    /**
     * @param string $message
     *
     * @throws SharePlatformException
     */
    private function _validateMessage(string $message): void
    {
        if (strlen($message) > TwitterPlatform::MESSAGE_MAX_LENGTH) {
            $errorMessage = sprintf(
                '%s: Twitter API Error: Message Too Long',
                SharePlatformException::MESSAGE_MESSAGE_HAS_BAD_FORMAT
            );

            throw new SharePlatformException(
                $errorMessage,
                ShareException::CODE_MESSAGE_HAS_BAD_FORMAT
            );
        }

        if (empty(trim($message))) {
            $errorMessage = sprintf(
                '%s: Message Empty',
                SharePlatformException::MESSAGE_MESSAGE_HAS_BAD_FORMAT
            );

            throw new SharePlatformException(
                $errorMessage,
                ShareException::CODE_MESSAGE_HAS_BAD_FORMAT
            );
        }
    }

    /**
     * @param array|null $response
     *
     * @return bool
     *
     * @throws SharePlatformException
     */
    private function _validateResponse(?array $response = null): bool
    {
        if (empty($response)) {
            $errorMessage = sprintf(
                '%s: Twitter API Response Has Bad Format',
                SharePlatformException::MESSAGE_REMOTE_ERROR
            );

            throw new SharePlatformException(
                $errorMessage,
                ShareException::CODE_REMOTE_ERROR
            );
        }

        if (!array_key_exists('errors', $response)) {
            return true;
        }

        $errors = (array)$response['errors'];

        if (empty($errors)) {
            $errorMessage = sprintf(
                '%s: Unknown Twitter API Error',
                SharePlatformException::MESSAGE_REMOTE_ERROR
            );

            throw new SharePlatformException(
                $errorMessage,
                ShareException::CODE_REMOTE_ERROR
            );
        }

        $error = (array)array_shift($errors);

        if (!array_key_exists('message', $error)) {
            $errorMessage = sprintf(
                '%s: Unknown Twitter API Error',
                SharePlatformException::MESSAGE_REMOTE_ERROR
            );

            throw new SharePlatformException(
                $errorMessage,
                ShareException::CODE_REMOTE_ERROR
            );
        }

        $error['message'] = (string)$error['message'];

        if (empty($error['message'])) {
            $errorMessage = sprintf(
                '%s: Unknown Twitter API Error',
                SharePlatformException::MESSAGE_REMOTE_ERROR
            );

            throw new SharePlatformException(
                $errorMessage,
                ShareException::CODE_REMOTE_ERROR
            );
        }

        $errorMessage = sprintf(
            '%s: %s',
            SharePlatformException::MESSAGE_REMOTE_ERROR,
            $error['message']
        );

        throw new SharePlatformException(
            $errorMessage,
            ShareException::CODE_REMOTE_ERROR
        );
    }
}
