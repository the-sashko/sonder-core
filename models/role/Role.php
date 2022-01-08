<?php

namespace Sonder\Models;

use Exception;
use Sonder\Core\CoreModel;
use Sonder\Core\Interfaces\IModel;
use Sonder\Core\Interfaces\IRole;
use Sonder\Core\ValuesObject;
use Sonder\Models\Role\RoleActionForm;
use Sonder\Models\Role\RoleActionValuesObject;
use Throwable;

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
     * @param int|null $id
     * @return RoleActionValuesObject|null
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
     * @param int $page
     * @return array|null
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
     * @return int
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
     * @throws Exception
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
     * @param RoleActionForm $roleActionForm
     * @return bool
     * @throws Exception
     */
    final public function saveRoleAction(RoleActionForm &$roleActionForm): bool
    {
        $roleActionForm->checkInputValues();

        if (!$roleActionForm->getStatus()) {
            return false;
        }

        if ($this->_checkIdInForm($roleActionForm)) {
            $this->_checkNameInForm($roleActionForm);
        }

        if (!$roleActionForm->getStatus()) {
            return false;
        }

        $roleActionVO = $this->_getRoleActionVOFromForm($roleActionForm, true);

        try {
            if (!$this->store->insertOrUpdateRoleAction($roleActionVO)) {
                $roleActionForm->setStatusFail();

                return false;
            }
        } catch (Throwable $exp) {
            $roleActionForm->setStatusFail();
            $roleActionForm->setError($exp->getMessage());

            return false;
        }

        return true;
    }

    /**
     * @param RoleActionForm $roleActionForm
     * @return void
     * @throws Exception
     */
    private function _checkNameInForm(RoleActionForm &$roleActionForm): void
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
            !$this->_isNameUniq($name, $roleActionForm->getId())
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
    private function _checkIdInForm(RoleActionForm &$roleActionForm): bool
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
                RoleActionForm::ROLE_ACTION_IS_SYSTEM
            );

            return false;
        }

        return true;
    }

    /**
     * @param string|null $name
     * @param int|null $id
     * @return bool
     */
    private function _isNameUniq(?string $name, ?int $id): bool
    {
        $row = $this->store->getRoleActionRowByName($name, $id);

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
}
