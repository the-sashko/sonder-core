<?php

namespace Sonder\Models\Reference\Interfaces;

use Attribute;
use Sonder\Interfaces\IModelStore;

#[IModelStore]
#[Attribute(Attribute::TARGET_CLASS)]
interface IReferenceStore extends IModelStore
{
    /**
     * @param string|null $reference
     * @return array|null
     */
    public function getReferenceRowByReference(
        ?string $reference = null
    ): ?array;

    /**
     * @param string|null $reference
     * @return bool
     */
    public function deleteReferenceByReference(
        ?string $reference = null
    ): bool;

    /**
     * @param string|null $reference
     * @return bool
     */
    public function insertReference(?string $reference = null): bool;
}
