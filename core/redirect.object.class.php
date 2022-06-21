<?php

namespace Sonder\Core;

use Sonder\Enums\HttpCodesEnum;
use Sonder\Interfaces\IRedirectObject;

#[IRedirectObject]
final class RedirectObject implements IRedirectObject
{
    private const HTTP_HEADER_PATTERN = 'Location: %s';

    /**
     * @param string|null $_url
     * @param bool $_isPermanent
     */
    public function __construct(
        private ?string $_url = null,
        private bool $_isPermanent = false
    ) {}

    /**
     * @return array
     */
    final public function __serialize(): array
    {
        return [
            'url' => base64_encode((string)$this->_url),
            'is_permanent' => (int)$this->_isPermanent
        ];
    }

    /**
     * @param array $values
     * @return void
     */
    final public function __unserialize(array $values): void
    {
        $this->_url = base64_decode($values['url']);
        $this->_isPermanent = (bool)$values['is_permanent'];
    }

    /**
     * @param string|null $url
     * @return void
     */
    final public function setUrl(?string $url = null): void
    {
        $this->_url = $url;
    }

    /**
     * @param bool $isPermanent
     * @return void
     */
    final public function setIsPermanent(bool $isPermanent = false): void
    {
        $this->_isPermanent = $isPermanent;
    }

    /**
     * @return void
     */
    final public function redirect(): void
    {
        if (empty($this->_url)) {
            return;
        }

        $httpCode = HttpCodesEnum::FOUND;

        if ($this->_isPermanent) {
            $httpCode = HttpCodesEnum::MOVED_PERMANENTLY;
        }

        header($this->_getHttpHeader(), true, $httpCode->value);

        exit(0);
    }

    /**
     * @return string|null
     */
    private function _getHttpHeader(): ?string
    {
        if (empty($this->_url)) {
            return null;
        }

        return sprintf(RedirectObject::HTTP_HEADER_PATTERN, $this->_url);
    }
}
