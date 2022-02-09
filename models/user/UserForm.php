<?php

namespace Sonder\Models\User;

use Exception;
use Sonder\Core\ModelFormObject;

final class UserForm extends ModelFormObject
{
    const LOGIN_MIN_LENGTH = 3;

    const LOGIN_MAX_LENGTH = 255;

    const EMAIL_MAX_LENGTH = 128;

    const EMAIL_PATTERN = '/^(.*?)@(.*?)\.(.*?)$/su';

    const PASSWORD_MIN_LENGTH = 8;

    const PASSWORD_MAX_LENGTH = 255;

    const LOGIN_EMPTY_ERROR_MESSAGE = 'Login is empty';

    const LOGIN_TOO_SHORT_ERROR_MESSAGE = 'Login is too short';

    const LOGIN_TOO_LONG_ERROR_MESSAGE = 'Login is too long';

    const LOGIN_EXISTS_ERROR_MESSAGE = 'User with this login already exists';

    const EMAIL_EMPTY_ERROR_MESSAGE = 'Email is empty';

    const EMAIL_TOO_LONG_ERROR_MESSAGE = 'Email is too long';

    const EMAIL_HAS_BAD_FORMAT = 'Email has bad format';

    const EMAIL_EXISTS_ERROR_MESSAGE = 'User with this email already exists';

    const PASSWORD_EMPTY_ERROR_MESSAGE = 'Password is empty';

    const PASSWORD_TOO_SHORT_ERROR_MESSAGE = 'Password is too short';

    const PASSWORD_TOO_LONG_ERROR_MESSAGE = 'Password is too long';

    const ROLE_IS_NOT_EXISTS_ERROR_MESSAGE = 'Role is not exist or inactive';

    const ROLE_IS_NOT_SET_ERROR_MESSAGE = 'Role is not set';

    const USER_IS_NOT_EXISTS_ERROR_MESSAGE = 'User with id "%d" is not exists';

    /**
     * @throws Exception
     */
    final public function checkInputValues(): void
    {
        $this->setStatusSuccess();

        $this->_validateLoginValue();
        $this->_validateEmailValue();
        $this->_validatePasswordValue();
        $this->_validateRoleIdValue();
    }

    /**
     * @return int|null
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
     * @throws Exception
     */
    final public function getEmail(): ?string
    {
        if ($this->has('email')) {
            return $this->get('email');
        }

        return null;
    }

    /**
     * @return string|null
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
     * @return bool
     * @throws Exception
     */
    final public function getIsAllowAccessByApi(): bool
    {
        if (!$this->has('is_allow_access_by_api')) {
            return false;
        }

        return (bool)$this->get('is_allow_access_by_api');
    }

    /**
     * @return bool
     * @throws Exception
     */
    final public function getIsActive(): bool
    {
        if (!$this->has('is_active')) {
            return false;
        }

        return (bool)$this->get('is_active');
    }

    /**
     * @param int|null $id
     * @return void
     * @throws Exception
     */
    final public function setId(?int $id = null): void
    {
        $this->set('id', $id);
    }

    /**
     * @param string|null $login
     * @return void
     * @throws Exception
     */
    final public function setLogin(?string $login = null): void
    {
        $this->set('login', $login);
    }

    /**
     * @param string|null $email
     * @return void
     * @throws Exception
     */
    final public function setEmail(?string $email = null): void
    {
        $this->set('email', $email);
    }

    /**
     * @param bool $isAllowAccessByApi
     * @return void
     * @throws Exception
     */
    final public function setIsAllowAccessByApi(
        bool $isAllowAccessByApi = false
    ): void
    {
        $this->set('is_allow_access_by_api', $isAllowAccessByApi);
    }

    /**
     * @param bool $isActive
     * @return void
     * @throws Exception
     */
    final public function setIsActive(bool $isActive = false): void
    {
        $this->set('is_active', $isActive);
    }

    /**
     * @return void
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
     * @return void
     * @throws Exception
     */
    private function _validateEmailValue(): void
    {
        $email = $this->getEmail();

        if (empty($email)) {
            $this->setError(UserForm::EMAIL_EMPTY_ERROR_MESSAGE);
            $this->setStatusFail();
        }

        if (!empty($email) && mb_strlen($email) > UserForm::EMAIL_MAX_LENGTH) {
            $this->setError(UserForm::EMAIL_TOO_LONG_ERROR_MESSAGE);
            $this->setStatusFail();
        }

        if (
            !empty($email) &&
            !preg_match(UserForm::EMAIL_PATTERN, $email)
        ) {
            $this->setError(UserForm::EMAIL_HAS_BAD_FORMAT);
            $this->setStatusFail();
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    private function _validatePasswordValue(): void
    {
        $id = $this->getId();
        $password = $this->getPassword();

        if (empty($id) && empty($password)) {
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

    /**
     * @return void
     * @throws Exception
     */
    private function _validateRoleIdValue(): void
    {
        if (empty($this->getRoleId())) {
            $this->setError(UserForm::ROLE_IS_NOT_SET_ERROR_MESSAGE);
            $this->setStatusFail();
        }
    }
}
