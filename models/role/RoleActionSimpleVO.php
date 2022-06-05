<?php

namespace Sonder\Models\Role;

use Exception;
use Sonder\Core\Interfaces\IRoleActionValuesObject;
use Sonder\Core\ModelSimpleValuesObject;

final class RoleActionSimpleValuesObject
    extends ModelSimpleValuesObject
    implements IRoleActionValuesObject
{
    /**
     * @return string
     * @throws Exception
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
     * @param array|null $params
     * @return array|null
     * @throws Exception
     */
    final public function exportRow(?array $params = null): ?array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName()
        ];
    }
}
