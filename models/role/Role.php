<?php

namespace Sonder\Models;

use Sonder\Core\CoreModel;
use Sonder\Core\Interfaces\IModel;
use Sonder\Core\Interfaces\IRole;
use Sonder\Models\Role\RoleVO;

final class Role extends CoreModel implements IModel, IRole
{
    /**
     * @var RoleVO|null
     */
    private ?RoleVO $_vo = null;

    /**
     * @return int|null
     */
    final public function getId(): ?int
    {
        if (empty($this->vo)) {
            return null;
        }

        return $this->_vo->getId();
    }

    /**
     * @return string|null
     */
    final public function getName(): ?string
    {
        if (empty($this->vo)) {
            return null;
        }

        return $this->_vo->getName();
    }

    /**
     * @return array|null
     */
    final public function getActions(): ?array
    {
        if (empty($this->vo)) {
            return null;
        }

        return $this->_vo->getActions();
    }

    /**
     * @param string $roleActionIdent
     *
     * @return bool
     */
    final public function can(string $roleActionIdent): bool
    {
        $actions = $this->getActions();

        if (empty($actions)) {
            return false;
        }

        return in_array($roleActionIdent, $actions);
    }

    //TODO
}
