<?php
namespace SonderCore\Core;

final class RouterObject
{
    private ?string $_controller = null;

    private ?string $_method = null;

    final public function getController(): ?string
    {
        return $this->_controller;
    }

    final public function getMethod(): ?string
    {
        return $this->_controller;
    }

    final public function setController(?string $controller = null): void
    {
        if (!empty($controller)) {
            $this->_controller = $controller;
        }
    }

    final public function setMethod(?string $method = null): void
    {
        if (!empty($method)) {
            $this->_method = $method;
        }
    }
}
