<?php

namespace Sonder\Models\User;

use Exception;
use Sonder\Core\Interfaces\IModelStore;
use Sonder\Core\ModelStore;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;

final class UserStore extends ModelStore implements IModelStore
{
    const USERS_TABLE = 'users';

    /**
     * @var string|null
     */
    public ?string $scope = 'user';

    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getUserRowById(
        ?int $id = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array
    {
        if (empty($id)) {
            return null;
        }

        $sqlWhere = sprintf('"id" = \'%d\'', $id);

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '%s AND ("ddate" IS NULL OR "ddate" < 1)',
                $sqlWhere
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf('%s AND "is_active" = true', $sqlWhere);
        }

        $sql = '
            SELECT *
            FROM "%s"
            WHERE %s
            LIMIT 1;
        ';

        $sql = sprintf($sql, UserStore::USERS_TABLE, $sqlWhere);

        return $this->getRow($sql);
    }

    /**
     * @param string|null $login
     * @return int|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getUserIdByLogin(?string $login = null): ?int
    {
        if (empty($login)) {
            return null;
        }

        $sqlWhere = sprintf('"login" = \'%s\'', $login);

        $sql = '
            SELECT "id"
            FROM "%s"
            WHERE %s
            LIMIT 1;
        ';

        $sql = sprintf($sql, UserStore::USERS_TABLE, $sqlWhere);

        $id = $this->getOne($sql);

        return empty($id) ? null : (int)$id;
    }

    /**
     * @param string|null $login
     * @param int|null $excludeId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getUserRowByLogin(
        ?string $login = null,
        ?int    $excludeId = null,
        bool    $excludeRemoved = true,
        bool    $excludeInactive = true
    ): ?array
    {
        if (empty($login)) {
            return null;
        }

        $sqlWhere = sprintf('"login" = \'%s\'', $login);

        if (!empty($excludeId)) {
            $sqlWhere = sprintf(
                '%s AND "id" <> %d',
                $sqlWhere,
                $excludeId
            );
        }

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '%s AND ("ddate" IS NULL OR "ddate" < 1)',
                $sqlWhere
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf('%s AND "is_active" = true', $sqlWhere);
        }

        $sql = '
            SELECT *
            FROM "%s"
            WHERE %s
            LIMIT 1;
        ';

        $sql = sprintf($sql, UserStore::USERS_TABLE, $sqlWhere);

        return $this->getRow($sql);
    }

    /**
     * @param string|null $email
     * @param int|null $excludeId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getUserRowByEmail(
        ?string $email = null,
        ?int    $excludeId = null,
        bool    $excludeRemoved = true,
        bool    $excludeInactive = true
    ): ?array
    {
        if (empty($email)) {
            return null;
        }

        $sqlWhere = sprintf('"email" = \'%s\'', $email);

        if (!empty($excludeId)) {
            $sqlWhere = sprintf(
                '%s AND "id" <> %d',
                $sqlWhere,
                $excludeId
            );
        }

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '%s AND ("ddate" IS NULL OR "ddate" < 1)',
                $sqlWhere
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf('%s AND "is_active" = true', $sqlWhere);
        }

        $sql = '
            SELECT *
            FROM "%s"
            WHERE %s
            LIMIT 1;
        ';

        $sql = sprintf($sql, UserStore::USERS_TABLE, $sqlWhere);

        return $this->getRow($sql);
    }

    /**
     * @param int $page
     * @param int $itemsOnPage
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getAllUsers(
        int  $page = 1,
        int  $itemsOnPage = 10,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array
    {
        $sqlWhere = 'true';

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '%s AND ("ddate" IS NULL OR "ddate" < 1)',
                $sqlWhere
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf('%s AND "is_active" = true', $sqlWhere);
        }

        $offset = $itemsOnPage * ($page - 1);

        $sql = '
            SELECT *
            FROM "%s"
            WHERE %s
            ORDER BY "cdate" DESC
            LIMIT %d
            OFFSET %d;
        ';

        $sql = sprintf(
            $sql,
            UserStore::USERS_TABLE,
            $sqlWhere,
            $itemsOnPage,
            $offset
        );

        return $this->getRows($sql);
    }

    /**
     * @param string|null $apiToken
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getRowByApiToken(?string $apiToken = null): ?array
    {
        if (empty($apiToken)) {
            return null;
        }

        $sqlWhere = '
            "api_token" = \'%s\' AND
            "is_active" = true AND
            (
                "ddate" < 1 OR
                "ddate" IS NULL
            )
        ';

        $sqlWhere = sprintf($sqlWhere, $apiToken);

        $sql = '
            SELECT *
            FROM "%s"
            WHERE %s
            LIMIT 1;
        ';

        $sql = sprintf($sql, UserStore::USERS_TABLE, $sqlWhere);

        return $this->getRow($sql);
    }

    /**
     * @param string|null $webToken
     * @param int|null $id
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getRowByWebTokenAndId(
        ?string $webToken = null,
        ?int    $id = null,
    ): ?array
    {
        if (empty($webToken) || empty($id)) {
            return null;
        }

        $sqlWhere = '
            "id" = \'%d\' AND
            "web_token" = \'%s\' AND
            "is_active" = true AND
            (
                "ddate" < 1 OR
                "ddate" IS NULL
            )
        ';

        $sqlWhere = sprintf($sqlWhere, $id, $webToken);

        $sql = '
            SELECT *
            FROM "%s"
            WHERE %s
            LIMIT 1;
        ';

        $sql = sprintf($sql, UserStore::USERS_TABLE, $sqlWhere);

        return $this->getRow($sql);
    }

    /**
     * @param string|null $login
     * @param string|null $passwordHash
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getRowByLoginAndPasswordHash(
        ?string $login = null,
        ?string $passwordHash = null
    ): ?array
    {
        if (empty($login) || empty($passwordHash)) {
            return null;
        }

        $sqlWhere = '
            "login" = \'%s\' AND
            "password_hash" = \'%s\' AND
            "is_active" = true AND
            (
                "ddate" < 1 OR
                "ddate" IS NULL
            )
        ';

        $sqlWhere = sprintf($sqlWhere, $login, $passwordHash);

        $sql = '
            SELECT *
            FROM "%s"
            WHERE %s
            LIMIT 1;
        ';

        $sql = sprintf($sql, UserStore::USERS_TABLE, $sqlWhere);

        return $this->getRow($sql);
    }

    /**
     * @param string|null $webToken
     * @param int|null $id
     * @param int|null $lastLoginDate
     * @return bool
     * @throws DatabasePluginException
     */
    final public function updateWebTokenById(
        ?string $webToken = null,
        ?int    $id = null,
        ?int    $lastLoginDate = null
    ): bool
    {
        if (empty($id)) {
            return false;
        }

        $row = [
            'web_token' => $webToken
        ];

        if (!empty($lastLoginDate)) {
            $row['last_login_date'] = $lastLoginDate;
        }

        return $this->updateUserById($row, $id);
    }

