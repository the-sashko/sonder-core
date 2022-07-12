<?php

namespace Sonder\Models\Reference\ValuesObjects;

use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IValuesObject;
use Sonder\Core\ValuesObject;
use Sonder\Models\Reference\Interfaces\IReferenceValuesObject;

#[IValuesObject]
#[IReferenceValuesObject]
final class ReferenceValuesObject
    extends ValuesObject
    implements IReferenceValuesObject
{
    /**
     * @return string|null
     * @throws ValuesObjectException
     */
    final public function getReference(): ?string
    {
        if (!$this->has('reference')) {
            return null;
        }

        return (string)$this->get('reference');
    }

    /**
     * @param string|null $reference
     * @return void
     * @throws ValuesObjectException
     */
    final public function setReference(?string $reference = null): void
    {
        $this->set('reference', $reference);
    }
}
