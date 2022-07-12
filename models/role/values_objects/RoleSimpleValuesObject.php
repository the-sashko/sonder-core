<?php

namespace Sonder\Models\Role\ValuesObjects;

use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IModelSimpleValuesObject;
use Sonder\Core\ModelSimpleValuesObject;
use Sonder\Interfaces\IValuesObject;
use Sonder\Models\Role\Exceptions\RoleException;
use Sonder\Models\Role\Exceptions\RoleSimpleValuesObjectException;
use Sonder\Models\Role\Interfaces\IRoleSimpleValuesObject;

#[IValuesObject]
#[IModelSimpleValuesObject]
#[IRoleSimpleValuesObject]
final class RoleSimpleValuesObject
    extends ModelSimpleValuesObject
    implements IRoleSimpleValuesObject
{
    /**
     * @return string
     * @throws ValuesObjectException
     */
    final public function getName(): string
    {
        $name = null;

        if ($this->has('name')) {
            $name = $this->get('name');
        }

        return (string)$name;
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
     * @return IRoleSimpleValuesObject|null
     * @throws ValuesObjectException
     */
    final public function getParentVO(): ?IRoleSimpleValuesObject
    {
        if (!$this->has('parent_simple_vo')) {
            return null;
        }

        $parent = $this->get('parent_simple_vo');

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
     * @param string|null $roleActionIdent
     * @return bool
     * @throws RoleSimpleValuesObjectException
     */
    public function can(?string $roleActionIdent = null): bool
    {
        $errorMessage = sprintf(
            RoleSimpleValuesObjectException::MESSAGE_ROLE_SIMPLE_VALUES_OBJECT_METHOD_NOT_IMPLEMENTED,
            'can()'
        );

        throw new RoleSimpleValuesObjectException(
            $errorMessage,
            RoleException::CODE_ROLE_SIMPLE_VALUES_OBJECT_METHOD_NOT_IMPLEMENTED
        );
    }

    /**
     * @param IRoleSimpleValuesObject|null $parentVO
     * @return void
     * @throws ValuesObjectException
     */
    final public function setParentVO(?IRoleSimpleValuesObject $parentVO = null
    ): void {
        if (!empty($parentVO)) {
            $this->set('parent_simple_vo', $parentVO);
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
     * @throws ValuesObjectException
     */
    final public function exportRow(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'allowed_actions' => $this->getAllowedActions(),
            'denied_actions' => $this->getDeniedActions()
        ];
    }
}
