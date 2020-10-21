<?php
namespace Core\Plugins\Session\Interfaces;

interface ISessionSecurity
{
    public function escapeInput($input = null);
}
