<?php

namespace Sonder\Models;

use Exception;
use Sonder\Core\CoreModel;
use Sonder\Core\Interfaces\IModel;
use Sonder\Core\Interfaces\IRoleValuesObject;
use Sonder\Core\Interfaces\IUser;
use Sonder\Core\ValuesObject;

final class User extends CoreModel implements IModel, IUser
{
    /**
     * @var ValuesObject
     */
    private ValuesObject $_currentUserVO;

    final public function __construct()
    {
        parent::__construct();

        $sessionPlugin = $this->getPlugin('session');

        $id = $sessionPlugin->get('user_id');
        $webToken = $sessionPlugin->get('user_token');

        $row = $this->store->getRowByWebTokenAndId($webToken, $id);

        if (!empty($row)) {
            $this->_currentUserVO = $this->getVO($row);
        }
    }

    /**
     * @param string|null $apiToken
     * @return void
     * @throws Exception
     */
    final public function signInByApiToken(?string $apiToken = null): void
    {
        $row = $this->store->getRowByApiToken($apiToken);

        if (!empty($row)) {
            $this->_currentUserVO = $this->getVO($row);
        }
    }

    /**
     * @param string|null $login
     * @param string|null $password
     * @return bool
     * @throws Exception
     */
    final public function signInByLoginAndPassword(
        ?string $login = null,
        ?string $password = null
    ): bool
    {
        $cryptPlugin = $this->getPlugin('crypt');

        $salt = $this->config->getValue('crypt', 'salt');

        $passwordHash = $cryptPlugin->getHash(
            sprintf('%s%s', $login, $password),
            $salt
        );

        $row = $this->store->getRowByLoginAndPasswordHash(
            $login,
            $passwordHash
        );

        if (empty($row)) {
            return false;
        }

        $this->_currentUserVO = $this->getVO($row);

        $userUniqueString = sprintf(
            '%s%s%d',
            json_encode($row),
            microtime(),
            rand(0, 1000000000)
        );

        $webToken = $cryptPlugin->getHash($userUniqueString, $salt);

        $sessionPlugin = $this->getPlugin('session');

        $sessionPlugin->set('user_id', (int)$this->getId());
        $sessionPlugin->set('user_token', $webToken);

        return $this->store->updateWebTokenById($webToken, $this->getId());
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function signOut(): bool
    {
        if (!$this->isSignedIn()) {
            return false;
        }

        $sessionPlugin = $this->getPlugin('session');

        $sessionPlugin->remove('user_id');
        $sessionPlugin->remove('user_token');

        return $this->store->updateWebTokenById(null, $this->getId());
    }

    /**
     * @return bool
     */
    final public function isSignedIn(): bool
    {
        return !empty($this->getId());
    }

    /**
     * @return int|null
     */
    final public function getId(): ?int
    {
        if (empty($this->_currentUserVO)) {
            return null;
        }

        return $this->_currentUserVO->getId();
    }

    /**
     * @return string|null
     */
    final public function getLogin(): ?string
    {
        if (empty($this->_currentUserVO)) {
            return null;
        }

        return $this->_currentUserVO->getLogin();
    }

    /**
     * @return IRoleValuesObject
     * @throws Exception
     */
    final public function getRole(): IRoleValuesObject
    {
        $role = $this->_currentUserVO->getRoleVO();

        if (!empty($role)) {
            return $role;
        }

        $role = $this->getModel('role');

        return $role->getGuestVO();
    }

    /**
     * @param array|null $row
     * @return ValuesObject
     * @throws Exception
     */
    final protected function getVO(?array $row = null): ValuesObject
    {
        $userVO = parent::getVO($row);

        if (!empty($userVO)) {
            $role = $this->getModel('role');

            $roleVO = $role->getVOById($userVO->getRoleId());

            $userVO->setRoleVO($roleVO);
        }

        return $userVO;
    }
}
