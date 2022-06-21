<?php

namespace Sonder\Models\User\Forms;

use Sonder\Core\ModelFormObject;
use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IModelFormObject;
use Sonder\Models\User\Interfaces\IUserForm;

#[IModelFormObject]
#[IUserForm]
final class UserForm extends ModelFormObject implements IUserForm
{
    final public const EMAIL_PATTERN = '/^(.*?)@(.*?)\.(.*?)$/su';

    final public const LOGIN_EMPTY_ERROR_MESSAGE = 'Login is empty';

    final public const LOGIN_TOO_SHORT_ERROR_MESSAGE = 'Login is too short';

    final public const LOGIN_TOO_LONG_ERROR_MESSAGE = 'Login is too long';

    final public const LOGIN_EXISTS_ERROR_MESSAGE = 'User with this login already exists';

    final public const EMAIL_EMPTY_ERROR_MESSAGE = 'Email is empty';

    final public const EMAIL_TOO_LONG_ERROR_MESSAGE = 'Email is too long';

    final public const EMAIL_HAS_BAD_FORMAT_ERROR_MESSAGE = 'Email has bad format';

    final public const EMAIL_EXISTS_ERROR_MESSAGE = 'User with this email already exists';

    final public const PASSWORD_EMPTY_ERROR_MESSAGE = 'Password is empty';

    final public const PASSWORD_TOO_SHORT_ERROR_MESSAGE = 'Password is too short';

    final public const PASSWORD_TOO_LONG_ERROR_MESSAGE = 'Password is too long';

    final public const ROLE_NOT_EXISTS_ERROR_MESSAGE = 'Role not exists or inactive';

    final public const ROLE_IS_NOT_SET_ERROR_MESSAGE = 'Role is not set';

    final public const USER_NOT_EXISTS_ERROR_MESSAGE = 'User with id "%d" not exists';

    private const LOGIN_MIN_LENGTH = 3;

    private const LOGIN_MAX_LENGTH = 255;

    private const EMAIL_MAX_LENGTH = 128;

    private const PASSWORD_MIN_LENGTH = 8;

    private const PASSWORD_MAX_LENGTH = 255;

    /**
     * @return void
     * @throws ValuesObjectException
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
            $login = $this->get('login');
            $login = preg_replace('/(\s+)/u', '', $login);

            return empty($login) ? null : $login;
        }

        return null;
    }

    /**
     * @return string|null
     * @throws ValuesObjectException
     */
    final public function getEmail(): ?string
    {
        if ($this->has('email')) {
            $email = $this->get('email');
            $email = preg_replace('/(\s+)/u', '', $email);

            return $email ?? null;
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
     * @return bool
     * @throws ValuesObjectException
     */
    final public function isAllowAccessByApi(): bool
    {
        if (!$this->has('is_allow_access_by_api')) {
            return false;
        }

        return (bool)$this->get('is_allow_access_by_api');
    }

    /**
     * @return bool
     * @throws ValuesObjectException
     */
    final public function isActive(): bool
    {
        if (!$this->has('is_active')) {
            return false;
        }

        return (bool)$this->get('is_active');
    }

    /**
     * @param int|null $id
     * @return void
     * @throws ValuesObjectException
     */
    final public function setId(?int $id = null): void
    {
        $this->set('id', $id);
    }

    /**
     * @param string|null $login
     * @return void
     * @throws ValuesObjectException
     */
    final public function setLogin(?string $login = null): void
    {
        $login = preg_replace('/(\s+)/u', '', $login);

        $this->set('login', $login);
    }

    /**
     * @param string|null $email
     * @return void
     * @throws ValuesObjectException
     */
    final public function setEmail(?string $email = null): void
    {
        $email = preg_replace('/(\s+)/u', '', $email);

        $this->set('email', $email);
    }

    /**
     * @param bool $isAllowAccessByApi
     * @return void
     * @throws ValuesObjectException
     */
    final public function setIsAllowAccessByApi(
        bool $isAllowAccessByApi = false
    ): void {
        $this->set('is_allow_access_by_api', $isAllowAccessByApi);
    }

    /**
     * @param bool $isActive
     * @return void
     * @throws ValuesObjectException
     */
    final public function setIsActive(bool $isActive = false): void
    {
        $this->set('is_active', $isActive);
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
     * @throws ValuesObjectException
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
            $this->setError(UserForm::EMAIL_HAS_BAD_FORMAT_ERROR_MESSAGE);
            $this->setStatusFail();
        }
    }

    /**
     * @return void
     * @throws ValuesObjectException
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
     * @throws ValuesObjectException
     */
    private function _validateRoleIdValue(): void
    {
        if (empty($this->getRoleId())) {
            $this->setError(UserForm::ROLE_IS_NOT_SET_ERROR_MESSAGE);
            $this->setStatusFail();
        }
    }
}
