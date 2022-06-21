<?php

namespace Sonder\Models\User\Forms;

use Sonder\Core\ModelFormObject;
use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IModelFormObject;
use Sonder\Models\User\Interfaces\ISignInForm;

#[IModelFormObject]
#[ISignInForm]
final class SignInForm extends ModelFormObject implements ISignInForm
{
    final public const INVALID_LOGIN_OR_PASSWORD_ERROR_MESSAGE = 'Invalid Login Or Password';

    /**
     * @return void
     * @throws ValuesObjectException
     */
    final public function checkInputValues(): void
    {
        $this->setStatusSuccess();

        $this->_validateLoginValue();
        $this->_validatePasswordValue();
    }

    /**
     * @return int|null
     * @throws ValuesObjectException
     */
    final public function getId(): ?int
    {
        if (!$this->has('id')) {
            return null;
        }

        $id = $this->get('id');

        if (empty($id)) {
            return null;
        }

        return (int)$id;
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

        $roleId = $this->get('role_id');

        if (empty($roleId)) {
            return null;
        }

        return (int)$roleId;
    }

    /**
     * @return string|null
     * @throws ValuesObjectException
     */
    final public function getLogin(): ?string
    {
        if ($this->has('login')) {
            return $this->get('login');
        }

        return null;
    }

    /**
     * @return string|null
     * @throws ValuesObjectException
     */
    final public function getPassword(): ?string
    {
        if ($this->has('password')) {
            return $this->get('password');
        }

        return null;
    }

    /**
     * @param string|null $login
     * @return void
     * @throws ValuesObjectException
     */
    final public function setLogin(?string $login = null): void
    {
        $this->set('login', $login);
    }

    /**
     * @return void
     * @throws ValuesObjectException
     */
    private function _validateLoginValue(): void
    {
        $login = $this->getLogin();

        if (empty($login)) {
            $this->setError(UserForm::LOGIN_EMPTY_ERROR_MESSAGE);
            $this->setStatusFail();
        }
    }

    /**
     * @return void
     * @throws ValuesObjectException
     */
    private function _validatePasswordValue(): void
    {
        $password = $this->getPassword();

        if (empty($password)) {
            $this->setError(UserForm::PASSWORD_EMPTY_ERROR_MESSAGE);
            $this->setStatusFail();
        }
    }
}
