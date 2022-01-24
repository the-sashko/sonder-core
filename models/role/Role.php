<?php

namespace Sonder\Models;

use Exception;
use Sonder\Core\CoreModel;
use Sonder\Core\Interfaces\IModel;
use Sonder\Core\Interfaces\IRole;
use Sonder\Core\Interfaces\IRoleValuesObject;
use Sonder\Core\ValuesObject;
use Sonder\Models\Role\RoleActionForm;
use Sonder\Models\Role\RoleActionValuesObject;
use Sonder\Models\Role\RoleForm;
use Sonder\Models\Role\RoleStore;
use Sonder\Models\Role\RoleValuesObject;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;
use Throwable;

/**
 * @property RoleStore $store
 */
final class Role extends CoreModel implements IModel, IRole
{
    /**
     * @var int
     */
    protected int $itemsOnPage = 10;

    /**
     * @param int|null $id
     * @return ValuesObject|null
     * @throws Exception
     */
    final public function getVOById(?int $id = null): ?ValuesObject
    {
        $row = $this->store->getRoleRowById($id);

        if (!empty($row)) {
            return $this->getVO($row);
        }

        return null;
    }

    /**
     * @param int $page
     * @return array|null
     * @throws Exception
     */
    final public function getRolesByPage(int $page): ?array
    {
        $rows = $this->store->getRoleRowsByPage($page, $this->itemsOnPage);

        if (empty($rows)) {
            return null;
        }

        return $this->getVOArray($rows);
    }

    /**
     * @param int|null $id
     * @return RoleActionValuesObject|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getRoleActionVOById(
        ?int $id = null
    ): ?RoleActionValuesObject
    {
        $row = $this->store->getRoleActionRowById($id);

        if (empty($row)) {
            return null;
        }

        return new RoleActionValuesObject($row);
    }

    /**
     * @param int|null $id
     * @return RoleValuesObject|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function getRoleVOById(?int $id = null): ?RoleValuesObject
    {
        $row = $this->store->getRoleRowById($id);

        if (empty($row)) {
            return null;
        }

        /* @var $roleVO RoleValuesObject */
        $roleVO = $this->getVO($row);

