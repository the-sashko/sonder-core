<?php

namespace Sonder\Core\Interfaces;

interface IRoleValuesObject
{
    /**
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @param string|null $roleActionIdent
     * @return bool
     */
    public function can(?string $roleActionIdent = null): bool;
}
