<?php

namespace Sonder\Models\User;

use Exception;
use Sonder\Core\ModelFormObject;

final class UserForm extends ModelFormObject
{
    const LOGIN_MIN_LENGTH = 3;

    const LOGIN_MAX_LENGTH = 255;

    const PASSWORD_MIN_LENGTH = 8;

    const PASSWORD_MAX_LENGTH = 255;

    const LOGIN_EMPTY_ERROR_MESSAGE = 'Login is empty';

    const LOGIN_TOO_SHORT_ERROR_MESSAGE = 'Login is too short';

    const LOGIN_TOO_LONG_ERROR_MESSAGE = 'Login is too long';

    const LOGIN_EXISTS_ERROR_MESSAGE = 'User with this login already exists';

    const PASSWORD_EMPTY_ERROR_MESSAGE = 'Password is empty';

    const PASSWORD_TOO_SHORT_ERROR_MESSAGE = 'Password is too short';

    const PASSWORD_TOO_LONG_ERROR_MESSAGE = 'Password is too long';

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

        if (!empty($login) && mb_strlen($login) > UserForm::LOGIN_MAX_LENGTH) {
            $this->setError(UserForm::LOGIN_TOO_LONG_ERROR_MESSAGE);
            $this->setStatusFail();
        }

        if (!empty($login) && mb_strlen($login) < UserForm::LOGIN_MIN_LENGTH) {
            $this->setError(UserForm::LOGIN_TOO_SHORT_ERROR_MESSAGE);
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

        if (
            !empty($password) &&
            mb_strlen($password) > UserForm::PASSWORD_MAX_LENGTH
        ) {
            $this->setError(UserForm::PASSWORD_TOO_LONG_ERROR_MESSAGE);
            $this->setStatusFail();
        }

        if (
            !empty($password) &&
            mb_strlen($password) < UserForm::PASSWORD_MIN_LENGTH
        ) {
            $this->setError(UserForm::PASSWORD_TOO_SHORT_ERROR_MESSAGE);
            $this->setStatusFail();
        }
    }
}
