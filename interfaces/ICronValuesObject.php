<?php

namespace Sonder\Interfaces;

use Attribute;

#[IModelValuesObject]
#[Attribute(Attribute::TARGET_CLASS)]
interface ICronValuesObject
{
    /**
     * @return string
     */
    public function getAlias(): string;

    /**
     * @return string
     */
    public function getController(): string;

    /**
     * @return string
     */
    public function getControllerMethod(): string;

    /**
     * @param bool $isFormatAsString
     * @return string|int|null
     */
    public function getInterval(
        bool $isFormatAsString = false
    ): string|int|null;

    /**
     * @param string|null $format
     * @return string|int|null
     */
    public function getTimeNextExec(?string $format = null): string|int|null;

    /**
     * @return string
     */
    public function getStatus(): string;

    /**
     * @return string|null
     */
    public function getErrorMessage(): ?string;
}
