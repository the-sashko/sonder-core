<?php

namespace Sonder\Core;

use Sonder\Enums\ConfigNamesEnum;
use Sonder\Exceptions\ConfigException;
use Sonder\Interfaces\IEvent;
use Sonder\Interfaces\IEventTypesEnum;

#[IEvent]
final class CoreEvent implements IEvent
{
    /**
     * @param IEventTypesEnum $type
     * @param array $values
     * @return array
     * @throws ConfigException
     */
    final public function run(IEventTypesEnum $type, array $values): array
    {
        $hookNames = (new ConfigObject)->get(ConfigNamesEnum::HOOKS);

        if (defined('APP_SYSTEM_HOOKS')) {
            $hookNames = array_merge($hookNames, APP_SYSTEM_HOOKS);
            $hookNames = array_unique($hookNames);
        }

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
                $hook->$hookMethod();

                $values = $hook->getValues();
            }
        }

        return $values;
    }

    /**
     * @param IEventTypesEnum $type
     * @return string
     */
    private function _getHookMethodByType(IEventTypesEnum $type): string
    {
        $hookMethod = explode('_', $type->value);

        foreach ($hookMethod as $key => $hookMethodPart) {
            $hookMethod[$key] = mb_convert_case($hookMethodPart, MB_CASE_TITLE);
        }

        $hookMethod = implode('', $hookMethod);

        return sprintf('on%s', $hookMethod);
    }
}
