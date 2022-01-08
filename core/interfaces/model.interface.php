<?php

namespace Sonder\Core\Interfaces;

use Sonder\Core\ModelFormObject;

interface IModel
{
    /**
     * @param array|null $formValues
     * @param string|null $formName
     * @return ModelFormObject|null
     */
    public function getForm(
        ?array  $formValues = null,
        ?string $formName = null
    ): ?ModelFormObject;
}
