<?php

namespace Sonder\Plugins\Session\Interfaces;

interface ISessionSecurity
{
    /**
     * @param null $input
     *
     * @return mixed
     */
    public function escapeInput($input = null): mixed;
}
