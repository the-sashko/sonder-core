<?php

namespace Sonder\Models;

use Sonder\Core\CoreModel;
use Sonder\Core\Interfaces\IModel;
use Sonder\Core\Interfaces\IUser;
use Sonder\Models\User\UserVO;

final class User extends CoreModel implements IModel, IUser
{
    /**
     * @var IModel
     */
    private IModel $_role;

    /**
     * @var UserVO|null
     */
    private ?UserVO $_vo = null;

    final public function __construct()
    {
        parent::__construct();

        $this->_role = $this->getModel('role');
    }

    /**
     * @param string|null $authToken
     *
     * @return bool
     */
    final public function signInByToken(?string $authToken = null): bool
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
    final public function signInByLoginAndPassword(
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
    final public function isSignedIn(): bool
    {
        //TODO

        return false;
    }

    /**
     * @return int|null
     */
    final public function getId(): ?int
    {
        if (empty($this->vo)) {
            return null;
        }

        return $this->_vo->getId();
    }

    /**
     * @return string|null
     */
    final public function getLogin(): ?string
    {
        if (empty($this->vo)) {
            return null;
        }

        return $this->_vo->getLogin();
    }

    /**
     * @return IModel
     */
    final public function getRole(): IModel
    {
        return $this->_role;
    }

    //TODO
}
