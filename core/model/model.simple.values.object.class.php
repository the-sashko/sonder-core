<?php

namespace Sonder\Core;

use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IModelSimpleValuesObject;
use Sonder\Interfaces\IReferencedValuesObject;
use Sonder\Interfaces\IValuesObject;

#[IValuesObject]
#[IModelSimpleValuesObject]
#[IReferencedValuesObject]
class ModelSimpleValuesObject
    extends ValuesObject
    implements IModelSimpleValuesObject, IReferencedValuesObject
{
    /**
     * @var string|null
     */
    protected ?string $linkPattern = null;

    /**
     * @return int|null
     * @throws ValuesObjectException
     */
    final public function getId(): ?int
    {
        if (!$this->has('id')) {
            return null;
        }

        return (int)$this->get('id');
    }

    /**
     * @return int|string|null
     * @throws ValuesObjectException
     */
    final public function getReference(): int|string|null
    {
        if (!$this->has('reference')) {
            return $this->getId();
        }

        return (string)$this->get('reference');
    }

    /**
     * @return string|null
     * @throws ValuesObjectException
     */
    final public function getLink(): ?string
    {
        if (empty($this->linkPattern)) {
            return null;
        }

        if (method_exists($this, 'getSlug')) {
            return sprintf($this->linkPattern, (string)$this->getSlug());
        }

        return sprintf($this->linkPattern, (string)$this->getId());
    }

    /**
     * @return bool
     * @throws ValuesObjectException
     */
    final public function isActive(): bool
    {
        return (bool)$this->get('is_active');
    }

    /**
     * @param string|null $format
     * @return string|int|null
     * @throws ValuesObjectException
     */
    final public function getCdate(?string $format = null): string|int|null
    {
        $cdate = (int)$this->get('cdate');

        if (empty($cdate)) {
            return null;
        }

        if (empty($format)) {
            return $cdate;
        }

        return date($format, $cdate);
    }

    /**
     * @param string|null $format
     * @return string|int|null
     * @throws ValuesObjectException
     */
    final public function getMdate(?string $format = null): string|int|null
    {
        $mdate = (int)$this->get('mdate');

        if (empty($mdate)) {
            return null;
        }

        if (empty($format)) {
            return $mdate;
        }

        return date($format, $mdate);
    }

    /**
     * @param string|null $format
     * @return string|int|null
     * @throws ValuesObjectException
     */
    final public function getDdate(?string $format = null): string|int|null
    {
        $ddate = (int)$this->get('ddate');

        if (empty($ddate)) {
            return null;
        }

        if (empty($format)) {
            return $ddate;
        }

        return date($format, $ddate);
    }

    /**
     * @return bool
     * @throws ValuesObjectException
     */
    final public function isRemoved(): bool
    {
        $ddate = $this->getDdate();

        if (empty($ddate)) {
            return false;
        }

        return true;
    }
}
