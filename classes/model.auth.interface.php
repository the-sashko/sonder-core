<?php
/**
 * Interface Of Authentication/Authorization Model
 */
interface IModelAuth
{
    /**
     * Check Authentication Token
     *
     * @param string $authToken Authentication Token
     *
     * @return bool Is Authentication Token Valid
     */
    public function checkToken(string $authToken = '') : bool;

    /**
     * Check Login And Password
     *
     * @param string $login    User Login
     * @param string $password User Password
     *
     * @return array Are Login And Password Valid
     */
    public function checkLoginAndPassword(
        string $login = '',
        string $password = ''
    ) : void;

    /**
     * Sign In By Auth Token
     *
     * @param string $authToken Authentication Token
     *
     * @return bool Is User Successfully Signed In
     */
    public function signInByToken(string $authToken = '') : bool;

    /**
     * Sign In By Login And Password
     *
     * @param string $login    User Login
     * @param string $password User Password
     *
     * @return bool Is User Successfully Signed In
     */
    public function signinByLoginAndPassword(
        string $login = '',
        string $password = ''
    ) : void;

    /**
     * Check Is User Signed In
     *
     * @return bool Is User Signed In
     */
    public function isSignedIn() : bool;

    /**
     * Signed Out User
     *
     * @return bool Is User Successfully Signed Out
     */
    public function signout() : bool;

    /**
     * Ban User
     *
     * @return bool Is User Successfully Added To Ban
     */
    public function add2ban() : bool;

    /**
     * Remove User From
     *
     * @return bool Is User Successfully Removed From Ban
     */
    public function removeFromBan() : bool;

    /**
     * Check Is User Banned
     *
     * @return bool Is User Banned
     */
    public function isBanned() : bool;
}
