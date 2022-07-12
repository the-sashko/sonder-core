<?php

namespace Sonder\Models\Reference\Interfaces;

use Attribute;
use Sonder\Interfaces\IModel;

#[IModel]
#[Attribute(Attribute::TARGET_CLASS)]
interface IReferenceModel extends IModel
{
    /**
     * @param string|null $reference
     * @return IReferenceValuesObject|null
     */
    public function getVOByReference(
        ?string $reference = null
    ): ?IReferenceValuesObject;

    /**
     * @param string|null $reference
     * @return bool
     */
    public function removeByReference(?string $reference = null): bool;

    /**
     * @return string
     */
    public function create(): string;
}
