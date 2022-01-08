<?php

namespace Sonder\Models\User;

use Exception;
use Sonder\Core\Interfaces\IRoleValuesObject;
use Sonder\Core\ValuesObject;

final class UserValuesObject extends ValuesObject
{
    /**
     * @return int
     * @throws Exception
     */
    final public function getId(): int
    {
        return (int)$this->get('id');
    }

    /**
     * @return string
     * @throws Exception
     */
    final public function getLogin(): string
    {
        return (string)$this->get('login');
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
    final public function getRole(): ?IRoleValuesObject
    {
        if (!$this->has('role')) {
            return null;
        }

        return $this->get('role');
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
     * @return string
     * @throws Exception
     */
    final public function getApiToken(): string
    {
        return (string)$this->get('api_token');
    }

    /**
     * @return string|null
     * @throws Exception
     */
    final public function getWebToken(): ?string
    {
        return (string)$this->get('web_token');
    }

    /**
     * @return bool
     * @throws Exception
     */
    final public function isActive(): bool
    {
        return (bool)$this->get('is_active');
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
     * @param IRoleValuesObject|null $role
     * @return void
     * @throws Exception
     */
    final public function setRole(?IRoleValuesObject $role = null): void
    {
        if (!empty($role)) {
            $this->set('role', $role);
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
        if (!empty($apiToken)) {
            $this->set('api_token', $apiToken);
        }
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
     * @param bool $isActive
     * @return void
     * @throws Exception
     */
    final public function setActive(bool $isActive = true): void
    {
        $this->set('is_active', $isActive);
    }

    final public function exportRow(?array $params = null): ?array
    {
        $row = parent::exportRow($params);

        if (empty($row)) {
            return null;
        }

        if (array_key_exists('role', $row) && empty($row['role'])) {
            $row['role'] = $this->getRole()->getName();
        }

        if (array_key_exists('password_hash', $row)) {
            unset($row['api_token']);
        }

        if (array_key_exists('web_token', $row)) {
            unset($row['web_token']);
        }

        if (array_key_exists('api_token', $row)) {
            unset($row['api_token']);
        }

        return $row;
    }

    //TODO: cdate, mdate, ddate, last_login_at
}
