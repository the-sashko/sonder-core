<?php

namespace Sonder\Models\Role\ValuesObjects;

use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IModelSimpleValuesObject;
use Sonder\Interfaces\IModelValuesObject;
use Sonder\Interfaces\IRoleActionValuesObject;
use Sonder\Interfaces\IValuesObject;
use Sonder\Models\Role\Interfaces\IRoleActionValuesObject as IRoleModelActionValuesObject;
use Sonder\Core\ModelValuesObject;

#[IValuesObject]
#[IModelSimpleValuesObject]
#[IModelValuesObject]
#[IRoleActionValuesObject]
#[IRoleModelActionValuesObject]
final class RoleActionValuesObject
    extends ModelValuesObject
    implements IRoleActionValuesObject, IRoleModelActionValuesObject
{
    final protected const EDIT_LINK_PATTERN = '/admin/users/roles/action/%d/';

    final protected const REMOVE_LINK_PATTERN = '/admin/users/roles/actions/remove/%d/';

    final protected const RESTORE_LINK_PATTERN = '/admin/users/roles/actions/restore/%d/';

    final protected const ADMIN_VIEW_LINK_PATTERN = '/admin/users/roles/actions/view/%d/';

    /**
     * @return string
     * @throws ValuesObjectException
     */
    final public function getName(): string
    {
        return (string)$this->get('name');
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
            RoleActionValuesObject::ADMIN_VIEW_LINK_PATTERN,
            $this->getId()
        );
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
}
