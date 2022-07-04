<?php

namespace Sonder\Models;

use Sonder\Core\CoreModel;
use Sonder\Enums\ConfigNamesEnum;
use Sonder\Exceptions\ConfigException;
use Sonder\Exceptions\CoreException;
use Sonder\Exceptions\ModelException;
use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IModel;
use Sonder\Interfaces\IRoleValuesObject;
use Sonder\Interfaces\IUserModel as IUserModelFramework;
use Sonder\Models\Role\Interfaces\IRoleSimpleValuesObject;
use Sonder\Models\User\Forms\CredentialsForm;
use Sonder\Models\User\Forms\UserForm;
use Sonder\Models\User\Interfaces\ICredentialsForm;
use Sonder\Models\User\Interfaces\IUserForm;
use Sonder\Models\User\Interfaces\IUserModel;
use Sonder\Models\User\Interfaces\IUserSimpleValuesObject;
use Sonder\Models\User\Interfaces\IUserStore;
use Sonder\Models\User\Interfaces\IUserValuesObject;
use Sonder\Models\User\ValuesObjects\UserValuesObject;
use Sonder\Models\User\ValuesObjects\UserSimpleValuesObject;
use Sonder\Models\Role\Interfaces\IRoleModel;
use Sonder\Plugins\TranslitPlugin;
use Throwable;

/**
 * @property null $api
 * @property IUserStore $store
 */
