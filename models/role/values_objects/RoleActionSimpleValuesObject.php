<?php

namespace Sonder\Models\Role\ValuesObjects;

use Sonder\Core\ModelSimpleValuesObject;
use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IModelSimpleValuesObject;
use Sonder\Interfaces\IValuesObject;
use Sonder\Models\Role\Interfaces\IRoleActionSimpleValuesObject;

#[IValuesObject]
#[IModelSimpleValuesObject]
#[IRoleActionSimpleValuesObject]
final class RoleActionSimpleValuesObject
    extends ModelSimpleValuesObject
    implements IRoleActionSimpleValuesObject
{
    /**
     * @return string
     * @throws ValuesObjectException
     */
    final public function getName(): string
    {
        $name = null;

        if ($this->has('name')) {
            $name = $this->get('name');
        }

        return (string)$name;
    }

    /**
     * @return array
     * @throws ValuesObjectException
     */
    final public function exportRow(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName()
        ];
    }
}
