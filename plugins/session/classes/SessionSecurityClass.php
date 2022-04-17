<?php
namespace Sonder\Plugins\Session\Classes;

use Sonder\Plugins\Session\Interfaces\ISessionSecurity;

final class SessionSecurity implements ISessionSecurity
{
    /**
     * @param null $input
     *
     * @return string|array|null
     */
    final public function escapeInput($input = null): string|array|null
    {
        if (is_array($input)) {
            return array_map([$this, 'escapeInput'], $input);
        }

        if (empty($input)) {
            return null;
        }

        $input = (string) $input;

        $input = strip_tags($input);
        $input = htmlspecialchars($input);
        $input = addslashes($input);

        return preg_replace('/(^\s+)|(\s+$)/su', '', $input);
    }
}
