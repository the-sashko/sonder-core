<?php

namespace Sonder\Models\Role\Forms;

use Sonder\Core\ModelFormObject;
use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IModelFormObject;
use Sonder\Models\Role\Interfaces\IRoleActionForm;

#[IModelFormObject]
#[IRoleActionForm]
final class RoleActionForm extends ModelFormObject implements IRoleActionForm
{
    final public const NAME_EMPTY_ERROR_MESSAGE = 'Name is empty';

    final public const NAME_TOO_SHORT_ERROR_MESSAGE = 'Name is too short';

    final public const NAME_TOO_LONG_ERROR_MESSAGE = 'Name is too long';

    final public const NAME_EXISTS_ERROR_MESSAGE = 'Role Action with this name already exists';

    final public const ROLE_ACTION_NOT_EXISTS_ERROR_MESSAGE = 'Role Action with id "%d" not exists';

    final public const ROLE_ACTION_IS_SYSTEM_ERROR_MESSAGE = 'System role action can not be changed';

    private const NAME_MIN_LENGTH = 3;

    private const NAME_MAX_LENGTH = 255;

    /**
     * @return void
     * @throws ValuesObjectException
     */
    final public function checkInputValues(): void
    {
        $this->setStatusSuccess();

        $this->_validateNameValue();
    }

    /**
     * @return int|null
     * @throws ValuesObjectException
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
     * @throws ValuesObjectException
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
     * @throws ValuesObjectException
     */
    final public function isActive(): bool
    {
        if (!$this->has('is_active')) {
            return false;
        }

        return (bool)$this->get('is_active');
    }

    /**
     * @param int|null $id
     * @return void
     * @throws ValuesObjectException
     */
    final public function setId(?int $id = null): void
    {
        $this->set('id', $id);
    }

    /**
     * @param string|null $name
     * @return void
     * @throws ValuesObjectException
     */
    final public function setName(?string $name = null): void
    {
        $this->set('name', $name);
    }

    /**
     * @param bool $isActive
     * @return void
     * @throws ValuesObjectException
     */
    final public function setIsActive(bool $isActive = false): void
    {
        $this->set('is_active', $isActive);
    }

    /**
     * @return void
     * @throws ValuesObjectException
     */
    private function _validateNameValue(): void
    {
        $name = $this->getName();

        if (empty($name)) {
            $this->setError(RoleActionForm::NAME_EMPTY_ERROR_MESSAGE);
            $this->setStatusFail();
        }

        if (!empty($name) && mb_strlen(
                $name
            ) > RoleActionForm::NAME_MAX_LENGTH) {
            $this->setError(RoleActionForm::NAME_TOO_LONG_ERROR_MESSAGE);
            $this->setStatusFail();
        }

        if (!empty($name) && mb_strlen(
                $name
            ) < RoleActionForm::NAME_MIN_LENGTH) {
            $this->setError(RoleActionForm::NAME_TOO_SHORT_ERROR_MESSAGE);
            $this->setStatusFail();
        }
    }
}
