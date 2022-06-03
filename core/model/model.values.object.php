<?php

namespace Sonder\Core;

use Exception;

class ModelValuesObject extends ModelSimpleValuesObject
{

    /**
     * @var string|null
     */
    protected ?string $editLinkPattern = null;

    /**
     * @var string|null
     */
    protected ?string $removeLinkPattern = null;

    /**
     * @var string|null
     */
    protected ?string $restoreLinkPattern = null;

    /**
     * @return string|null
     * @throws Exception
     */
    final public function getEditLink(): ?string
    {
        if (empty($this->editLinkPattern)) {
            return null;
        }

        return sprintf($this->editLinkPattern, $this->getId());
    }

    /**
     * @return string|null
     * @throws Exception
     */
    final public function getRemoveLink(): ?string
    {
        if (empty($this->removeLinkPattern)) {
            return null;
        }

        return sprintf($this->removeLinkPattern, $this->getId());
    }

    /**
     * @return string|null
     * @throws Exception
     */
    final public function getRestoreLink(): ?string
    {
        if (empty($this->restoreLinkPattern)) {
            return null;
        }

        return sprintf($this->restoreLinkPattern, $this->getId());
    }

    /**
     * @param bool $isActive
     * @return void
     * @throws Exception
     */
    final public function setIsActive(bool $isActive = true): void
    {
        $this->set('is_active', $isActive);
    }

    /**
     * @return void
     * @throws Exception
     */
    final public function setCdate(): void
    {
        $this->set('cdate', time());
    }

    /**
     * @return void
     * @throws Exception
     */
    final public function setMdate(): void
    {
        $this->set('mdate', time());
    }

    /**
     * @return void
     * @throws Exception
     */
    final public function setDdate(): void
    {
        $this->set('ddate', time());
    }
}
