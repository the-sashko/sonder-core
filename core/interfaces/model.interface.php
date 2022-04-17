<?php

namespace Sonder\Core\Interfaces;

interface IModel
{
    /**
     * @param array|null $formValues
     * @param string|null $formName
     * @return mixed
     */
    public function getForm(
        ?array  $formValues = null,
        ?string $formName = null
    ): mixed;
}
