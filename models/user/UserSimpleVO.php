<?php

namespace Sonder\Models\User;

use Exception;
use Sonder\Core\Interfaces\IRoleValuesObject;
use Sonder\Core\ModelSimpleValuesObject;

final class UserSimpleValuesObject extends ModelSimpleValuesObject
{
    /**
     * @return string|null
     * @throws Exception
     */
    final public function getLogin(): ?string
    {
        if (!$this->has('login')) {
            return null;
        }

        return $this->get('login');
    }

    /**
     * @return string|null
     * @throws Exception
     */
    final public function getEmail(): ?string
    {
        if (!$this->has('email')) {
            return null;
        }

        return $this->get('email');
    }

    /**
     * @return int|null
     * @throws Exception
     */
    final public function getRoleId(): ?int
    {
        if (!$this->has('role_id')) {
            return null;
        }

        return (int)$this->get('role_id');
    }

    /**
     * @return IRoleValuesObject|null
     * @throws Exception
     */
    final public function getRoleVO(): ?IRoleValuesObject
    {
        if (!$this->has('role_simple_vo')) {
            return null;
        }

        return $this->get('role_simple_vo');
    }

    /**
     * @param IRoleValuesObject|null $roleVO
     * @return void
     * @throws Exception
     */
    final public function setRoleVO(?IRoleValuesObject $roleVO = null): void
    {
        if (!empty($roleVO)) {
            $this->set('role_simple_vo', $roleVO);
        }
    }

    /**
     * @param array|null $params
     * @return array|null
     * @throws Exception
     */
    final public function exportRow(?array $params = null): ?array
    {
        $roleVO = $this->getRoleVO();

        return [
            'id' => $this->getId(),
            'login' => $this->getLogin(),
            'email' => $this->getEmail(),
            'role' => empty($roleVO) ? null : $roleVO->exportRow()
        ];
    }
}
