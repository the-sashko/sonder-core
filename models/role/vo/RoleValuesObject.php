<?php

namespace Sonder\Models\Role\ValuesObjects;

use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IModelSimpleValuesObject;
use Sonder\Interfaces\IModelValuesObject;
use Sonder\Interfaces\IValuesObject;
use Sonder\Models\Role\Interfaces\IRoleValuesObject as IRoleModelValuesObject;
use Sonder\Interfaces\IRoleValuesObject;
use Sonder\Core\ModelValuesObject;

#[IValuesObject]
#[IModelSimpleValuesObject]
#[IModelValuesObject]
#[IRoleValuesObject]
#[IRoleModelValuesObject]
final class RoleValuesObject
    extends ModelValuesObject
    implements IRoleValuesObject, IRoleModelValuesObject
{
    final protected const EDIT_LINK_PATTERN = '/admin/users/role/%d/';

    final protected const REMOVE_LINK_PATTERN = '/admin/users/roles/remove/%d/';

    final protected const RESTORE_LINK_PATTERN = '/admin/users/roles/restore/%d/';

    final protected const ADMIN_VIEW_LINK_PATTERN = '/admin/users/roles/view/%d/';

    /**
     * @return string
     * @throws ValuesObjectException
     */
    final public function getName(): string
    {
        return (string)$this->get('name');
    }

    /**
     * @return int|null
     * @throws ValuesObjectException
     */
    final public function getParentId(): ?int
    {
        $parentId = $this->get('parent_id');

        if (empty($parentId)) {
            return null;
        }

        return (int)$parentId;
    }

    /**
     * @return IRoleValuesObject|null
     * @throws ValuesObjectException
     */
    final public function getParentVO(): ?IRoleValuesObject
    {
        if (!$this->has('parent_vo')) {
            return null;
        }

        $parent = $this->get('parent_vo');

        if (empty($parent)) {
            return null;
        }

        return $parent;
    }

    /**
     * @return array|null
     * @throws ValuesObjectException
     */
    final public function getAllowedActions(): ?array
    {
        if (!$this->has('allowed_actions')) {
            return null;
        }

        $allowedActions = $this->get('allowed_actions');

        if (empty($allowedActions) || !is_array($allowedActions)) {
            return null;
        }

        return $allowedActions;
    }

    /**
     * @return array|null
     * @throws ValuesObjectException
     */
    final public function getDeniedActions(): ?array
    {
        if (!$this->has('denied_actions')) {
            return null;
        }

        $deniedActions = $this->get('denied_actions');

        if (empty($deniedActions) || !is_array($deniedActions)) {
            return null;
        }

        return $deniedActions;
    }

    /**
     * @return bool
     * @throws ValuesObjectException
     */
    final public function isSystem(): bool
    {
        return (bool)$this->get('is_system');
    }

    /**
     * @return string
     * @throws ValuesObjectException
     */
    final public function getAdminViewLink(): string
    {
        return sprintf(
            RoleValuesObject::ADMIN_VIEW_LINK_PATTERN,
            $this->getId()
        );
    }

    /**
     * @param string|null $roleActionIdent
     * @return bool
     * @throws ValuesObjectException
     */
    final public function can(?string $roleActionIdent = null): bool
    {
        if (empty($roleActionIdent)) {
            return false;
        }

        if (!in_array($roleActionIdent, (array)$this->getAllowedActions())) {
            return false;
        }

        if (in_array($roleActionIdent, (array)$this->getDeniedActions())) {
            return false;
        }

        return true;
    }

    /**
     * @param string|null $name
     * @return void
     * @throws ValuesObjectException
     */
    final public function setName(?string $name = null): void
    {
        if (!empty($name)) {
            $this->set('name', $name);
        }
    }

    /**
     * @param int|null $parentId
     * @return void
     * @throws ValuesObjectException
     */
    final public function setParentId(?int $parentId = null): void
    {
        if (!empty($parentId)) {
            $this->set('parent_id', $parentId);
        }
    }

    /**
     * @param IRoleValuesObject|null $parentVO
     * @return void
     * @throws ValuesObjectException
     */
    final public function setParentVO(
        ?IRoleValuesObject $parentVO = null
    ): void {
        if (!empty($parentVO)) {
            $this->set('parent_vo', $parentVO);
        }
    }

    /**
     * @param array|null $allowedActions
     * @return void
     * @throws ValuesObjectException
     */
    final public function setAllowedActions(
        ?array $allowedActions = null
    ): void {
        if (!empty($allowedActions)) {
            $allowedActions = array_merge(
                $allowedActions,
                (array)$this->getAllowedActions()
            );

            $allowedActions = array_unique($allowedActions);

            $this->set('allowed_actions', $allowedActions);
        }
    }

    /**
     * @param array|null $deniedActions
     * @return void
     * @throws ValuesObjectException
     */
    final public function setDeniedActions(?array $deniedActions = null): void
    {
        if (!empty($deniedActions)) {
            $deniedActions = array_merge(
                $deniedActions,
                (array)$this->getDeniedActions()
            );

            $deniedActions = array_unique($deniedActions);

            $this->set('denied_actions', $deniedActions);
        }
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

        if (array_key_exists('parent_vo', $row)) {
            unset($row['parent_vo']);
        }

        return $row;
    }
}
