<?php

namespace Sonder\Models\User;

use Exception;
use Sonder\Core\ModelFormObject;

final class SignInForm extends ModelFormObject
{
    const INVALID_LOGIN_OR_PASSWORD = 'Invalid Login Or Password';

    /**
     * @throws Exception
     */
    final public function checkInputValues(): void
    {
        $this->setStatusSuccess();

        $this->_validateLoginValue();
        $this->_validatePasswordValue();
    }

    /**
     * @return int|null
     *
     * @throws Exception
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
     *
     * @throws Exception
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
     *
     * @throws Exception
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
     *
     * @throws Exception
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
     *
     * @throws Exception
     */
    final public function setLogin(?string $login = null): void
    {
        $this->set('login', $login);
    }

    /**
     * @throws Exception
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
     * @throws Exception
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
