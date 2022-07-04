<?php

namespace Sonder\Models;

use Sonder\Core\CoreModel;
use Sonder\Exceptions\CoreException;
use Sonder\Exceptions\ModelException;
use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IModel;
use Sonder\Interfaces\IRoleValuesObject;
use Sonder\Models\Role\Exceptions\RoleException;
use Sonder\Models\Role\Exceptions\RoleModelException;
use Sonder\Models\Role\Forms\RoleActionForm;
use Sonder\Models\Role\Forms\RoleForm;
use Sonder\Models\Role\Interfaces\IRoleModel;
use Sonder\Models\Role\Interfaces\IRoleSimpleValuesObject;
use Sonder\Models\Role\Interfaces\IRoleStore;
use Sonder\Models\Role\Interfaces\IRoleActionForm;
use Sonder\Models\Role\Interfaces\IRoleForm;
use Sonder\Interfaces\IRoleModel as IRoleModelFramework;
use Sonder\Models\Role\ValuesObjects\RoleActionSimpleValuesObject;
use Sonder\Models\Role\ValuesObjects\RoleActionValuesObject;
use Sonder\Models\Role\ValuesObjects\RoleSimpleValuesObject;
use Sonder\Models\Role\ValuesObjects\RoleValuesObject;
use Throwable;

/**
 * @property null $api
 * @property IRoleStore $store
 */
