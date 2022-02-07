<?php

namespace Sonder\Models\Config;

use Exception;
use Sonder\Core\ValuesObject;

final class ConfigValuesObject extends ValuesObject
{
    /**
     * @var string
     */
    private string $_editLinkPattern = '/admin/settings/configs/edit/%s/';

    /**
     * @var string
     */
    private string $_viewLinkPattern = '/admin/settings/configs/view/%s/';

    /**
     * @return string
     * @throws Exception
     */
    final public function getName(): string
    {
        return (string)$this->get('name');
    }

    /**
     * @return string
     * @throws Exception
     */
    final public function getFilePath(): string
    {
        return (string)$this->get('file_path');
    }

    /**
     * @return array|null
     * @throws Exception
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
     * @throws Exception
     */
    final public function getViewLink(): string
    {
        return sprintf($this->_viewLinkPattern, $this->getName());
    }

    /**
     * @return string
     * @throws Exception
     */
    final public function getEditLink(): string
    {
        return sprintf($this->_editLinkPattern, $this->getName());
    }

    /**
     * @param string|null $name
     * @return void
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
     */
    final public function setValues(?array $values = null): void
    {
        if (!empty($values)) {
            $this->set('values', $values);
        }
    }

    /**
     * @param array|null $params
     * @return array|null
     * @throws Exception
     */
    final public function exportRow(?array $params = null): ?array
    {
        throw new Exception(
            'Method "exportRow" is prohibited for this class'
        );
    }
}
