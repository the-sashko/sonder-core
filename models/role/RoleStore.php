<?php

namespace Sonder\Models\Role;

use Sonder\Core\Interfaces\IModelStore;
use Sonder\Core\ModelStore;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;

final class RoleStore extends ModelStore implements IModelStore
{
    const ROLES_TABLE = 'roles';
    const ROLE_ACTIONS_TABLE = 'role_actions';
    const ROLE_TO_ACTIONS_TABLE = 'role2action';

    /**
     * @var string|null
     */
    public ?string $scope = 'role';

    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getRoleRowById(
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

        $sql = sprintf($sql, RoleStore::ROLES_TABLE, $sqlWhere);

        return $this->getRow($sql);
    }

    /**
     * @param string|null $name
     * @param int|null $excludeId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getRoleRowByName(
        ?string $name = null,
        ?int    $excludeId = null,
        bool    $excludeRemoved = false,
        bool    $excludeInactive = false
    ): ?array
    {
        if (empty($name)) {
            return null;
        }

        $sqlWhere = sprintf('WHERE "name" = \'%s\'', $name);

        if (!empty($excludeId)) {
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

        $sql = sprintf($sql, RoleStore::ROLES_TABLE, $sqlWhere);

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
    final public function getAllRoleRows(
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
            RoleStore::ROLES_TABLE,
            $sqlWhere,
            $itemsOnPage,
            $offset
        );

        return $this->getRows($sql);
    }

    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getRoleActionRowById(
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

        $sql = sprintf($sql, RoleStore::ROLE_ACTIONS_TABLE, $sqlWhere);

        return $this->getRow($sql);
    }

    /**
     * @param string|null $name
     * @param int|null $excludeId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getRoleActionRowByName(
        ?string $name = null,
        ?int    $excludeId = null,
        bool    $excludeRemoved = false,
        bool    $excludeInactive = false
    ): ?array
    {
        if (empty($name)) {
            return null;
        }

        $sqlWhere = sprintf('WHERE "name" = \'%s\'', $name);

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
            %s
            LIMIT 1;
        ';

        $sql = sprintf($sql, RoleStore::ROLE_ACTIONS_TABLE, $sqlWhere);

        return $this->getRow($sql);
    }

    /**
     * @param int $page
     * @param int $itemsOnPage
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getRoleActionRowsByPage(
        int $page = 1,
        int $itemsOnPage = 10
    ): ?array
    {
        $offset = $itemsOnPage * ($page - 1);

        $sql = '
            SELECT *
            FROM "%s"
            ORDER BY "cdate" DESC
            LIMIT %d
            OFFSET %d;
        ';

        $sql = sprintf(
            $sql,
            RoleStore::ROLE_ACTIONS_TABLE,
            $itemsOnPage,
            $offset
        );

        return $this->getRows($sql);
    }

    final public function getRoleActionRowsCount(
        int $page = 1,
        int $itemsOnPage = 10
    ): int
    {
        $offset = $itemsOnPage * ($page - 1);

        $sql = '
            SELECT COUNT(*) AS "count"
            FROM "%s";
        ';

        $sql = sprintf(
            $sql,
            RoleStore::ROLE_ACTIONS_TABLE,
            $itemsOnPage,
            $offset
        );

        return (int)$this->getOne($sql);
    }

    /**
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getAllActionRows(): ?array
    {
        $sqlWhere = '
            WHERE
                 ("ddate" IS NULL OR "ddate" < 1) AND
                 "is_active" = true
        ';

        $sql = '
            SELECT *
            FROM "%s"
            %s
            ORDER BY "id" DESC;
        ';

        $sql = sprintf(
            $sql,
            RoleStore::ROLE_ACTIONS_TABLE,
            $sqlWhere
        );

        return $this->getRows($sql);
    }

    /**
     * @param array|null $row
     * @param int|null $id
     * @return bool
     * @throws DatabasePluginException
     */
    final public function updateRolaById(
        ?array $row = null,
        ?int   $id = null
    ): bool
    {
        if (empty($row) || empty($id)) {
            return false;
        }

        return $this->updateRowById(RoleStore::ROLES_TABLE, $row, $id);
    }

    /**
     * @param int|null $id
     * @param bool $isSoftDelete
     * @return bool
     * @throws DatabasePluginException
     */
    final public function deleteRoleById(
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

            return $this->updateRoleById($row, $id);
        }

        return $this->deleteRowById(RoleStore::ROLES_TABLE, $id);
    }

    /**
     * @param int|null $id
     * @return bool
     */
    final public function restoreRoleById(?int $id = null): bool
    {
        if (empty($id)) {
            return false;
        }

        $row = [
            'ddate' => NULL,
            'is_active' => true
        ];

        return $this->updateRoleById($row, $id);
    }

    /**
     * @param RoleActionValuesObject|null $roleActionVO
     * @return bool
     * @throws DatabasePluginException
     */
    final public function insertOrUpdateRoleAction(
        ?RoleActionValuesObject $roleActionVO = null
    ): bool
    {
        $id = $roleActionVO->getId();

        if (empty($id)) {
            $roleActionVO->setCdate();

            return $this->insertRoleAction($roleActionVO->exportRow());
        }

        $roleActionVO->setMdate();

        return $this->updateRoleActionById($roleActionVO->exportRow(), $id);
    }

    /**
     * @param array|null $row
     * @return bool
     * @throws DatabasePluginException
     */
    final public function insertRoleAction(?array $row = null): bool
    {
        if (empty($row)) {
            return false;
        }

        return $this->addRow(RoleStore::ROLE_ACTIONS_TABLE, $row);
    }

    /**
     * @param array|null $row
     * @param int|null $id
     * @return bool
     * @throws DatabasePluginException
     */
    final public function updateRoleActionById(
        ?array $row = null,
        ?int   $id = null
    ): bool
    {
        if (empty($row) || empty($id)) {
            return false;
        }

        return $this->updateRowById(
            RoleStore::ROLE_ACTIONS_TABLE,
            $row,
            $id
        );
    }
}
