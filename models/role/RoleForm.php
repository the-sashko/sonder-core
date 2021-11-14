<?php

namespace Sonder\Models\Role;

use Exception;
use Sonder\Core\ModelFormObject;

final class RoleForm extends ModelFormObject
{
    const NAME_MIN_LENGTH = 3;

    const NAME_MAX_LENGTH = 255;

    const NAME_EMPTY_ERROR_MESSAGE = 'Name is empty';

    const NAME_TOO_SHORT_ERROR_MESSAGE = 'Name is too short';

    const NAME_TOO_LONG_ERROR_MESSAGE = 'Name is too long';

    const NAME_EXISTS_ERROR_MESSAGE = 'Role with this name already exists';

    /**
     * @throws Exception
     */
    final public function checkInputValues(): void
    {
        $this->setStatusSuccess();

        $this->_validateNameValue();
    }

    /**
     * @return int|null
     *
     * @throws Exception
     */
    final public function getId(): ?int
    {
        if (!$this->has('id')) {
            return null;
        }

        $id = $this->get('id');

        if (empty($id)) {
            return null;
        }

        return (int)$id;
    }

    /**
     * @return int|null
     *
     * @throws Exception
     */
    final public function getParentId(): ?int
    {
        if (!$this->has('parent_id')) {
            return null;
        }

        $parentId = $this->get('parent_id');

        if (empty($parentId)) {
            return null;
        }

        return (int)$parentId;
    }

    /**
     * @return string|null
     *
     * @throws Exception
     */
    final public function getName(): ?string
    {
        if ($this->has('name')) {
            return $this->get('name');
        }

        return null;
    }

    /**
     * @return array|null
     *
     * @throws Exception
     */
    final public function getAllowedActions(): ?array
    {
        if ($this->has('allowed_actions')) {
            return $this->get('allowed_actions');
        }

        return null;
    }

    /**
     * @return array|null
     *
     * @throws Exception
     */
    final public function getDeniedActions(): ?array
    {
        if ($this->has('denied_actions')) {
            return $this->get('denied_actions');
        }

        return null;
    }

    /**
     * @param string|null $name
     *
     * @throws Exception
     */
    final public function setName(?string $name = null): void
    {
        $this->set('name', $name);
    }

    /**
     * @param array|null $allowedActions
     *
     * @throws Exception
     */
    final public function setAllowedActions(?array $allowedActions = null): void
    {
        $this->set('allowed_actions', $allowedActions);
    }

    /**
     * @param array|null $deniedActions
     *
     * @throws Exception
     */
    final public function setDeniedActions(?array $deniedActions = null): void
    {
        $this->set('denied_actions', $deniedActions);
    }

    /**
     * @throws Exception
     */
    private function _validateNameValue(): void
    {
        $name = $this->getName();

        if (empty($name)) {
            $this->setError(RoleForm::NAME_EMPTY_ERROR_MESSAGE);
            $this->setStatusFail();
        }

        if (!empty($name) && mb_strlen($name) > RoleForm::NAME_MAX_LENGTH) {
            $this->setError(RoleForm::NAME_TOO_LONG_ERROR_MESSAGE);
            $this->setStatusFail();
        }

        if (!empty($name) && mb_strlen($name) < RoleForm::NAME_MIN_LENGTH) {
            $this->setError(RoleForm::NAME_TOO_SHORT_ERROR_MESSAGE);
            $this->setStatusFail();
        }
    }
}