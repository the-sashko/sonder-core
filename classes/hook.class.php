<?php

/**
 * Core Hook Class
 */
class HookCore extends CommonCore
{
    /**
     * @var array
     */
    private array $_entityData = [];

    /**
     * @param array|null $entityData
     *
     * @throws CoreException
     */
    public function __construct(?array $entityData = null)
    {
        parent::__construct();

        if (!empty($entityData)) {
            $this->_entityData = $entityData;
        }

        $this->init();
    }

    /**
     * Initial Method Of Hook
     */
    public function init(): void
    {
        // The method can be used by overriding in a child class
    }

    /**
     * @param string|null $key
     * @param null $value
     */
    final protected function setEntityParam(?string $key = null, $value = null): void
    {
        if (!empty($key)) {
            $this->_entityData[$key] = $value;
        }
    }

    /**
     * @param string|null $key
     *
     * @return bool
     */
    final protected function hasEntityParam(?string $key = null): bool
    {
        if (empty($key)) {
            return false;
        }

        return array_key_exists($key, $this->_entityData);
    }

    /**
     * @param string|null $key
     *
     * @return mixed
     */
    final protected function getEntityParam(?string $key = null): mixed
    {
        if (empty($key) || empty($this->_entityData)) {
            return null;
        }

        if (!array_key_exists($key, $this->_entityData)) {
            return null;
        }

        return $this->_entityData[$key];
    }

    /**
     * @return array
     */
    final protected function getEntity(): array
    {
        return $this->_entityData;
    }
}
