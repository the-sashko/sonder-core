<?php
namespace Core\Plugins\Session\Classes;

use Core\Plugins\Session\Interfaces\ISessionSecurity;

class SessionSecurity implements ISessionSecurity
{
    /**
     * Sanitize Input Value: Remove Tags, Remove Special Charcters, Add Slashes
     *
     * @param mixed $input Input Value (String Or Array Of Strings)
     *
     * @return mixed Output Sanitized Value (String Or Array Of Strings)
     */
    public function escapeInput($input = null)
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
