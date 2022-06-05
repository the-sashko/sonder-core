<?php

namespace Sonder\Models\Role;

use Exception;
use Sonder\Core\Interfaces\IRoleValuesObject;
use Sonder\Core\ModelSimpleValuesObject;

final class RoleSimpleValuesObject
    extends ModelSimpleValuesObject
    implements IRoleValuesObject
{
    /**
     * @return string
     * @throws Exception
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
     * @return IRoleValuesObject|null
     * @throws Exception
     */
    final public function getParentVO(): ?IRoleValuesObject
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
     * @throws Exception
     */
    public function can(?string $roleActionIdent = null): bool
    {
        throw new Exception('Method can() Is Not Implemented');
    }

    /**
     * @param IRoleValuesObject|null $parentVO
     * @return void
     * @throws Exception
     */
    final public function setParentVO(?IRoleValuesObject $parentVO = null): void
    {
        if (!empty($parentVO)) {
            $this->set('parent_simple_vo', $parentVO);
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

    /**
     * @param array|null $params
     * @return array|null
     * @throws Exception
     */
    final public function exportRow(?array $params = null): ?array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'allowed_actions' => $this->getAllowedActions(),
            'denied_actions' => $this->getDeniedActions()
        ];
    }
}
