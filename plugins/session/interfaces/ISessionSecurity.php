<?php

namespace Sonder\Plugins\Session\Interfaces;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
interface ISessionSecurity
{
    /**
     * @param $input
     * @return string|array|null
     */
    public function escapeInput($input = null): string|array|null;
}
