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
     * @var string|null
     */
    protected ?string $adminViewLinkPattern = '/admin/users/roles/actions/' .
    'view/%d/';

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
    final public function isSystem(): bool
    {
        return (bool)$this->get('is_system');
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getAdminViewLink(): string
    {
        return sprintf($this->adminViewLinkPattern, $this->getId());
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
