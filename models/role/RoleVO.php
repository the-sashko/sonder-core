<?php

namespace Sonder\Models\Role;

use Exception;
use Sonder\Core\Interfaces\IRoleValuesObject;
use Sonder\Core\ModelValuesObject;
use Sonder\Core\ValuesObject;

final class RoleValuesObject
    extends ModelValuesObject
    implements IRoleValuesObject
{
    /**
     * @var string|null
     */
    protected ?string $editLinkPattern = '/admin/users/role/%d/';

    /**
     * @var string|null
     */
    protected ?string $removeLinkPattern = '/admin/users/roles/remove/%d/';

    /**
     * @var string|null
     */
    protected ?string $restoreLinkPattern = '/admin/users/roles/restore/%d/';

    /**
     * @return string
     * @throws Exception
     */
    final public function getName(): string
    {
        return (string)$this->get('name');
    }

    /**
     * @return int|null
     * @throws Exception
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
     * @return ValuesObject|null
     * @throws Exception
     */
    final public function getParentVO(): ?ValuesObject
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
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
     */
    final public function getIsSystem(): bool
    {
        return (bool)$this->get('is_system');
    }

    /**
     * @param string|null $roleActionIdent
     * @return bool
     * @throws Exception
     */
    public function can(?string $roleActionIdent = null): bool
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
     * @throws Exception
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
     * @throws Exception
     */
    final public function setParentId(?int $parentId = null): void
    {
        if (!empty($parentId)) {
            $this->set('parent_id', $parentId);
        }
    }

    /**
     * @param ValuesObject|null $parentVO
     * @return void
     * @throws Exception
     */
    final public function setParentVO(?ValuesObject $parentVO = null): void
    {
        if (!empty($parentVO)) {
            $this->set('parent_vo', $parentVO);
        }
    }

    /**
     * @param array|null $allowedActions
     * @return void
     * @throws Exception
     */
    final public function setAllowedActions(
        ?array $allowedActions = null
    ): void
    {
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
     * @throws Exception
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
}
