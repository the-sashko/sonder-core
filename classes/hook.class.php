<?php
/**
 * Core Hook Class
 */
class HookCore extends CommonCore
{
    private $_entityData = [];

    public function __construct(?array $entityData = null)
    {
        parent::__construct();

        if (!empty($entityData)) {
            $this->_entityData = $entityData;
        }

        $this->init();
    }

    public function setEntityParam(?string $key = null, $value = null): void
    {
        if (!empty($key)) {
            $this->_entityData[$key] = $value;
        }
    }

    public function hasEntityParam(?string $key = null): bool
    {
        if (empty($key)) {
            return false;
        }

        return array_key_exists($key, $this->_entityData);
    }

    public function getEntityParam(?string $key = null)
    {
        if (empty($key) || empty($this->_entityData)) {
            return null;
        }

        if (!array_key_exists($key, $this->_entityData)) {
            return null;
        }

        return $this->_entityData[$key];
    }

    public function getEntity(): array
    {
        return $this->_entityData;
    }

    /**
    * Inital Method Of Hook
    */
    public function init(): void
    {
        // The method can be used by overriding in a child class
    }
}
