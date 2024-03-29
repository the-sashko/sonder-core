<?php

namespace Sonder\Models\User\Forms;

use Sonder\Core\ModelFormObject;
use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IModelFormObject;
use Sonder\Models\User\Interfaces\ICredentialsForm;

#[IModelFormObject]
#[ICredentialsForm]
final class CredentialsForm extends ModelFormObject implements ICredentialsForm
{
    final public const LOGIN_EMPTY_ERROR_MESSAGE = 'Login is empty';

    final public const LOGIN_TOO_SHORT_ERROR_MESSAGE = 'Login is too short';

    final public const LOGIN_TOO_LONG_ERROR_MESSAGE = 'Login is too long';

    final public const LOGIN_EXISTS_ERROR_MESSAGE = 'User with this login already exists';

    final public const PASSWORD_EMPTY_ERROR_MESSAGE = 'Password can not be empty when login was changed';

    final public const PASSWORD_TOO_SHORT_ERROR_MESSAGE = 'Password is too short';

    final public const PASSWORD_TOO_LONG_ERROR_MESSAGE = 'Password is too long';

    final public const USER_NOT_EXISTS_ERROR_MESSAGE = 'User not exists';

    private const LOGIN_MIN_LENGTH = 3;

    private const LOGIN_MAX_LENGTH = 255;

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
     * @param string|null $login
     * @return void
     * @throws ValuesObjectException
     */
    final public function setLogin(?string $login = null): void
    {
        $this->set('login', $login);
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
     * @return void
     * @throws ValuesObjectException
     */
    private function _validateLoginValue(): void
    {
        $login = $this->getLogin();

        if (empty($login)) {
            $this->setError(CredentialsForm::LOGIN_EMPTY_ERROR_MESSAGE);
            $this->setStatusFail();
        }

        if (
            !empty($login) &&
            mb_strlen($login) > CredentialsForm::LOGIN_MAX_LENGTH
        ) {
            $this->setError(CredentialsForm::LOGIN_TOO_LONG_ERROR_MESSAGE);
            $this->setStatusFail();
        }

        if (
            !empty($login) &&
            mb_strlen($login) < CredentialsForm::LOGIN_MIN_LENGTH
        ) {
            $this->setError(CredentialsForm::LOGIN_TOO_SHORT_ERROR_MESSAGE);
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

        if (
            !empty($password) &&
            mb_strlen($password) > CredentialsForm::PASSWORD_MAX_LENGTH
        ) {
            $this->setError(
                CredentialsForm::PASSWORD_TOO_LONG_ERROR_MESSAGE
            );

            $this->setStatusFail();
        }

        if (
            !empty($password) &&
            mb_strlen($password) < CredentialsForm::PASSWORD_MIN_LENGTH
        ) {
            $this->setError(
                CredentialsForm::PASSWORD_TOO_SHORT_ERROR_MESSAGE
            );

            $this->setStatusFail();
        }
    }
}
