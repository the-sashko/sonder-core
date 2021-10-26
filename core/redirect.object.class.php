<?php

namespace Sonder\Core;

final class RedirectObject
{
    /**
     * @var string|null
     */
    private ?string $_url = null;

    /**
     * @var bool
     */
    private bool $_isPermanent = false;

    final public function __serialize(): array
    {
        return [
            'url' => base64_encode($this->_url),
            'is_permanent' => (int)$this->_isPermanent
        ];
    }

    /**
     * @param array $values
     */
    final public function __unserialize(array $values): void
    {
        $this->_url = base64_decode($values['url']);
        $this->_isPermanent = (bool)$values['is_permanent'];
    }

    /**
     * @return string
     */
    final public function getUrl(): string
    {
        return $this->_url;
    }

    /**
     * @param string|null $url
     */
    final public function setUrl(?string $url = null): void
    {
        $this->_url = $url;
    }

    /**
     * @param bool $isPermanent
     */
    final public function setIsPermanent(bool $isPermanent = false): void
    {
        $this->_isPermanent = $isPermanent;
    }

    final public function redirect(): void
    {
        $responseCode = $this->_isPermanent ? 301 : 302;

        if (!empty($this->_url)) {
            header(sprintf('Location: %s', $this->_url), true, $responseCode);
            exit(0);
        }
    }
}
