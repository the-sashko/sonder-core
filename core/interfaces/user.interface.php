<?php

namespace Sonder\Core\Interfaces;

interface IUser
{
    /**
     * @param string|null $authToken
     * @return bool
     */
    public function signInByToken(?string $authToken = null): bool;

    /**
     * @param string|null $login
     * @param string|null $password
     * @return bool
     */
    public function signInByLoginAndPassword(
        ?string $login = null,
        ?string $password = null
    ): bool;

    /**
     * @return bool
     */
    public function signOut(): bool;

    /**
     * @return bool
     */
    public function isSignedIn(): bool;

    /**
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * @return string|null
     */
    public function getLogin(): ?string;

    /**
     * @return IModel
     */
    public function getRole(): IModel;
}
