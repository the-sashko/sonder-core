<?php

namespace Sonder\Models\Role;

use Exception;
use Sonder\Core\Interfaces\IRoleActionValuesObject;
use Sonder\Core\ModelValuesObject;

final class RoleActionValuesObject
    extends ModelValuesObject
    implements IRoleActionValuesObject
{
    /**
     * @var string|null
     */
    protected ?string $editLinkPattern = '/admin/users/roles/action/%d/';

    /**
     * @var string|null
     */
    protected ?string $removeLinkPattern = '/admin/users/roles/actions/' .
    'remove/%d/';

    /**
     * @var string|null
     */
    protected ?string $restoreLinkPattern = '/admin/users/roles/actions/' .
    'restore/%d/';

    /**
     * @return string
     * @throws Exception
     */
    final public function getName(): string
    {
        return (string)$this->get('name');
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
}