    /**
     * @param array|null $row
     * @param int|null $id
     * @return bool
     * @throws DatabasePluginException
     */
    final public function updateUserById(
        ?array $row = null,
        ?int   $id = null
    ): bool
    {
        if (empty($row) || empty($id)) {
            return false;
        }

        return $this->updateRowById(UserStore::USERS_TABLE, $row, $id);
    }

    /**
     * @param int|null $id
     * @param bool $isSoftDelete
     * @return bool
     * @throws DatabasePluginException
     */
    final public function deleteUserById(
        ?int $id = null,
        bool $isSoftDelete = true
    ): bool
    {
        if (empty($id)) {
            return false;
        }

        if ($isSoftDelete) {
            $row = [
                'ddate' => time(),
                'is_active' => false
            ];

            return $this->updateUserById($row, $id);
        }

        return $this->deleteRowById(UserStore::USERS_TABLE, $id);
    }

    /**
     * @param int|null $id
     * @return bool
     * @throws DatabasePluginException
     */
    final public function restoreUserById(?int $id = null): bool
    {
        if (empty($id)) {
            return false;
        }

        $row = [
            'ddate' => NULL,
            'is_active' => true
        ];

        return $this->updateUserById($row, $id);
    }

    /**
     * @param int $page
     * @param int $itemsOnPage
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getUserRowsByPage(
        int  $page = 1,
        int  $itemsOnPage = 10,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array
    {
        $sqlWhere = 'true';

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '%s AND ("ddate" IS NULL OR "ddate" < 1)',
                $sqlWhere
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf('%s AND "is_active" = true', $sqlWhere);
        }

        $offset = $itemsOnPage * ($page - 1);

        $sql = '
            SELECT *
            FROM "%s"
            WHERE %s
            ORDER BY "cdate" DESC
            LIMIT %d
            OFFSET %d;
        ';

        $sql = sprintf(
            $sql,
            UserStore::USERS_TABLE,
            $sqlWhere,
            $itemsOnPage,
            $offset
        );

        return $this->getRows($sql);
    }

    /**
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getUserRowsCount(
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int
    {
        $sqlWhere = 'true';

        if ($excludeRemoved) {
            $sqlWhere = sprintf(
                '%s AND ("ddate" IS NULL OR "ddate" < 1)',
                $sqlWhere
            );
        }

        if ($excludeInactive) {
            $sqlWhere = sprintf('%s AND "is_active" = true', $sqlWhere);
        }

        $sql = '
            SELECT COUNT("id") AS "count"
            FROM "%s"
            WHERE %s;
        ';

        $sql = sprintf($sql, UserStore::USERS_TABLE, $sqlWhere);

        return (int)$this->getOne($sql);
    }

    /**
     * @param UserValuesObject|null $userVO
     * @return bool
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function insertOrUpdateUser(
        ?UserValuesObject $userVO = null
    ): bool
    {
        $id = $userVO->getId();

        $apiToken = $userVO->getApiToken();
        $passwordHash = $userVO->getPasswordHash();

        if (empty($id)) {
            $userVO->setCdate();

            $row = $userVO->exportRow();

            if (!empty($apiToken)) {
                $row['api_token'] = $apiToken;
            }

            if (!empty($passwordHash)) {
                $row['password_hash'] = $passwordHash;
            }

            return $this->insertUser($row);
        }

        $userVO->setMdate();

        $row = $userVO->exportRow();

        if (!empty($apiToken)) {
            $row['api_token'] = $apiToken;
        }

        if (!empty($passwordHash)) {
            $row['password_hash'] = $passwordHash;
        }

        return $this->updateUserById($row, $id);
    }

    /**
     * @param array|null $row
     * @return bool
     * @throws DatabasePluginException
     */
    final public function insertUser(?array $row = null): bool
    {
        if (empty($row)) {
            return false;
        }

        return $this->addRow(UserStore::USERS_TABLE, $row);
    }
}
