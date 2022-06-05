<?php

namespace Sonder\Models;

use Exception;
use Sonder\Core\CoreModel;
use Sonder\Core\Interfaces\IModel;
use Sonder\Core\Interfaces\IRole;
use Sonder\Core\Interfaces\IRoleValuesObject;
use Sonder\Core\Interfaces\IUser;
use Sonder\Core\ValuesObject;
use Sonder\Models\User\CredentialsForm;
use Sonder\Models\User\UserForm;
use Sonder\Models\User\UserSimpleValuesObject;
use Sonder\Models\User\UserStore;
use Sonder\Models\User\UserValuesObject;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;
use Sonder\Plugins\TranslitPlugin;
use Throwable;


/**
 * @property UserStore $store
 */
final class User extends CoreModel implements IModel, IUser
{
    /**
     * @var int
     */
    protected int $itemsOnPage = 10;

    /**
     * @var UserValuesObject
     */
    private UserValuesObject $_currentUserVO;

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
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
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
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function signInByLoginAndPassword(
        ?string $login = null,
        ?string $password = null
    ): bool
    {
        $cryptPlugin = $this->getPlugin('crypt');

        $salt = $this->config->getValue('crypt', 'salt');

        $passwordHash = $this->_getPasswordHashByLoginAndPassword(
            $login,
            $password
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

        return $this->store->updateWebTokenById(
            $webToken,
            $this->getId(),
            time()
        );
    }

    /**
     * @return bool
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function signOut(): bool
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
     * @throws Exception
     */
    final public function isSignedIn(): bool
    {
        return !empty($this->getId());
    }

    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return UserValuesObject|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getVOById(
        ?int $id = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?UserValuesObject
    {
        $row = $this->store->getUserRowById(
            $id,
            $excludeRemoved,
            $excludeInactive
        );

        if (!empty($row)) {
            return $this->getVO($row);
        }

        return null;
    }

    /**
     * @param int|null $id
     * @return UserSimpleValuesObject|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function getSimpleVOById(
        ?int $id = null
    ): ?UserSimpleValuesObject
    {
        $row = $this->store->getUserRowById($id);

        if (!empty($row)) {
            return $this->getSimpleVO($row);
        }

        return null;
    }

    /**
     * @return int|null
     * @throws Exception
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
     * @throws Exception
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

        /* @var $roleModel Role */
        $roleModel = $this->getModel('role');

        return $roleModel->getGuestVO();
    }

    /**
     * @return IRoleValuesObject
     * @throws Exception
     */
    final public function getRoleSimpleVO(): IRoleValuesObject
    {
        /* @var $role ValuesObject */
        $role = $this->_currentUserVO->getRoleVO();

        if (!empty($role)) {
            /* @var $simpleRole IRoleValuesObject */
            $simpleRole = $this->simplifyVO($role);

            return $simpleRole;
        }

        /* @var $roleModel Role */
        $roleModel = $this->getModel('role');

        return $roleModel->getGuestSimpleVO();
    }

    /**
     * @param int $page
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getUsersByPage(
        int  $page,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array
    {
        $rows = $this->store->getUserRowsByPage(
            $page,
            $this->itemsOnPage,
            $excludeRemoved,
            $excludeInactive
        );

        if (empty($rows)) {
            return null;
        }

        return $this->getVOArray($rows);
    }

    /**
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return int
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function getUsersPageCount(
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int
    {
        $rowsCount = $this->store->getUserRowsCount(
            $excludeRemoved,
            $excludeInactive
        );

        $pageCount = (int)($rowsCount / $this->itemsOnPage);

        if ($pageCount * $this->itemsOnPage < $rowsCount) {
            $pageCount++;
        }

        return $pageCount;
    }

    /**
     * @param array|null $row
     * @return UserValuesObject
     * @throws Exception
     */
    final protected function getVO(?array $row = null): ValuesObject
    {
        /* @var $userVO UserValuesObject */
        $userVO = parent::getVO($row);

        if (!empty($userVO)) {
            /* @var $role IRole */
            $role = $this->getModel('role');

            $roleVO = $role->getVOById($userVO->getRoleId());

            if (empty($roleVO)) {
                $roleVO = $role->getGuestVO();
            }

            $userVO->setRoleVO($roleVO);
        }

        return $userVO;
    }

    /**
     * @param array|null $row
     * @return UserValuesObject
     * @throws Exception
     */
    final protected function getSimpleVO(?array $row = null): ValuesObject
    {
        /* @var $userVO UserSimpleValuesObject */
        $userVO = parent::getSimpleVO($row);

        if (!empty($userVO)) {
            /* @var $role IRole */
            $role = $this->getModel('role');

            $simpleRoleVO = $role->getRoleSimpleVOById($userVO->getRoleId());

            $userVO->setRoleVO($simpleRoleVO);
        }

        return $userVO;
    }

    /**
     * @param int|null $id
     * @return bool
     * @throws DatabasePluginException
     */
    final public function removeById(?int $id = null): bool
    {
        if (empty($id)) {
            return false;
        }

        return $this->store->deleteUserById($id);
    }

    /**
     * @param int|null $id
     * @return bool
     * @throws DatabasePluginException
     */
    final public function restoreById(?int $id = null): bool
    {
        if (empty($id)) {
            return false;
        }

        return $this->store->restoreUserById($id);
    }

    /**
     * @param UserForm $userForm
     * @return bool
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function save(UserForm $userForm): bool
    {
        $userForm->checkInputValues();

        if (!$userForm->getStatus()) {
            return false;
        }

        $this->_checkIdInUserForm($userForm);
        $this->_checkLoginInUserForm($userForm);
        $this->_checkEmailInUserForm($userForm);
        $this->_checkRoleIdInUserForm($userForm);

        if (!$userForm->getStatus()) {
            return false;
        }

        $userVO = $this->_getVOFromUserForm($userForm, true);

        try {
            if (!$this->store->insertOrUpdateUser($userVO)) {
                $userForm->setStatusFail();

                return false;
            }

            $id = $this->store->getUserIdByLogin($userForm->getLogin());

            if (!empty($id)) {
                $userForm->setId($id);
            }
        } catch (Throwable $thr) {
            $userForm->setStatusFail();
            $userForm->setError($thr->getMessage());

            return false;
        }

        return true;
    }

    /**
     * @param CredentialsForm $credentialsForm
     * @return bool
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function saveCredentials(
        CredentialsForm $credentialsForm
    ): bool
    {
        $credentialsForm->checkInputValues();

        if (!$credentialsForm->getStatus()) {
            return false;
        }

        $id = $credentialsForm->getId();

        if (empty($id)) {
            $credentialsForm->setStatusFail();

            $credentialsForm->setError(
                CredentialsForm::USER_NOT_EXISTS_ERROR_MESSAGE
            );

            return false;
        }

        $this->_checkLoginInCredentialsForm($credentialsForm);

        $userVO = $this->_getVOFromCredentialsForm($credentialsForm);

        if (empty($userVO)) {
            $credentialsForm->setStatusFail();

            $credentialsForm->setError(
                CredentialsForm::USER_NOT_EXISTS_ERROR_MESSAGE
            );

            return false;
        }

        if (!$credentialsForm->getStatus()) {
            return false;
        }

        $apiToken = null;

        if ($credentialsForm->isAllowAccessByApi()) {
            $apiToken = $userVO->getApiToken();
        }

        $passwordHash = $userVO->getPasswordHash();

        $userVO->setMdate();

        $row = $userVO->exportRow();

        $row['api_token'] = $apiToken;

        if (!empty($passwordHash)) {
            $row['password_hash'] = $passwordHash;
        }
        $credentialsForm->checkInputValues();

        if (!$credentialsForm->getStatus()) {
            return false;
        }

        $id = $credentialsForm->getId();

        if (empty($id)) {
            $credentialsForm->setStatusFail();

            $credentialsForm->setError(
                CredentialsForm::USER_NOT_EXISTS_ERROR_MESSAGE
            );

            return false;
        }

        $this->_checkLoginInCredentialsForm($credentialsForm);

        $userVO = $this->_getVOFromCredentialsForm($credentialsForm);

        if (empty($userVO)) {
            $credentialsForm->setStatusFail();

            $credentialsForm->setError(
                CredentialsForm::USER_NOT_EXISTS_ERROR_MESSAGE
            );

            return false;
        }

        if (!$credentialsForm->getStatus()) {
            return false;
        }

        $apiToken = null;

        if ($credentialsForm->isAllowAccessByApi()) {
            $apiToken = $userVO->getApiToken();
        }

        $passwordHash = $userVO->getPasswordHash();

        $userVO->setMdate();

        $row = $userVO->exportRow();

        $row['api_token'] = $apiToken;

        if (!empty($passwordHash)) {
            $row['password_hash'] = $passwordHash;
        }

        try {
            if (
                !$this->store->updateUserById($row, $credentialsForm->getId())
            ) {
                $credentialsForm->setStatusFail();

                return false;
            }
        } catch (Throwable $thr) {
            $credentialsForm->setStatusFail();
            $credentialsForm->setError($thr->getMessage());

            return false;
        }

        return true;
    }

    /**
     * @param UserForm $userForm
     * @return bool
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _checkIdInUserForm(UserForm $userForm): bool
    {
        $id = $userForm->getId();

        if (empty($id)) {
            return true;
        }

        $userVO = $this->_getVOFromUserForm($userForm);

        if (empty($userVO)) {
            $userForm->setStatusFail();

            $userForm->setError(sprintf(
                UserForm::USER_NOT_EXISTS_ERROR_MESSAGE,
                $id
            ));

            return false;
        }

        return true;
    }

    /**
     * @param UserForm $userForm
     * @param bool $isCreateVOIfEmptyId
     * @return UserValuesObject|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _getVOFromUserForm(
        UserForm $userForm,
        bool     $isCreateVOIfEmptyId = false
    ): ?UserValuesObject
    {
        $row = null;

        $id = $userForm->getId();

        if (empty($id) && !$isCreateVOIfEmptyId) {
            return null;
        }

        if (!empty($id)) {
            $row = $this->store->getUserRowById(
                $id,
                false,
                false
            );
        }

        if (!empty($id) && empty($row)) {
            return null;
        }

        $userVO = new UserValuesObject($row);

        if (empty($id)) {
            $passwordHash = $this->_getPasswordHashByLoginAndPassword(
                $userForm->getLogin(),
                $userForm->getPassword()
            );

            $userVO->setPasswordHash($passwordHash);
        }

        if (empty($id) && $userForm->isAllowAccessByApi()) {
            $apiToken = $this->_getApiTokenFromVO($userVO);
            $userVO->setApiToken($apiToken);
        }

        $userVO->setLogin($userForm->getLogin());
        $userVO->setEmail($userForm->getEmail());
        $userVO->setIsActive($userForm->isActive());
        $userVO->setRoleId($userForm->getRoleId());

        return $userVO;
    }

    /**
     * @param CredentialsForm $credentialsForm
     * @return UserValuesObject|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _getVOFromCredentialsForm(
        CredentialsForm $credentialsForm
    ): ?UserValuesObject
    {
        $id = $credentialsForm->getId();

        if (empty($id)) {
            return null;
        }

        $row = $this->store->getUserRowById(
            $id,
            false,
            false
        );

        if (empty($row)) {
            return null;
        }

        $userVO = new UserValuesObject($row);

        if (
            $credentialsForm->isAllowAccessByApi() &&
            empty($userVO->getApiToken())
        ) {
            $apiToken = $this->_getApiTokenFromVO($userVO);

            $userVO->setApiToken($apiToken);
        }

        if (
            $credentialsForm->getLogin() != $userVO->getLogin() &&
            empty($credentialsForm->getPassword())
        ) {
            $credentialsForm->setStatusFail();

            $credentialsForm->setError(
                CredentialsForm::PASSWORD_EMPTY_ERROR_MESSAGE
            );
        }

        $userVO->setLogin($credentialsForm->getLogin());

        if (!empty($credentialsForm->getPassword())) {
            $passwordHash = $this->_getPasswordHashByLoginAndPassword(
                $credentialsForm->getLogin(),
                $credentialsForm->getPassword()
            );

            $userVO->setPasswordHash($passwordHash);
        }


        return $userVO;
    }

    /**
     * @param UserForm $userForm
     * @return void
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    private function _checkLoginInUserForm(UserForm $userForm): void
    {
        /* @var $translitPlugin TranslitPlugin */
        $translitPlugin = $this->getPlugin('translit');

        $login = $userForm->getLogin();
        $login = $translitPlugin->getSlug($login);

        $userForm->setLogin($login);

        if (empty($login)) {
            $userForm->setStatusFail();
            $userForm->setError(UserForm::LOGIN_EMPTY_ERROR_MESSAGE);
        }

        if (
            !empty($login) &&
            !$this->_isLoginUniq($login, $userForm->getId())
        ) {
            $userForm->setStatusFail();
            $userForm->setError(UserForm::LOGIN_EXISTS_ERROR_MESSAGE);
        }
    }

