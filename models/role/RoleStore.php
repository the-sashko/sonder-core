<?php

namespace Sonder\Models\Role;

use Sonder\Core\Interfaces\IModelStore;
use Sonder\Core\ModelStore;

final class RoleStore extends ModelStore implements IModelStore
{
    /**
     * @var string|null
     */
    public ?string $scope = 'role';

    final public function getRowById(?int $id = null): ?array
    {
        //TODO

        return null;
    }

    //TODO
}