        return $roleVO;
    }

    /**
     * @param int $page
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getRoleActionsByPage(int $page): ?array
    {
        $rows = $this->store->getRoleActionRowsByPage(
            $page,
            $this->itemsOnPage
        );

        if (empty($rows)) {
            return null;
        }

        return array_map(function ($row) {
            return new RoleActionValuesObject($row);
        }, $rows);
    }

    /**
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getAllRoleActions(): ?array
    {
        $rows = $this->store->getAllRoleActionRows();

        if (empty($rows)) {
            return null;
        }

        return array_map(function ($row) {
            return new RoleActionValuesObject($row);
        }, $rows);
    }

    /**
     * @return array|null
     * @throws Exception
     */
    final public function getAllRoles(): ?array
    {
        $rows = $this->store->getAllRoleRows();

        if (empty($rows)) {
            return null;
        }

        return $this->getVOArray($rows);
    }

    /**
     * @return int
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getRolesPageCount(): int
    {
        $rowsCount = $this->store->getRoleRowsCount();

        $pageCount = (int)($rowsCount / $this->itemsOnPage);

        if ($pageCount * $this->itemsOnPage < $rowsCount) {
            $pageCount++;
        }

        return $pageCount;
    }

    /**
     * @return int
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getRoleActionsPageCount(): int
    {
        $rowsCount = $this->store->getRoleActionRowsCount();

        $pageCount = (int)($rowsCount / $this->itemsOnPage);

        if ($pageCount * $this->itemsOnPage < $rowsCount) {
            $pageCount++;
        }

        return $pageCount;
    }

    /**
     * @return ValuesObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getGuestVO(): ValuesObject
    {
        $row = $this->store->getRoleRowByName('guest');

        if (empty($row)) {
            throw new Exception('Guest Role Is Not Exists In Database');
        }

        return $this->getVO($row);
    }

    /**
     * @param int|null $roleId
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getAllowedActionsByRoleId(?int $roleId = null): ?array
    {
        if (empty($roleId)) {
            return null;
        }

        return $this->store->getAllowedActionRowsByRoleId($roleId);
    }

    /**
     * @param int|null $roleId
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getDeniedActionsByRoleId(?int $roleId = null): ?array
    {
        if (empty($roleId)) {
            return null;
        }

        return $this->store->getDeniedActionRowsByRoleId($roleId);
    }

    /**
     * @param RoleActionForm $roleActionForm
     * @return bool
     * @throws Exception
     */
    final public function saveRoleAction(RoleActionForm $roleActionForm): bool
    {
        $roleActionForm->checkInputValues();

        if (!$roleActionForm->getStatus()) {
            return false;
        }

        $this->_checkIdInRoleActionForm($roleActionForm);
        $this->_checkNameInRoleActionForm($roleActionForm);

        if (!$roleActionForm->getStatus()) {
            return false;
        }

        $roleActionVO = $this->_getRoleActionVOFromForm(
            $roleActionForm,
            true
        );

        try {
            if (!$this->store->insertOrUpdateRoleAction($roleActionVO)) {
                $roleActionForm->setStatusFail();

                return false;
            }

            if (!empty($roleActionForm->getId())) {
                return true;
            }

            $id = $this->store->getRoleActionIdByName(
                $roleActionVO->getName()
            );

            if (!empty($id)) {
                $roleActionForm->setId($id);
            }
        } catch (Throwable $exp) {
            $roleActionForm->setStatusFail();
            $roleActionForm->setError($exp->getMessage());

            return false;
        }

        return true;
    }

    /**
     * @param RoleForm $roleForm
     * @return bool
     * @throws Exception
     */
    final public function saveRole(RoleForm $roleForm): bool
    {
        $roleForm->checkInputValues();

        if (!$roleForm->getStatus()) {
            return false;
        }

        $this->_checkIdInRoleForm($roleForm);
        $this->_checkNameInRoleForm($roleForm);
        $this->_checkParentIdInRoleForm($roleForm);

        if (!$roleForm->getStatus()) {
            return false;
        }

        $roleVO = $this->_getRoleVOFromForm($roleForm, true);

        $this->store->start();

        $deniedActions = (array)$roleForm->getDeniedActions();
        $allowedActions = (array)$roleForm->getAllowedActions();

        try {
            if (!$this->store->insertOrUpdateRole($roleVO)) {
                $roleForm->setStatusFail();

                $this->store->rollback();

                return false;
            }

            $row = $this->store->getRoleRowByName($roleVO->getName());

            if (empty($row)) {
                $roleForm->setStatusFail();

                $this->store->rollback();

                return false;
            }

            /* @var $roleVO RoleValuesObject */
            $roleVO = $this->getVO($row);

            $this->store->deleteRoleToRoleActionByRoleId($roleVO->getId());

            foreach ($deniedActions as $deniedActionId) {
                $this->store->insertRoleToRoleAction(
                    $roleVO->getId(),
                    $deniedActionId,
                    false
                );
            }

            foreach ($allowedActions as $allowedActionId) {
                if (!in_array($allowedActionId, $deniedActions)) {
                    $this->store->insertRoleToRoleAction(
                        $roleVO->getId(),
                        $allowedActionId,
                        true
                    );
                }
            }

            $this->store->commit();

            if (!empty($roleForm->getId())) {
                return true;
            }

            $id = $this->store->getRoleIdByName(
                $roleForm->getName()
            );

            if (!empty($id)) {
                $roleForm->setId($id);
            }
        } catch (Throwable $exp) {
            $roleForm->setStatusFail();
            $roleForm->setError($exp->getMessage());

            $this->store->rollback();

            return false;
        }

        return true;
    }

    /**
     * @param int|null $id
     * @return bool
     * @throws DatabasePluginException
     */
    final public function removeRoleActionById(?int $id): bool
    {
        if (empty($id)) {
            return false;
        }

        return $this->store->deleteRoleActionById($id);
    }

    /**
     * @param int|null $id
     * @return bool
     * @throws DatabasePluginException
     */
    final public function restoreRoleActionById(?int $id): bool
    {
        if (empty($id)) {
            return false;
        }

        return $this->store->restoreRoleActionById($id);
    }

    /**
     * @param int|null $id
     * @return bool
     * @throws DatabasePluginException
     */
    final public function removeRoleById(?int $id): bool
    {
        if (empty($id)) {
            return false;
        }

        return $this->store->deleteRoleById($id);
    }

    /**
     * @param int|null $id
     * @return bool
     * @throws DatabasePluginException
     */
    final public function restoreRoleById(?int $id): bool
    {
        if (empty($id)) {
            return false;
        }

        return $this->store->restoreRoleById($id);
    }

    /**
     * @param array|null $row
     * @return ValuesObject
     * @throws Exception
     */
    final protected function getVO(?array $row = null): ValuesObject
    {
        /* @var $roleVO IRoleValuesObject */
        $roleVO = parent::getVO($row);

        if (empty($roleVO->getId())) {
            return $roleVO;
        }

        $this->_setParentToVO($roleVO);
        $this->_setActionsToVO($roleVO);

        return $roleVO;
    }

    /**
     * @param RoleValuesObject $roleVO
     * @return void
     * @throws Exception
     */
    private function _setParentToVO(RoleValuesObject $roleVO): void
    {
        $parentVO = null;

        if (!empty($roleVO->getParentId())) {
            /* @var $parentVO RoleValuesObject */
            $parentVO = $this->getVOById($roleVO->getParentId());
        }

        if (
            !empty($parentVO) &&
            $parentVO->getIsActive() &&
            empty($parentVO->getDdate())
        ) {
            $roleVO->setParentVO($parentVO);
        }
    }

    /**
     * @param RoleValuesObject $roleVO
     * @return void
     * @throws Exception
     */
    private function _setActionsToVO(RoleValuesObject $roleVO): void
    {
        $allowedActions = $this->getAllowedActionsByRoleId($roleVO->getId());
        $deniedActions = $this->getDeniedActionsByRoleId($roleVO->getId());

        $roleVO->setAllowedActions($allowedActions);
        $roleVO->setDeniedActions($deniedActions);

        /* @var $roleParentVO RoleValuesObject */
        $roleParentVO = $roleVO->getParentVO();

        while (!empty($roleParentVO)) {
            $roleParentId = $roleParentVO->getId();

            $allowedActions = $this->getAllowedActionsByRoleId($roleParentId);
            $deniedActions = $this->getDeniedActionsByRoleId($roleParentId);

            $roleVO->setAllowedActions($allowedActions);
            $roleVO->setDeniedActions($deniedActions);

            $roleParentVO = $roleParentVO->getParentVO();
        }
    }

    /**
     * @param RoleActionForm $roleActionForm
     * @return void
     * @throws Exception
     */
    private function _checkNameInRoleActionForm(
        RoleActionForm $roleActionForm
    ): void
    {
        $translitPlugin = $this->getPlugin('translit');

        $name = $roleActionForm->getName();
        $name = $translitPlugin->getSlug($name);

        $roleActionForm->setName($name);

        if (empty($name)) {
            $roleActionForm->setStatusFail();

            $roleActionForm->setError(
                RoleActionForm::NAME_EMPTY_ERROR_MESSAGE
            );
        }

        if (
            !empty($name) &&
            !$this->_isRoleActionNameUniq($name, $roleActionForm->getId())
        ) {
            $roleActionForm->setStatusFail();

            $roleActionForm->setError(
                RoleActionForm::NAME_EXISTS_ERROR_MESSAGE
            );
        }
    }

    /**
     * @param RoleActionForm $roleActionForm
     * @return bool
     * @throws Exception
     */
    private function _checkIdInRoleActionForm(
        RoleActionForm $roleActionForm
    ): bool
    {
        $id = $roleActionForm->getId();

        if (empty($id)) {
            return true;
        }

        $roleActionVO = $this->_getRoleActionVOFromForm($roleActionForm);

        if (empty($roleActionVO)) {
            $roleActionForm->setStatusFail();

            $roleActionForm->setError(
                RoleActionForm::ROLE_ACTION_IS_NOT_EXISTS_ERROR_MESSAGE
            );

            return false;
        }

        if ($roleActionVO->getIsSystem()) {
            $roleActionForm->setStatusFail();

            $roleActionForm->setError(
                RoleActionForm::ROLE_ACTION_IS_SYSTEM_ERROR_MESSAGE
            );

            return false;
        }

        return true;
    }

    /**
     * @param RoleForm $roleForm
     * @return void
     * @throws Exception
     */
    private function _checkNameInRoleForm(RoleForm $roleForm): void
    {
        $translitPlugin = $this->getPlugin('translit');

        $name = $roleForm->getName();
        $name = $translitPlugin->getSlug($name);

        $roleForm->setName($name);

        if (empty($name)) {
            $roleForm->setStatusFail();
            $roleForm->setError(RoleForm::NAME_EMPTY_ERROR_MESSAGE);
        }

        if (
            !empty($name) &&
            !$this->_isRoleNameUniq($name, $roleForm->getId())
        ) {
            $roleForm->setStatusFail();
            $roleForm->setError(RoleForm::NAME_EXISTS_ERROR_MESSAGE);
        }
    }

    /**
     * @param RoleForm $roleForm
     * @return void
     * @throws Exception
     */
    private function _checkParentIdInRoleForm(RoleForm $roleForm): void
    {
        $parentId = $roleForm->getParentId();
        $id = $roleForm->getId();

        /* @var $roleParentVO RoleValuesObject */
        $roleParentVO = $this->getVOById($parentId);

        if (!empty($parentId) && empty($roleParentVO)) {
            $roleForm->setStatusFail();

            $roleForm->setError(
                RoleForm::PARENT_ROLE_IS_NOT_EXISTS_ERROR_MESSAGE
            );
        }

        while (!empty($roleParentVO)) {
            if ($id == $roleParentVO->getId()) {
                $roleForm->setStatusFail();
                $roleForm->setError(
                    RoleForm::ROLE_HAVE_CIRCULAR_DEPENDENCY_ERROR_MESSAGE
                );
            }

            $roleParentVO = $roleParentVO->getParentVO();
        }
    }

    /**
     * @param RoleForm $roleForm
     * @return bool
     * @throws Exception
     */
    private function _checkIdInRoleForm(RoleForm $roleForm): bool
    {
        $id = $roleForm->getId();

        if (empty($id)) {
            return true;
        }

        $roleVO = $this->_getRoleVOFromForm($roleForm);

        if (empty($roleVO)) {
            $roleForm->setStatusFail();

            $roleForm->setError(
                RoleForm::ROLE_IS_NOT_EXISTS_ERROR_MESSAGE
            );

            return false;
        }

        if ($roleVO->getIsSystem()) {
            $roleForm->setStatusFail();

            $roleForm->setError(RoleForm::ROLE_IS_SYSTEM_ERROR_MESSAGE);

            return false;
        }

        return true;
    }

    /**
     * @param string|null $name
     * @param int|null $id
     * @return bool
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    private function _isRoleActionNameUniq(?string $name, ?int $id): bool
    {
        $row = $this->store->getRoleActionRowByName($name, $id);

        return empty($row);
    }

    /**
     * @param string|null $name
     * @param int|null $id
     * @return bool
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    private function _isRoleNameUniq(?string $name, ?int $id): bool
    {
        $row = $this->store->getRoleRowByName($name, $id);

        return empty($row);
    }

    /**
     * @param RoleActionForm $roleActionForm
     * @param bool $isCreateVOIfEmptyId
     * @return RoleActionValuesObject|null
     * @throws Exception
     */
    private function _getRoleActionVOFromForm(
        RoleActionForm $roleActionForm,
        bool           $isCreateVOIfEmptyId = false
    ): ?RoleActionValuesObject
    {
        $row = null;

        $id = $roleActionForm->getId();

        if (empty($id) && !$isCreateVOIfEmptyId) {
            return null;
        }

        if (!empty($id)) {
            $row = $this->store->getRoleActionRowById($id);
        }

        if (!empty($id) && empty($row)) {
            return null;
        }

        $roleActionVO = new RoleActionValuesObject($row);

        $roleActionVO->setName($roleActionForm->getName());
        $roleActionVO->setIsActive($roleActionForm->getIsActive());

        return $roleActionVO;
    }

    /**
     * @param RoleForm $roleForm
     * @param bool $isCreateVOIfEmptyId
     * @return RoleValuesObject|null
     * @throws Exception
     */
    private function _getRoleVOFromForm(
        RoleForm $roleForm,
        bool     $isCreateVOIfEmptyId = false
    ): ?RoleValuesObject
    {
        $row = null;

        $id = $roleForm->getId();

        if (empty($id) && !$isCreateVOIfEmptyId) {
            return null;
        }

        if (!empty($id)) {
            $row = $this->store->getRoleRowById($id);
        }

        if (!empty($id) && empty($row)) {
            return null;
        }

        $roleVO = new RoleValuesObject($row);

        $roleVO->setName($roleForm->getName());
        $roleVO->setIsActive($roleForm->getIsActive());
        $roleVO->setParentId($roleForm->getParentId());

        return $roleVO;
    }
}
