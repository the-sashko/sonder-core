<?php

namespace Sonder\Core;

use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IModelSimpleValuesObject;
use Sonder\Interfaces\IModelValuesObject;
use Sonder\Interfaces\IReferencedValuesObject;
use Sonder\Interfaces\IValuesObject;

#[IValuesObject]
#[IModelSimpleValuesObject]
#[IReferencedValuesObject]
#[IModelValuesObject]
abstract class ModelValuesObject
    extends ModelSimpleValuesObject
    implements IModelValuesObject
{
    protected const EDIT_LINK_PATTERN = null;

    protected const REMOVE_LINK_PATTERN = null;

    protected const RESTORE_LINK_PATTERN = null;

    /**
     * @return string|null
     * @throws ValuesObjectException
     */
    final public function getEditLink(): ?string
    {
        if (empty(static::EDIT_LINK_PATTERN) || empty($this->getId())) {
            return null;
        }

        return sprintf(static::EDIT_LINK_PATTERN, $this->getId());
    }

    /**
     * @return string|null
     * @throws ValuesObjectException
     */
    final public function getRemoveLink(): ?string
    {
        if (empty(static::REMOVE_LINK_PATTERN) || empty($this->getId())) {
            return null;
        }

        return sprintf(static::REMOVE_LINK_PATTERN, $this->getId());
    }

    /**
     * @return string|null
     * @throws ValuesObjectException
     */
    final public function getRestoreLink(): ?string
    {
        if (empty(static::RESTORE_LINK_PATTERN) || empty($this->getId())) {
            return null;
        }

        return sprintf(static::RESTORE_LINK_PATTERN, $this->getId());
    }

    /**
     * @param string|null $reference
     * @return void
     * @throws ValuesObjectException
     */
    final public function setReference(?string $reference = null): void
    {
        if (empty($reference)) {
            return;
        }

        $this->set('reference', $reference);
    }

    /**
     * @param bool $isActive
     * @return void
     * @throws ValuesObjectException
     */
    final public function setIsActive(bool $isActive = true): void
    {
        $this->set('is_active', $isActive);
    }

    /**
     * @return void
     * @throws ValuesObjectException
     */
    final public function setCdate(): void
    {
        $this->set('cdate', time());
    }

    /**
     * @return void
     * @throws ValuesObjectException
     */
    final public function setMdate(): void
    {
        $this->set('mdate', time());
    }

    /**
     * @return void
     * @throws ValuesObjectException
     */
    final public function setDdate(): void
    {
        $this->set('ddate', time());
    }
}
