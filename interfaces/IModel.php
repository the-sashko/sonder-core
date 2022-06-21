<?php

namespace Sonder\Interfaces;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
interface IModel
{
    /**
     * @param array|null $formValues
     * @param string|null $formName
     * @return IModelFormObject|null
     */
    public function getForm(
        ?array $formValues = null,
        ?string $formName = null
    ): ?IModelFormObject;
}