    /**
     * @param CredentialsForm $credentialsForm
     * @return void
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    private function _checkLoginInCredentialsForm(
        CredentialsForm $credentialsForm
    ): void
    {
        /* @var $translitPlugin TranslitPlugin */
        $translitPlugin = $this->getPlugin('translit');

        $login = $credentialsForm->getLogin();
        $login = $translitPlugin->getSlug($login);

        $credentialsForm->setLogin($login);

        if (empty($login)) {
            $credentialsForm->setStatusFail();

            $credentialsForm->setError(
                CredentialsForm::LOGIN_EMPTY_ERROR_MESSAGE
            );
        }

        if (
            !empty($login) &&
            !$this->_isLoginUniq($login, $credentialsForm->getId())
        ) {
            $credentialsForm->setStatusFail();

            $credentialsForm->setError(
                CredentialsForm::LOGIN_EXISTS_ERROR_MESSAGE
            );
        }
    }

    /**
     * @param UserForm $userForm
     * @return void
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    private function _checkEmailInUserForm(UserForm $userForm): void
    {
        $email = $userForm->getEmail();

        $email = preg_replace('/\s+/su', '', $email);
        $email = mb_convert_case($email, MB_CASE_LOWER);

        $userForm->setEmail($email);

        if (empty($email)) {
            $userForm->setStatusFail();
            $userForm->setError(UserForm::EMAIL_EMPTY_ERROR_MESSAGE);
        }

        if (
            !empty($email) &&
            !preg_match(UserForm::EMAIL_PATTERN, $email)
        ) {
            $userForm->setStatusFail();
            $userForm->setError(
                UserForm::EMAIL_HAS_BAD_FORMAT_ERROR_MESSAGE
            );
        }

        if (
            !empty($email) &&
            !$this->_isEmailUniq($email, $userForm->getId())
        ) {
            $userForm->setStatusFail();
            $userForm->setError(UserForm::EMAIL_EXISTS_ERROR_MESSAGE);
        }
    }

    /**
     * @param string|null $login
     * @param int|null $id
     * @return bool
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    private function _isLoginUniq(?string $login = null, ?int $id = null): bool
    {
        $row = $this->store->getUserRowByLogin(
            $login,
            $id,
            false,
            false
        );

        return empty($row);
    }

    /**
     * @param string|null $email
     * @param int|null $id
     * @return bool
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    private function _isEmailUniq(?string $email = null, ?int $id = null): bool
    {
        $row = $this->store->getUserRowByEmail(
            $email,
            $id,
            false,
            false
        );

        return empty($row);
    }

    /**
     * @param UserForm $userForm
     * @return void
     * @throws Exception
     */
    private function _checkRoleIdInUserForm(UserForm $userForm): void
    {
        /* @var $roleModel Role */
        $roleModel = $this->getModel('role');

        $roleId = $userForm->getRoleId();

        if (empty($roleModel->getVOById($roleId))) {
            $userForm->setStatusFail();

            $userForm->setError(
                UserForm::ROLE_NOT_EXISTS_ERROR_MESSAGE
            );
        }
    }

    /**
     * @param string|null $login
     * @param string|null $password
     * @return string|null
     * @throws Exception
     */
    private function _getPasswordHashByLoginAndPassword(
        ?string $login = null,
        ?string $password = null
    ): ?string
    {
        if (empty($login) || empty($password)) {
            return null;
        }

        $cryptPlugin = $this->getPlugin('crypt');

        $salt = $this->config->getValue('crypt', 'salt');

        return $cryptPlugin->getHash(
            sprintf('%s%s', $login, $password),
            $salt
        );
    }

    /**
     * @param UserValuesObject|null $userVO
     * @return string|null
     * @throws Exception
     */
    private function _getApiTokenFromVO(?UserValuesObject $userVO): ?string
    {
        if (empty($userVO)) {
            return null;
        }

        $cryptPlugin = $this->getPlugin('crypt');

        $salt = $this->config->getValue('crypt', 'salt');

        $userUniqueString = sprintf(
            '%s%d%s',
            json_encode($userVO->exportRow()),
            rand(0, time()),
            microtime()
        );

        return $cryptPlugin->getHash($userUniqueString, $salt);
    }
}