#[IModel]
#[IUserModelFramework]
#[IUserModel]
final class UserModel extends CoreModel implements IUserModel,
                                                   IUserModelFramework
{
    final protected const ITEMS_ON_PAGE = 10;

    private const SALT_CONFIG_VALUE = 'salt';

    /**
     * @var IUserValuesObject
     */
    #[IUserValuesObject]
    private IUserValuesObject $_currentUserVO;

    /**
     * @throws ConfigException
     * @throws CoreException
     * @throws ModelException
     * @throws ValuesObjectException
     */
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
     * @throws CoreException
     * @throws ModelException
     * @throws ValuesObjectException
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
     * @throws ConfigException
     * @throws CoreException
     * @throws ModelException
     * @throws ValuesObjectException
     */
    final public function signInByLoginAndPassword(
        ?string $login = null,
        ?string $password = null
    ): bool {
        $cryptPlugin = $this->getPlugin('crypt');

        $salt = $this->config->getValue(
            ConfigNamesEnum::CRYPT,
            UserModel::SALT_CONFIG_VALUE
        );

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
     * @throws CoreException
     * @throws ValuesObjectException
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
     * @throws ValuesObjectException
     */
    final public function isSignedIn(): bool
    {
        return !empty($this->getId());
    }

    /**
     * @param int|null $id
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return IUserValuesObject|null
     * @throws CoreException
     * @throws ModelException
     * @throws ValuesObjectException
     */
    final public function getVOById(
        ?int $id = null,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?IUserValuesObject {
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
     * @return IUserSimpleValuesObject|null
     * @throws CoreException
     * @throws ModelException
     * @throws ValuesObjectException
     */
    final public function getSimpleVOById(
        ?int $id = null
    ): ?IUserSimpleValuesObject {
        $row = $this->store->getUserRowById($id);

        if (!empty($row)) {
            return $this->getSimpleVO($row);
        }

        return null;
    }

    /**
     * @return int|null
     * @throws ValuesObjectException
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
     * @throws CoreException
     * @throws ModelException
     * @throws Role\Exceptions\RoleModelException
     * @throws ValuesObjectException
     */
    final public function getRole(): IRoleValuesObject
    {
        $roleVO = $this->_currentUserVO->getRoleVO();

        if (!empty($roleVO)) {
            return $roleVO;
        }

        /* @var $roleModel RoleModel */
        $roleModel = $this->getModel('role');

        return $roleModel->getGuestVO();
    }

    /**
     * @return IRoleSimpleValuesObject
     * @throws CoreException
     * @throws ModelException
     * @throws Role\Exceptions\RoleModelException
     * @throws ValuesObjectException
     */
    final public function getRoleSimpleVO(): IRoleSimpleValuesObject
    {
        /* @var $roleModel RoleModel */
        $roleModel = $this->getModel('role');

        $roleVO = $this->_currentUserVO->getRoleVO();

        if (!empty($roleVO)) {
            /* @var $roleSimpleVO IRoleSimpleValuesObject */
            $roleSimpleVO = $roleModel->simplifyVO($roleVO);

            return $roleSimpleVO;
        }

        return $roleModel->getGuestSimpleVO();
    }

    /**
     * @param int $page
     * @param bool $excludeRemoved
     * @param bool $excludeInactive
     * @return array|null
     * @throws ModelException
     */
    final public function getUsersByPage(
        int $page,
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): ?array {
        $rows = $this->store->getUserRowsByPage(
            $page,
            UserModel::ITEMS_ON_PAGE,
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
     */
    final public function getUsersPageCount(
        bool $excludeRemoved = true,
        bool $excludeInactive = true
    ): int {
        $rowsCount = $this->store->getUserRowsCount(
            $excludeRemoved,
            $excludeInactive
        );

        $pageCount = (int)($rowsCount / UserModel::ITEMS_ON_PAGE);

        if ($pageCount * UserModel::ITEMS_ON_PAGE < $rowsCount) {
            $pageCount++;
        }

        return $pageCount;
    }

    /**
     * @param int|null $id
     * @return bool
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
     */
    final public function restoreById(?int $id = null): bool
    {
        if (empty($id)) {
            return false;
        }

        return $this->store->restoreUserById($id);
    }

    /**
     * @param IUserForm $userForm
     * @return bool
     * @throws ConfigException
     * @throws CoreException
     * @throws ValuesObjectException
     */
    final public function save(IUserForm $userForm): bool
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
     * @param ICredentialsForm $credentialsForm
     * @return bool
     * @throws ConfigException
     * @throws CoreException
     * @throws ValuesObjectException
     */
    final public function saveCredentials(
        ICredentialsForm $credentialsForm
    ): bool {
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

        $userVO->setMdate();

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
     * @param array|null $row
     * @return IUserValuesObject
     * @throws CoreException
     * @throws ModelException
     * @throws ValuesObjectException
     */
    final protected function getVO(?array $row = null): IUserValuesObject
    {
        /* @var $userVO UserValuesObject */
        $userVO = parent::getVO($row);

        if (!empty($userVO)) {
            /* @var $role IRoleModel */
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
     * @return IUserSimpleValuesObject
     * @throws CoreException
     * @throws ValuesObjectException
     * @throws ModelException
     */
    final protected function getSimpleVO(
        ?array $row = null
    ): IUserSimpleValuesObject {
        /* @var $userVO UserSimpleValuesObject */
        $userVO = parent::getSimpleVO($row);

        if (!empty($userVO)) {
            /* @var $role IRoleModel */
            $role = $this->getModel('role');

            $simpleRoleVO = $role->getRoleSimpleVOById($userVO->getRoleId());

            $userVO->setRoleVO($simpleRoleVO);
        }

        return $userVO;
    }

    /**
     * @param IUserForm $userForm
     * @return void
     * @throws ConfigException
     * @throws CoreException
     * @throws ValuesObjectException
     */
    private function _checkIdInUserForm(IUserForm $userForm): void
    {
        $id = $userForm->getId();

        if (empty($id)) {
            return;
        }

        $userVO = $this->_getVOFromUserForm($userForm);

        if (empty($userVO)) {
            $userForm->setStatusFail();

            $userForm->setError(
                sprintf(
                    UserForm::USER_NOT_EXISTS_ERROR_MESSAGE,
                    $id
                )
            );
        }
    }

    /**
     * @param IUserForm $userForm
     * @param bool $isCreateVOIfEmptyId
     * @return IUserValuesObject|null
     * @throws ConfigException
     * @throws CoreException
     * @throws ValuesObjectException
     */
    private function _getVOFromUserForm(
        IUserForm $userForm,
        bool $isCreateVOIfEmptyId = false
    ): ?IUserValuesObject {
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
     * @param ICredentialsForm $credentialsForm
     * @return IUserValuesObject|null
     * @throws ConfigException
     * @throws CoreException
     * @throws ValuesObjectException
     */
    private function _getVOFromCredentialsForm(
        ICredentialsForm $credentialsForm
    ): ?IUserValuesObject {
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
     * @param IUserForm $userForm
     * @return void
     * @throws CoreException
     */
    private function _checkLoginInUserForm(IUserForm $userForm): void
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
     * @param ICredentialsForm $credentialsForm
     * @return void
     * @throws CoreException
     */
    private function _checkLoginInCredentialsForm(
        ICredentialsForm $credentialsForm
    ): void {
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
     * @param IUserForm $userForm
     * @return void
     */
    private function _checkEmailInUserForm(IUserForm $userForm): void
    {
        $email = $userForm->getEmail();

        $email = preg_replace('/\s+/u', '', $email);
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
     * @param IUserForm $userForm
     * @return void
     * @throws CoreException
     */
    private function _checkRoleIdInUserForm(IUserForm $userForm): void
    {
        /* @var $roleModel IRoleModel */
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
     * @throws ConfigException
     * @throws CoreException
     */
    private function _getPasswordHashByLoginAndPassword(
        ?string $login = null,
        ?string $password = null
    ): ?string {
        if (empty($login) || empty($password)) {
            return null;
        }

        $cryptPlugin = $this->getPlugin('crypt');

        $salt = $this->config->getValue(
            ConfigNamesEnum::CRYPT,
            UserModel::SALT_CONFIG_VALUE
        );

        return $cryptPlugin->getHash(
            sprintf('%s%s', $login, $password),
            $salt
        );
    }

    /**
     * @param IUserValuesObject|null $userVO
     * @return string|null
     * @throws ConfigException
     * @throws CoreException
     */
    private function _getApiTokenFromVO(?IUserValuesObject $userVO): ?string
    {
        if (empty($userVO)) {
            return null;
        }

        $cryptPlugin = $this->getPlugin('crypt');

        $salt = $this->config->getValue(
            ConfigNamesEnum::CRYPT,
            UserModel::SALT_CONFIG_VALUE
        );

        $userUniqueString = sprintf(
            '%s%d%s',
            json_encode($userVO->exportRow()),
            rand(0, time()),
            microtime()
        );

        return $cryptPlugin->getHash($userUniqueString, $salt);
    }
}
