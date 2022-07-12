<?php

namespace Sonder\Models\Config\ValuesObjects;

use Sonder\Core\ModelValuesObject;
use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IModelValuesObject;
use Sonder\Interfaces\IValuesObject;
use Sonder\Model\Config\Exceptions\ConfigException;
use Sonder\Model\Config\Exceptions\ConfigValuesObjectException;
use Sonder\Models\Config\Interfaces\IConfigValuesObject;

#[IValuesObject]
#[IModelValuesObject]
#[IConfigValuesObject]
final class ConfigValuesObject
    extends ModelValuesObject
    implements IConfigValuesObject
{
    final protected const EDIT_LINK_PATTERN = '/admin/settings/configs/edit/%s/';

    final protected const VIEW_LINK_PATTERN = '/admin/settings/configs/view/%s/';

    /**
     * @return string
     * @throws ValuesObjectException
     */
    final public function getName(): string
    {
        return (string)$this->get('name');
    }

    /**
     * @return string
     * @throws ValuesObjectException
     */
    final public function getFilePath(): string
    {
        return (string)$this->get('file_path');
    }

    /**
     * @return array|null
     * @throws ValuesObjectException
     */
    final public function getValues(): ?array
    {
        $values = $this->get('values');

        if (!empty($values) && is_array($values)) {
            return $values;
        }

        return null;
    }

    /**
     * @return string
     * @throws ValuesObjectException
     */
    final public function getViewLink(): string
    {
        return sprintf(
            ConfigValuesObject::VIEW_LINK_PATTERN,
            $this->getName()
        );
    }

    /**
     * @param string|null $name
     * @return void
     * @throws ValuesObjectException
     */
    final public function setName(?string $name = null): void
    {
        if (!empty($name)) {
            $this->set('name', $name);
        }
    }

    /**
     * @param string|null $filePath
     * @return void
     * @throws ValuesObjectException
     */
    final public function setFilePath(?string $filePath = null): void
    {
        if (!empty($filePath)) {
            $this->set('file_path', $filePath);
        }
    }

    /**
     * @param array|null $values
     * @return void
     * @throws ValuesObjectException
     */
    final public function setValues(?array $values = null): void
    {
        if (!empty($values)) {
            $this->set('values', $values);
        }
    }

    /**
     * @return array
     * @throws ConfigValuesObjectException
     */
    final public function exportRow(): array
    {
        $errorMessage = sprintf(
            ConfigValuesObjectException::MESSAGE_VALUES_OBJECT_METHOD_IS_PROHIBITED,
            'exportRow'
        );

        throw new ConfigValuesObjectException(
            $errorMessage,
            ConfigException::CODE_VALUES_OBJECT_METHOD_IS_PROHIBITED
        );
    }
}
