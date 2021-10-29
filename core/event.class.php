<?php

namespace Sonder\Core;

use Sonder\Core\Interfaces\IEvent;

final class CoreEvent implements IEvent
{
    const TYPE_BEFORE_MIDDLEWARES = 'before_middlewares';
    const TYPE_AFTER_MIDDLEWARES = 'after_middlewares';
    const TYPE_INIT_CONTROLLER = 'init_controller';
    const TYPE_BEFORE_RENDER = 'before_render';
    const TYPE_AFTER_RENDER = 'after_render';

    final public function run(string $type, array $values): array
    {
        $hookNames = (new ConfigObject)->get('hooks');
        $hookMethod = $this->_getHookMethodByType($type);

        foreach ($hookNames as $hookName) {
            $hookClass = sprintf(
                'Sonder\Hooks\%sHook',
                mb_convert_case($hookName, MB_CASE_TITLE)
            );

            if (
                class_exists($hookClass) &&
                method_exists($hookClass, $hookMethod)
            ) {
                $hook = new $hookClass($values);
                $hook->$hookMethod($values);

                $values = $hook->getValues();
            }
        }

        return $values;
    }

    private function _getHookMethodByType(string $type): string
    {
        $hookMethod = explode('_', $type);

        foreach ($hookMethod as $key => $hookMethodPart) {
            $hookMethod[$key] = mb_convert_case($hookMethodPart, MB_CASE_TITLE);
        }

        $hookMethod = implode('', $hookMethod);

        return sprintf('on%s', $hookMethod);
    }
}
