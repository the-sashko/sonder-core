<?php
namespace SonderCore\Core;

class RedirectObject
{
    /**
     * @var string|null
     */
    private ?string $_url = null;

    /**
     * @var bool
     */
    private bool $_isPermanent = false;

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
