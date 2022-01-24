<?php

namespace Sonder\Models\Role;

use Exception;
use Sonder\Core\ModelFormObject;

final class RoleActionForm extends ModelFormObject
{
    const NAME_EMPTY_ERROR_MESSAGE = 'Name is empty';

    const NAME_EXISTS_ERROR_MESSAGE = 'Role Action with this name already ' .
    'exists';

    const ROLE_ACTION_IS_NOT_EXISTS_ERROR_MESSAGE = 'Role Action with id ' .
    '"%d" is not exists';

    const ROLE_ACTION_IS_SYSTEM_ERROR_MESSAGE = 'System role action can not ' .
    'be changed';

    /**
     * @return void
     * @throws Exception
     */
    final public function checkInputValues(): void
    {
        $this->setStatusSuccess();

        $this->_validateNameValue();
    }

    /**
     * @return int|null
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
     * @return string|null
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
     * @return bool
     * @throws Exception
     */
    final public function getIsActive(): bool
    {
        if (!$this->has('is_active')) {
            return false;
        }

        return (bool)$this->get('is_active');
    }

    /**
     * @param int|null $id
     * @return void
     * @throws Exception
     */
    final public function setId(?int $id = null): void
    {
        $this->set('id', $id);
    }

    /**
     * @param string|null $name
     * @return void
     * @throws Exception
     */
    final public function setName(?string $name = null): void
    {
        $this->set('name', $name);
    }

    /**
     * @param bool $isActive
     * @return void
     * @throws Exception
     */
    final public function setIsActive(bool $isActive = false): void
    {
        $this->set('is_active', $isActive);
    }

    /**
     * @return void
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
