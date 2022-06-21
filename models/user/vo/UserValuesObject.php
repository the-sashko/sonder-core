<?php

namespace Sonder\Models\User\ValuesObjects;

use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IModelSimpleValuesObject;
use Sonder\Interfaces\IModelValuesObject;
use Sonder\Core\ModelValuesObject;
use Sonder\Interfaces\IValuesObject;
use Sonder\Models\Role\Interfaces\IRoleValuesObject;
use Sonder\Models\User\Interfaces\IUserValuesObject;

#[IValuesObject]
#[IModelSimpleValuesObject]
#[IModelValuesObject]
#[IUserValuesObject]
final class UserValuesObject
    extends ModelValuesObject
    implements IUserValuesObject
{
    /**
     * @var string|null
     */
    protected ?string $editLinkPattern = '/admin/user/%d/';

    /**
     * @var string|null
     */
    protected ?string $removeLinkPattern = '/admin/users/remove/%d/';

    /**
     * @var string|null
     */
    protected ?string $restoreLinkPattern = '/admin/users/restore/%d/';

    /**
     * @var string|null
     */
    protected ?string $adminViewLinkPattern = '/admin/users/view/%d/';

    /**
     * @var string|null
     */
    protected ?string $adminCredentialsLinkPattern = '/admin/users/credentials/%d/';

    /**
     * @return string
     * @throws ValuesObjectException
     */
    final public function getLogin(): string
    {
        return (string)$this->get('login');
    }

    /**
     * @return string
     * @throws ValuesObjectException
     */
    final public function getEmail(): string
    {
        return (string)$this->get('email');
    }

    /**
     * @return int
     * @throws ValuesObjectException
     */
    final public function getRoleId(): int
    {
        return (int)$this->get('role_id');
    }

    /**
     * @return IRoleValuesObject|null
     * @throws ValuesObjectException
     */
    final public function getRoleVO(): ?IRoleValuesObject
    {
        if (!$this->has('role_vo')) {
            return null;
        }

        return $this->get('role_vo');
    }

    /**
     * @return string
     * @throws ValuesObjectException
     */
    final public function getPasswordHash(): string
    {
        return (string)$this->get('password_hash');
    }

    /**
     * @return string|null
     * @throws ValuesObjectException
     */
    final public function getApiToken(): ?string
    {
        if (!$this->has('api_token')) {
            return null;
        }

        return (string)$this->get('api_token');
    }

    /**
     * @return string
     * @throws ValuesObjectException
     */
    final public function getWebToken(): string
    {
        return (string)$this->get('web_token');
    }

    /**
     * @param string|null $format
     * @return string|int|null
     * @throws ValuesObjectException
     */
    final public function getLastSignInDate(
        ?string $format = null
    ): string|int|null
    {
        $ddate = (int)$this->get('last_login_date');

        if (empty($ddate)) {
            return null;
        }

        if (empty($format)) {
            return $ddate;
        }

        return date($format, $ddate);
    }

    /**
     * @return string
     * @throws ValuesObjectException
     */
    final public function getEditLink(): string
    {
        return sprintf($this->editLinkPattern, $this->getId());
    }

    /**
     * @return string
     * @throws ValuesObjectException
     */
    final public function getAdminViewLink(): string
    {
        return sprintf($this->adminViewLinkPattern, $this->getId());
    }

    /**
     * @return string
     * @throws ValuesObjectException
     */
    public function getAdminCredentialsLink(): string
    {
        return sprintf($this->adminCredentialsLinkPattern, $this->getId());
    }

    /**
     * @param string|null $login
     * @return void
     * @throws ValuesObjectException
     */
    final public function setLogin(?string $login = null): void
    {
        if (!empty($login)) {
            $this->set('login', $login);
        }
    }

    /**
     * @param string|null $email
     * @return void
     * @throws ValuesObjectException
     */
    final public function setEmail(?string $email = null): void
    {
        if (!empty($email)) {
            $this->set('email', $email);
        }
    }

    /**
     * @param int|null $roleId
     * @return void
     * @throws ValuesObjectException
     */
    final public function setRoleId(?int $roleId = null): void
    {
        if (!empty($roleId)) {
            $this->set('role_id', $roleId);
        }
    }

    /**
     * @param IRoleValuesObject|null $roleVO
     * @return void
     * @throws ValuesObjectException
     */
    final public function setRoleVO(?IRoleValuesObject $roleVO = null): void
    {
        if (!empty($roleVO)) {
            $this->set('role_vo', $roleVO);
        }
    }

    /**
     * @param string|null $passwordHash
     * @return void
     * @throws ValuesObjectException
     */
    final public function setPasswordHash(?string $passwordHash = null): void
    {
        if (!empty($passwordHash)) {
            $this->set('password_hash', $passwordHash);
        }
    }

    /**
     * @param string|null $apiToken
     * @return void
     * @throws ValuesObjectException
     */
    final public function setApiToken(?string $apiToken = null): void
    {
        $this->set('api_token', $apiToken);
    }

    /**
     * @param string|null $webToken
     * @return void
     * @throws ValuesObjectException
     */
    final public function setWebToken(?string $webToken = null): void
    {
        if (!empty($apiToken)) {
            $this->set('web_token', $webToken);
        }
    }

    /**
     * @return void
     * @throws ValuesObjectException
     */
    final public function setLastLoginDate(): void
    {
        $this->set('last_login_date', time());
    }

    /**
     * @return array
     */
    final public function exportRow(): array
    {
        $row = parent::exportRow();

        if (empty($row)) {
            return $row;
        }

        if (array_key_exists('role_vo', $row) && empty($row['role_vo'])) {
            unset($row['role_vo']);
        }

        if (array_key_exists('password_hash', $row)) {
            unset($row['password_hash']);
        }

        if (array_key_exists('web_token', $row)) {
            unset($row['web_token']);
        }

        if (array_key_exists('api_token', $row)) {
            unset($row['api_token']);
        }

        return $row;
    }
}
