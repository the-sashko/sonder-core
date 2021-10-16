<?php

namespace Sonder\Plugins;

final class SecurityPlugin
{
    /**
     * @param mixed|null $input
     *
     * @return mixed
     */
    final public function escapeInput(mixed $input = null): mixed
    {
        if (is_array($input)) {
            return array_map([$this, 'escapeInput'], $input);
        }

        if (empty($input)) {
            return null;
        }

        if (!is_string($input)) {
            return $input;
        }

        $input = strip_tags($input);
        $input = htmlspecialchars($input);

        return addslashes($input);
    }
}
