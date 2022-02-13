<?php

namespace Sonder\Core\Interfaces;

interface IRoleValuesObject
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return IRoleValuesObject|null
     */
    public function getParentVO(): ?IRoleValuesObject;

    /**
     * @return array|null
     */
    public function getAllowedActions(): ?array;

    /**
     * @return array|null
     */
    public function getDeniedActions(): ?array;

    /**
     * @param string|null $roleActionIdent
     * @return bool
     */
    public function can(?string $roleActionIdent = null): bool;
}
