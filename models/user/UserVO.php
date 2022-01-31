<?php

namespace Sonder\Models\User;

use Exception;
use Sonder\Core\Interfaces\IRoleValuesObject;
use Sonder\Core\ModelValuesObject;

final class UserValuesObject extends ModelValuesObject
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
    protected ?string $adminCredentialsLinkPattern = '/admin/users/' .
    'credentials/%d/';

    /**
     * @return string
     * @throws Exception
     */
    final public function getLogin(): string
    {
        return (string)$this->get('login');
    }

    /**
     * @return string
     * @throws Exception
     */
    final public function getEmail(): string
    {
        return (string)$this->get('email');
    }

    /**
     * @return int
     * @throws Exception
     */
    final public function getRoleId(): int
    {
        return (int)$this->get('role_id');
    }

    /**
     * @return IRoleValuesObject|null
     * @throws Exception
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
     * @throws Exception
     */
    final public function getPasswordHash(): string
    {
        return (string)$this->get('password_hash');
    }

    /**
     * @return string|null
     * @throws Exception
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
     * @throws Exception
     */
    final public function getWebToken(): string
    {
        return (string)$this->get('web_token');
    }

    /**
     * @param string|null $format
     * @return string|int|null
     * @throws Exception
     */
    final public function getLastLoginDate(
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
     * @throws Exception
     */
    final public function getAdminViewLink(): string
    {
        return sprintf($this->adminViewLinkPattern, $this->getId());
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getAdminCredentialsLink(): string
    {
        return sprintf($this->adminCredentialsLinkPattern, $this->getId());
    }

    /**
     * @param string|null $login
     * @return void
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
     */
    final public function setApiToken(?string $apiToken = null): void
    {
        $this->set('api_token', $apiToken);
    }

    /**
     * @param string|null $webToken
     * @return void
     * @throws Exception
     */
    final public function setWebToken(?string $webToken = null): void
    {
        if (!empty($apiToken)) {
            $this->set('web_token', $webToken);
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    final public function setLastLoginDate(): void
    {
        $this->set('last_login_date', time());
    }

    /**
     * @param array|null $params
     * @return array|null
     */
    final public function exportRow(?array $params = null): ?array
    {
        $row = parent::exportRow($params);

        if (empty($row)) {
            return null;
        }

        if (array_key_exists('role', $row) && empty($row['role'])) {
            unset($row['role']);
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