#[IModel]
#[IRoleModelFramework]
#[IRoleModel]
final class RoleModel
    extends CoreModel
    implements IRoleModel, IRoleModelFramework
{
    final protected const ITEMS_ON_PAGE = 10;

    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return IRoleValuesObject|null
     * @throws ModelException
     * @throws ValuesObjectException
     */
    public function getVOById(
        ?int $id = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?IRoleValuesObject {
        /* @var $roleVO ?IRoleValuesObject */
        $roleVO = $this->getRoleVOById(
            $id,
            $excludeRemoved,
            $excludeInactive
        );

        return $roleVO;
    }

    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return IRoleValuesObject|null
     * @throws ModelException
     * @throws ValuesObjectException
     */
    public function getSimpleVOById(
        ?int $id = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?IRoleValuesObject {
        /* @var $roleVO ?IRoleValuesObject */
        $roleVO = $this->getRoleSimpleVOById(
            $id,
            $excludeRemoved,
            $excludeInactive
        );

        return $roleVO;
    }

    /**
     * @param int $page
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     * @throws ModelException
     */
    final public function getRolesByPage(
        int $page,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array {
        $rows = $this->store->getRoleRowsByPage(
            $page,
            RoleModel::ITEMS_ON_PAGE,
            $excludeRemoved,
            $excludeInactive
        );

        if (empty($rows)) {
            return null;
        }

        return $this->getVOArray($rows);
    }

    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return RoleActionValuesObject|null
     */
    final public function getRoleActionVOById(
        ?int $id = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?RoleActionValuesObject {
        $row = $this->store->getRoleActionRowById(
            $id,
            $excludeRemoved,
            $excludeInactive
        );

        if (empty($row)) {
            return null;
        }

        return new RoleActionValuesObject($row);
    }

    /**
     * @param int|null $id
     * @return RoleActionSimpleValuesObject|null
     */
    final public function getRoleActionSimpleVOById(
        ?int $id = null
    ): ?RoleActionSimpleValuesObject {
        $row = $this->store->getRoleActionRowById($id);

        if (empty($row)) {
            return null;
        }

        return new RoleActionSimpleValuesObject($row);
    }

    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return RoleValuesObject|null
     * @throws ModelException
     * @throws ValuesObjectException
     */
    final public function getRoleVOById(
        ?int $id = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?RoleValuesObject {
        $row = $this->store->getRoleRowById(
            $id,
            $excludeRemoved,
            $excludeInactive
        );

        if (empty($row)) {
            return null;
        }

        /* @var $roleVO RoleValuesObject */
        $roleVO = $this->getVO($row);

        return $roleVO;
    }

    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return RoleSimpleValuesObject|null
     * @throws ModelException
     * @throws ValuesObjectException
     */
    final public function getRoleSimpleVOById(
        ?int $id = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?RoleSimpleValuesObject {
        $row = $this->store->getRoleRowById(
            $id,
            $excludeRemoved,
            $excludeInactive
        );

        if (empty($row)) {
            return null;
        }

        /* @var $roleVO RoleSimpleValuesObject */
        $roleVO = $this->getSimpleVO($row);

        return $roleVO;
    }

    /**
     * @param int $page
     * @return array|null
     */
    final public function getRoleActionsByPage(
        int $page
    ): ?array {
        $rows = $this->store->getRoleActionRowsByPage(
            $page,
            RoleModel::ITEMS_ON_PAGE
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
     * @throws ModelException
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
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     */
    final public function getRolesPageCount(
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int {
        $rowsCount = $this->store->getRoleRowsCount(
            $excludeRemoved,
            $excludeInactive
        );

        $pageCount = (int)($rowsCount / RoleModel::ITEMS_ON_PAGE);

        if ($pageCount * RoleModel::ITEMS_ON_PAGE < $rowsCount) {
            $pageCount++;
        }

        return $pageCount;
    }

    /**
     * @return int
     */
    final public function getRoleActionsPageCount(): int
    {
        $rowsCount = $this->store->getRoleActionRowsCount(false, false);

        $pageCount = (int)($rowsCount / RoleModel::ITEMS_ON_PAGE);

        if ($pageCount * RoleModel::ITEMS_ON_PAGE < $rowsCount) {
            $pageCount++;
        }

        return $pageCount;
    }

    /**
     * @return IRoleValuesObject
     * @throws ModelException
     * @throws RoleModelException
     * @throws ValuesObjectException
     */
    final public function getGuestVO(): IRoleValuesObject
    {
        $row = $this->store->getRoleRowByName('guest');

        if (empty($row)) {
            throw new RoleModelException(
                RoleModelException::MESSAGE_MODEL_GUEST_ROLE_NOT_EXISTS,
                RoleException::CODE_MODEL_GUEST_ROLE_NOT_EXISTS,
            );
        }

        return $this->getVO($row);
    }

    /**
     * @return IRoleSimpleValuesObject
     * @throws ModelException
     * @throws RoleModelException
     * @throws ValuesObjectException
     */
    final public function getGuestSimpleVO(): IRoleSimpleValuesObject
    {
        $row = $this->store->getRoleRowByName('guest');

        if (empty($row)) {
            throw new RoleModelException(
                RoleModelException::MESSAGE_MODEL_GUEST_ROLE_NOT_EXISTS,
                RoleException::CODE_MODEL_GUEST_ROLE_NOT_EXISTS,
            );
        }

        return $this->getSimpleVO($row);
    }

    /**
     * @param int|null $roleId
     * @return array|null
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
     */
    final public function getDeniedActionsByRoleId(?int $roleId = null): ?array
    {
        if (empty($roleId)) {
            return null;
        }

        return $this->store->getDeniedActionRowsByRoleId($roleId);
    }

    /**
     * @param IRoleActionForm $roleActionForm
     * @return bool
     * @throws CoreException
     * @throws ValuesObjectException
     */
    final public function saveRoleAction(IRoleActionForm $roleActionForm): bool
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
        } catch (Throwable $thr) {
            $roleActionForm->setStatusFail();
            $roleActionForm->setError($thr->getMessage());

            return false;
        }

        return true;
    }

    /**
     * @param IRoleForm $roleForm
     * @return bool
     * @throws CoreException
     * @throws ModelException
     * @throws ValuesObjectException
     */
    final public function saveRole(IRoleForm $roleForm): bool
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

            $row = $this->store->getRoleRowByName(
                $roleVO->getName(),
                null,
                false,
                false
            );

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
                        $allowedActionId
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
        } catch (Throwable $thr) {
            $roleForm->setStatusFail();
            $roleForm->setError($thr->getMessage());

            $this->store->rollback();

            return false;
        }

        return true;
    }

    /**
     * @param int|null $id
     * @return bool
     */
    final public function removeRoleActionById(?int $id = null): bool
    {
        if (empty($id)) {
            return false;
        }

        return $this->store->deleteRoleActionById($id);
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

        return $this->store->restoreRoleActionById($id);
    }

    /**
     * @param int|null $id
     * @return bool
     */
    final public function removeRoleById(?int $id = null): bool
    {
        if (empty($id)) {
            return false;
        }

        return $this->store->deleteRoleById($id);
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

        return $this->store->restoreRoleById($id);
    }

    /**
     * @param array|null $row
     * @return IRoleValuesObject
     * @throws ModelException
     * @throws ValuesObjectException
     */
    final protected function getVO(?array $row = null): IRoleValuesObject
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
     * @param array|null $row
     * @return IRoleSimpleValuesObject
     * @throws ModelException
     * @throws ValuesObjectException
     */
    final protected function getSimpleVO(
        ?array $row = null
    ): IRoleSimpleValuesObject {
        /* @var $roleVO IRoleSimpleValuesObject */
        $roleVO = parent::getSimpleVO($row);

        if (empty($roleVO->getId())) {
            return $roleVO;
        }

        $this->_setParentToVO($roleVO);
        $this->_setActionsToVO($roleVO);

        return $roleVO;
    }

    /**
     * @param IRoleValuesObject|IRoleSimpleValuesObject $roleVO
     * @return void
     * @throws ModelException
     * @throws ValuesObjectException
     */
    private function _setParentToVO(
        IRoleValuesObject|IRoleSimpleValuesObject $roleVO
    ): void {
        $parentVO = null;

        if (!empty($roleVO->getParentId())) {
            /* @var $parentVO IRoleSimpleValuesObject */
            $parentVO = $this->getSimpleVOById($roleVO->getParentId());
        }

        if (
            !empty($parentVO) &&
            $parentVO->isActive() &&
            empty($parentVO->getDdate())
        ) {
            $roleVO->setParentVO($parentVO);
        }
    }

    /**
     * @param IRoleValuesObject|IRoleSimpleValuesObject $roleVO
     * @return void
     * @throws ValuesObjectException
     */
    private function _setActionsToVO(
        IRoleValuesObject|IRoleSimpleValuesObject $roleVO
    ): void {
        $allowedActions = $this->getAllowedActionsByRoleId($roleVO->getId());
        $deniedActions = $this->getDeniedActionsByRoleId($roleVO->getId());

        $roleVO->setAllowedActions($allowedActions);
        $roleVO->setDeniedActions($deniedActions);

        /* @var $roleParentVO IRoleValuesObject */
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
     * @throws ValuesObjectException
     * @throws CoreException
     */
    private function _checkNameInRoleActionForm(
        RoleActionForm $roleActionForm
    ): void {
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
     * @return void
     * @throws ValuesObjectException
     */
    private function _checkIdInRoleActionForm(
        RoleActionForm $roleActionForm
    ): void {
        $id = $roleActionForm->getId();

        if (empty($id)) {
            return;
        }

        $roleActionVO = $this->_getRoleActionVOFromForm($roleActionForm);

        if (empty($roleActionVO)) {
            $roleActionForm->setStatusFail();

            $roleActionForm->setError(
                sprintf(
                    RoleActionForm::ROLE_ACTION_NOT_EXISTS_ERROR_MESSAGE,
                    $id
                )
            );

            return;
        }

        if ($roleActionVO->isSystem()) {
            $roleActionForm->setStatusFail();

            $roleActionForm->setError(
                RoleActionForm::ROLE_ACTION_IS_SYSTEM_ERROR_MESSAGE
            );
        }
    }

    /**
     * @param RoleForm $roleForm
     * @return void
     * @throws CoreException
     * @throws ValuesObjectException
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
     * @throws ModelException
     * @throws ValuesObjectException
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
                RoleForm::PARENT_ROLE_NOT_EXISTS_ERROR_MESSAGE
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
     * @return void
     * @throws ValuesObjectException
     */
    private function _checkIdInRoleForm(RoleForm $roleForm): void
    {
        $id = $roleForm->getId();

        if (empty($id)) {
            return;
        }

        $roleVO = $this->_getRoleVOFromForm($roleForm);

        if (empty($roleVO)) {
            $roleForm->setStatusFail();

            $roleForm->setError(
                sprintf(
                    RoleForm::ROLE_NOT_EXISTS_ERROR_MESSAGE,
                    $id
                )
            );

            return;
        }

        if ($roleVO->isSystem()) {
            $roleForm->setStatusFail();

            $roleForm->setError(RoleForm::ROLE_IS_SYSTEM_ERROR_MESSAGE);
        }
    }

    /**
     * @param string|null $name
     * @param int|null $id
     * @return bool
     */
    private function _isRoleActionNameUniq(
        ?string $name = null,
        ?int $id = null
    ): bool {
        $row = $this->store->getRoleActionRowByName($name, $id, false, false);

        return empty($row);
    }

    /**
     * @param string|null $name
     * @param int|null $id
     * @return bool
     */
    private function _isRoleNameUniq(
        ?string $name = null,
        ?int $id = null
    ): bool {
        $row = $this->store->getRoleRowByName($name, $id, false, false);

        return empty($row);
    }

    /**
     * @param RoleActionForm $roleActionForm
     * @param bool $isCreateVOIfEmptyId
     * @return RoleActionValuesObject|null
     * @throws ValuesObjectException
     */
    private function _getRoleActionVOFromForm(
        RoleActionForm $roleActionForm,
        bool $isCreateVOIfEmptyId = false
    ): ?RoleActionValuesObject {
        $row = null;

        $id = $roleActionForm->getId();

        if (empty($id) && !$isCreateVOIfEmptyId) {
            return null;
        }

        if (!empty($id)) {
            $row = $this->store->getRoleActionRowById(
                $id,
                false,
                false
            );
        }

        if (!empty($id) && empty($row)) {
            return null;
        }

        $roleActionVO = new RoleActionValuesObject($row);

        $roleActionVO->setName($roleActionForm->getName());
        $roleActionVO->setIsActive($roleActionForm->isActive());

        return $roleActionVO;
    }

    /**
     * @param RoleForm $roleForm
     * @param bool $isCreateVOIfEmptyId
     * @return RoleValuesObject|null
     * @throws ValuesObjectException
     */
    private function _getRoleVOFromForm(
        RoleForm $roleForm,
        bool $isCreateVOIfEmptyId = false
    ): ?RoleValuesObject {
        $row = null;

        $id = $roleForm->getId();

        if (empty($id) && !$isCreateVOIfEmptyId) {
            return null;
        }

        if (!empty($id)) {
            $row = $this->store->getRoleRowById($id, false, false);
        }

        if (!empty($id) && empty($row)) {
            return null;
        }

        $roleVO = new RoleValuesObject($row);

        $roleVO->setName($roleForm->getName());
        $roleVO->setIsActive($roleForm->isActive());
        $roleVO->setParentId($roleForm->getParentId());

        return $roleVO;
    }
}
