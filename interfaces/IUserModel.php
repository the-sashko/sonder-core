<?php

namespace Sonder\Interfaces;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
interface IUserModel
{
    /**
     * @param string|null $apiToken
     * @return void
     */
    public function signInByApiToken(?string $apiToken = null): void;

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
     * @return IRoleValuesObject
     */
    public function getRole(): IRoleValuesObject;
}
