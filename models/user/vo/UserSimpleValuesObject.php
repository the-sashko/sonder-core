<?php

namespace Sonder\Models\User\ValuesObjects;

use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IModelSimpleValuesObject;
use Sonder\Core\ModelSimpleValuesObject;
use Sonder\Interfaces\IValuesObject;
use Sonder\Models\Role\Interfaces\IRoleSimpleValuesObject;
use Sonder\Models\User\Interfaces\IUserSimpleValuesObject;

#[IValuesObject]
#[IModelSimpleValuesObject]
#[IUserSimpleValuesObject]
final class UserSimpleValuesObject
    extends ModelSimpleValuesObject
    implements IUserSimpleValuesObject
{
    /**
     * @return string|null
     * @throws ValuesObjectException
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
     * @throws ValuesObjectException
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
     * @throws ValuesObjectException
     */
    final public function getRoleId(): ?int
    {
        if (!$this->has('role_id')) {
            return null;
        }

        return (int)$this->get('role_id');
    }

    /**
     * @return IRoleSimpleValuesObject|null
     * @throws ValuesObjectException
     */
    final public function getRoleVO(): ?IRoleSimpleValuesObject
    {
        if (!$this->has('role_simple_vo')) {
            return null;
        }

        return $this->get('role_simple_vo');
    }

    /**
     * @param IRoleSimpleValuesObject|null $roleVO
     * @return void
     * @throws ValuesObjectException
     */
    final public function setRoleVO(
        ?IRoleSimpleValuesObject $roleVO = null
    ): void
    {
        if (!empty($roleVO)) {
            $this->set('role_simple_vo', $roleVO);
        }
    }

    /**
     * @return array
     * @throws ValuesObjectException
     */
    final public function exportRow(): array
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
