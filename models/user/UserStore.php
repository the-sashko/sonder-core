<?php

namespace Sonder\Models\User;

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
        bool $excludeRemoved = false,
        bool $excludeInactive = false
    ): ?array
    {
        if (empty($id)) {
            return null;
        }

        $sqlWhere = sprintf('WHERE "id" = \'%d\'', $id);

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
            %s
            LIMIT 1;
        ';

        $sql = sprintf($sql, UserStore::USERS_TABLE, $sqlWhere);

        return $this->getRow($sql);
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
        bool    $excludeRemoved = false,
        bool    $excludeInactive = false
    ): ?array
    {
        if (empty($login)) {
            return null;
        }

        $sqlWhere = sprintf('WHERE "login" = \'%s\'', $login);

        if (empty($excludeId)) {
            $sqlWhere = sprintf('%s AND "id" <> %d', $sqlWhere, $excludeId);
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
            %s
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
        bool $excludeRemoved = false,
        bool $excludeInactive = false
    ): ?array
    {
        $sqlWhere = 'WHERE true';

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
            %s
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
            WHERE
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
            %s
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
            WHERE
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
            %s
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
            WHERE
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
            %s
            LIMIT 1;
        ';

        $sql = sprintf($sql, UserStore::USERS_TABLE, $sqlWhere);

        return $this->getRow($sql);
    }

    /**
     * @param array|null $webToken
     * @param int|null $id
     * @return bool
     * @throws DatabasePluginException
     */
    final public function updateWebTokenById(
        ?string $webToken = null,
        ?int    $id = null
    ): bool
    {
        if (empty($id)) {
            return false;
        }

        $row = [
            'web_token' => $webToken
        ];

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
}
