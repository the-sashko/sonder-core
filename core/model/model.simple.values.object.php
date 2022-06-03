<?php

namespace Sonder\Core;

use Exception;

class ModelSimpleValuesObject extends ValuesObject
{
    /**
     * @var string|null
     */
    protected ?string $linkPattern = null;

    /**
     * @return int|null
     * @throws Exception
     */
    final public function getId(): ?int
    {
        if (!$this->has('id')) {
            return null;
        }

        return (int)$this->get('id');
    }

    /**
     * @return string|null
     * @throws Exception
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
     * @throws Exception
     */
    final public function isActive(): bool
    {
        return (bool)$this->get('is_active');
    }

    /**
     * @param string|null $format
     * @return string|int|null
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
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
