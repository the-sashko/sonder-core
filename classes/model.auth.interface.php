<?php
/**
 * Interface Of Authentication/Authorization Model
 */
interface IModelAuth
{
    /**
     * Sign In By Auth Token
     *
     * @param string|null $authToken Authentication Token
     *
     * @return bool Is User Successfully Signed In
     */
    public function signInByToken(?string $authToken = null): bool;

    /**
     * Sign In By Login And Password
     *
     * @param string|null $login    User Login
     * @param string|null $password User Password
     *
     * @return bool Is User Successfully Signed In
     */
    public function signinByLoginAndPassword(
        ?string $login    = null,
        ?string $password = null
    ): bool;

    /**
     * Check Is User Signed In
     *
     * @return bool Is User Signed In
     */
    public function isSignedIn(): bool;

    /**
     * Signed Out User
     *
     * @return bool Is User Successfully Signed Out
     */
    public function signOut(): bool;
}
