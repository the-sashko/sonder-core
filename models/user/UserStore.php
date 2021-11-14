<?php

namespace Sonder\Models\User;

use Sonder\Core\Interfaces\IModelStore;
use Sonder\Core\ModelStore;

final class UserStore extends ModelStore implements IModelStore
{
    /**
     * @var string|null
     */
    public ?string $scope = 'user';

    final public function getRowByAuthToken(?string $token = null): ?array
    {
        //TODO

        return null;
    }

    final public function getRowByLogin(?string $login = null): ?array
    {
        //TODO

        return null;
    }

    //TODO
}
