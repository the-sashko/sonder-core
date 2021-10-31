<?php

namespace Sonder\Models;

use Sonder\Core\CoreModel;
use Sonder\Core\Interfaces\IModel;
use Sonder\Core\Interfaces\IUser;

class User extends CoreModel implements IModel, IUser
{
    /**
     * @var IModel
     */
    private IModel $_role;

    public function __construct()
    {
        parent::__construct();

        $this->_role = $this->getModel('role');
    }

    /**
     * @param string|null $authToken
     *
     * @return bool
     */
    public function signInByToken(?string $authToken = null): bool
    {
        //TODO

        return false;
    }

    /**
     * @param string|null $login
     * @param string|null $password
     *
     * @return bool
     */
    public function signInByLoginAndPassword(
        ?string $login = null,
        ?string $password = null
    ): bool
    {
        //TODO

        return false;
    }

    /**
     * @return bool
     */
    public function signOut(): bool
    {
        //TODO

        return false;
    }

    /**
     * @return bool
     */
    public function isSignedIn(): bool
    {
        //TODO

        return false;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        //TODO

        return null;
    }

    /**
     * @return string|null
     */
    public function getLogin(): ?string
    {
        //TODO

        return null;
    }

    /**
     * @return IModel
     */
    public function getRole(): IModel
    {
        return $this->_role;
    }

    //TODO
}
