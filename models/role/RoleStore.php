<?php

namespace Sonder\Models\Role;

use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IModelStore;
use Sonder\Core\ModelStore;
use Sonder\Models\Role\Interfaces\IRoleStore;
use Sonder\Models\Role\Interfaces\IRoleValuesObject;
use Sonder\Models\Role\Interfaces\IRoleActionValuesObject;

#[IModelStore]
#[IRoleStore]
final class RoleStore extends ModelStore implements IRoleStore
{
    final protected const SCOPE = 'role';

    private const ROLES_TABLE = 'roles';
    private const ROLE_ACTIONS_TABLE = 'role_actions';
    private const ROLE_TO_ACTIONS_TABLE = 'role2action';

    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    final public function getRoleRowById(
        ?int $id = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array {
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

        $sql = sprintf($sql, RoleStore::ROLES_TABLE, $sqlWhere);

        return $this->getRow($sql);
    }

    /**
     * @param string|null $name
     * @param int|null $excludeId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    final public function getRoleRowByName(
        ?string $name = null,
        ?int $excludeId = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array {
        if (empty($name)) {
            return null;
        }

        $sqlWhere = sprintf('"name" = \'%s\'', $name);

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
            WHERE %s
            LIMIT 1;
        ';

        $sql = sprintf($sql, RoleStore::ROLES_TABLE, $sqlWhere);

        return $this->getRow($sql);
    }

    /**
     * @param int $page
     * @param int $limit
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    final public function getRoleRowsByPage(
        int $page = 1,
        int $limit = 10,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array {
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

        $offset = $limit * ($page - 1);

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
            RoleStore::ROLES_TABLE,
            $sqlWhere,
            $limit,
            $offset
        );

        return $this->getRows($sql);
    }

    /**
     * @return array|null
     */
    final public function getAllRoleRows(): ?array
    {
        $sqlWhere = '
            ("ddate" IS NULL OR "ddate" < 1) AND
            "is_active" = true
        ';

        $sql = '
            SELECT *
            FROM "%s"
            WHERE %s
            ORDER BY "cdate" DESC;
        ';

        $sql = sprintf(
            $sql,
            RoleStore::ROLES_TABLE,
            $sqlWhere
        );

        return $this->getRows($sql);
    }

    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    final public function getRoleActionRowById(
        ?int $id = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array {
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

        $sql = sprintf($sql, RoleStore::ROLE_ACTIONS_TABLE, $sqlWhere);

        return $this->getRow($sql);
    }

    /**
     * @param int|null $roleId
     * @return array|null
     */
    final public function getAllowedActionRowsByRoleId(
        ?int $roleId = null
    ): ?array {
        if (empty($roleId)) {
            return null;
        }

        return $this->getActionRowsByRoleId($roleId, true);
    }

    /**
     * @param int|null $roleId
     * @return array|null
     */
    final public function getDeniedActionRowsByRoleId(
        ?int $roleId = null
    ): ?array {
        if (empty($roleId)) {
            return null;
        }

        return $this->getActionRowsByRoleId($roleId, false);
    }

    /**
     * @param int|null $roleId
     * @param bool|null $isAllowed
     * @return array|null
     */
    final public function getActionRowsByRoleId(
        ?int $roleId = null,
        ?bool $isAllowed = null
    ): ?array {
        if (empty($roleId) || is_null($isAllowed)) {
            return null;
        }

        $isAllowed = $isAllowed ? 'true' : 'false';

        $sql = '
            SELECT
                "actions"."name" AS "name"
            FROM "%s" AS "actions"
            LEFT JOIN "%s" AS "role2action"
                ON "role2action"."action_id" = "actions"."id"
            WHERE
                  "role2action"."role_id" = %d AND
                  ("actions"."ddate" IS NULL OR "actions"."ddate" < 1) AND
                  "actions"."is_active" = true AND
                  "role2action"."is_allowed" = %s
            ORDER BY "actions"."name";
        ';

        $sql = sprintf(
            $sql,
            RoleStore::ROLE_ACTIONS_TABLE,
            RoleStore::ROLE_TO_ACTIONS_TABLE,
            $roleId,
            $isAllowed
        );

        $rows = $this->getRows($sql);

        if (empty($rows)) {
            return null;
        }

        return array_map(function ($row) {
            return $row['name'];
        }, $rows);
    }

    /**
     * @param string|null $name
     * @param int|null $excludeId
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     */
    final public function getRoleActionRowByName(
        ?string $name = null,
        ?int $excludeId = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array {
        if (empty($name)) {
            return null;
        }

        $sqlWhere = sprintf('"name" = \'%s\'', $name);

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

        $sql = sprintf($sql, RoleStore::ROLE_ACTIONS_TABLE, $sqlWhere);

        return $this->getRow($sql);
    }

    /**
     * @param string|null $name
     * @return int|null
     */
    final public function getRoleActionIdByName(?string $name = null): ?int
    {
        if (empty($name)) {
            return null;
        }

        $sqlWhere = sprintf('"name" = \'%s\'', $name);

        $sql = '
            SELECT "id"
            FROM "%s"
            WHERE %s
            LIMIT 1;
        ';

        $sql = sprintf($sql, RoleStore::ROLE_ACTIONS_TABLE, $sqlWhere);

        $id = $this->getOne($sql);

        return empty($id) ? null : (int)$id;
    }

    /**
     * @param string|null $name
     * @return int|null
     */
    final public function getRoleIdByName(?string $name = null): ?int
    {
        if (empty($name)) {
            return null;
        }

        $sqlWhere = sprintf('"name" = \'%s\'', $name);

        $sql = '
            SELECT "id"
            FROM "%s"
            WHERE %s
            LIMIT 1;
        ';

        $sql = sprintf($sql, RoleStore::ROLES_TABLE, $sqlWhere);

        $id = $this->getOne($sql);

        return empty($id) ? null : (int)$id;
    }

    /**
     * @param int $page
     * @param int $limit
     * @return array|null
     */
    final public function getRoleActionRowsByPage(
        int $page = 1,
        int $limit = 10
    ): ?array {
        $offset = $limit * ($page - 1);

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
            $limit,
            $offset
        );

        return $this->getRows($sql);
    }

    /**
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     */
    final public function getRoleActionRowsCount(
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int {
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

        $sql = sprintf($sql, RoleStore::ROLE_ACTIONS_TABLE, $sqlWhere);

        return (int)$this->getOne($sql);
    }

    /**
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     */
    final public function getRoleRowsCount(
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int {
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

        $sql = sprintf($sql, RoleStore::ROLES_TABLE, $sqlWhere);

        return (int)$this->getOne($sql);
    }

    /**
     * @return array|null
     */
    final public function getAllRoleActionRows(): ?array
    {
        $sqlWhere = '
            ("ddate" IS NULL OR "ddate" < 1) AND
            "is_active" = true
        ';

        $sql = '
            SELECT *
            FROM "%s"
            WHERE %s
            ORDER BY "cdate" DESC;
        ';

        $sql = sprintf(
            $sql,
            RoleStore::ROLE_ACTIONS_TABLE,
            $sqlWhere
        );

        return $this->getRows($sql);
    }

    /**
     * @param int|null $id
     * @param bool $isSoftDelete
     * @return bool
     */
    final public function deleteRoleById(
        ?int $id = null,
        bool $isSoftDelete = true
    ): bool {
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
     * @param bool $isSoftDelete
     * @return bool
     */
    final public function deleteRoleActionById(
        ?int $id = null,
        bool $isSoftDelete = true
    ): bool {
        if (empty($id)) {
            return false;
        }

        $condition = sprintf('"id" = %d AND "is_system" = false', $id);

        if ($isSoftDelete) {
            $row = [
                'ddate' => time(),
                'is_active' => false
            ];

            return $this->updateRoleAction($row, $condition);
        }

        return $this->deleteRoleAction($condition);
    }

    /**
     * @param int|null $id
     * @return bool
     */
    final public function restoreRoleActionById(?int $id = null): bool
    {
        if (empty($id)) {
            return false;
        }

        $condition = sprintf('"id" = %d', $id);

        $row = [
            'ddate' => null,
            'is_active' => true
        ];

        return $this->updateRoleAction($row, $condition);
    }

    /**
     * @param array|null $row
     * @param string|null $condition
     * @return bool
     */
    final public function updateRoleAction(
        ?array $row = null,
        ?string $condition = null
    ): bool {
        if (empty($row) || empty($condition)) {
            return false;
        }

        return $this->updateRows(
            RoleStore::ROLE_ACTIONS_TABLE,
            $row,
            $condition
        );
    }

    /**
     * @param string|null $condition
     * @return bool
     */
    final public function deleteRoleAction(?string $condition = null): bool
    {
        if (empty($condition)) {
            return false;
        }

        return $this->deleteRows(
            RoleStore::ROLE_ACTIONS_TABLE,
            $condition
        );
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
            'ddate' => null,
            'is_active' => true
        ];

        return $this->updateRoleById($row, $id);
    }

    /**
     * @param IRoleActionValuesObject|null $roleActionVO
     * @return bool
     * @throws ValuesObjectException
     */
    final public function insertOrUpdateRoleAction(
        ?IRoleActionValuesObject $roleActionVO = null
    ): bool {
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
     */
    final public function insertRoleAction(?array $row = null): bool
    {
        if (empty($row)) {
            return false;
        }

        return $this->addRow(RoleStore::ROLE_ACTIONS_TABLE, $row);
    }

    /**
     * @param int|null $roleId
     * @param int|null $roleActionId
     * @param bool $isAllowed
     * @return bool
     */
    final public function insertRoleToRoleAction(
        ?int $roleId = null,
        ?int $roleActionId = null,
        bool $isAllowed = true
    ): bool {
        if (empty($roleId) || empty($roleActionId)) {
            return false;
        }

        $row = [
            'role_id' => $roleId,
            'action_id' => $roleActionId,
            'is_allowed' => $isAllowed ? 'true' : 'false'
        ];

        return $this->addRow(RoleStore::ROLE_TO_ACTIONS_TABLE, $row);
    }

    /**
     * @param int|null $roleId
     * @return bool
     */
    final public function deleteRoleToRoleActionByRoleId(
        ?int $roleId = null
    ): bool {
        if (empty($roleId)) {
            return false;
        }

        $condition = sprintf('"role_id" = %d', $roleId);

        return $this->deleteRows(RoleStore::ROLE_TO_ACTIONS_TABLE, $condition);
    }

    /**
     * @param array|null $row
     * @return bool
     */
    final public function insertRole(?array $row = null): bool
    {
        if (empty($row)) {
            return false;
        }

        return $this->addRow(RoleStore::ROLES_TABLE, $row);
    }

    /**
     * @param array|null $row
     * @param int|null $id
     * @return bool
     */
    final public function updateRoleActionById(
        ?array $row = null,
        ?int $id = null
    ): bool {
        if (empty($row) || empty($id)) {
            return false;
        }

        return $this->updateRowById(
            RoleStore::ROLE_ACTIONS_TABLE,
            $row,
            $id
        );
    }

    /**
     * @param array|null $row
     * @param int|null $id
     * @return bool
     */
    final public function updateRoleById(
        ?array $row = null,
        ?int $id = null
    ): bool {
        if (empty($row) || empty($id)) {
            return false;
        }

        return $this->updateRowById(RoleStore::ROLES_TABLE, $row, $id);
    }

    /**
     * @param IRoleValuesObject|null $roleVO
     * @return bool
     * @throws ValuesObjectException
     */
    final public function insertOrUpdateRole(
        ?IRoleValuesObject $roleVO = null
    ): bool {
        $id = $roleVO->getId();

        if (empty($id)) {
            $roleVO->setCdate();

            return $this->insertRole($roleVO->exportRow());
        }

        $roleVO->setMdate();

        return $this->updateRoleById($roleVO->exportRow(), $id);
    }
}
