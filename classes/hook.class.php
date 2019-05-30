<?php
/**
 * Core Hook Class
 */
class HookCore extends CommonCore
{
    private $_entityData = [];

    public function __construct(array $entityData = [])
    {
        parent::__construct();
        $this->_entityData = $entityData;
        $this->init();
    }

    public function setEntityParam(
        string $paramName = '',
               $paramData = ''
    ) : void
    {
        $this->_entityData[$paramName] = $paramData;
    }

    public function hasEntityParam(string $paramName = '') : bool
    {
        return array_key_exists($paramName, $this->_entityData);
    }

    public function getEntityParam(string $paramName = '')
    {
        if (!$this->hasEntityParam($paramName)) {
            return NULL;
        }

        return $this->_entityData[$paramName];
    }

    public function getEntity() : array
    {
        return $this->_entityData;
    }

    /**
    * Inital Method Of Hook
    */
    public function init() : void
    {
        // The method can be used by overriding in a child class
    }
}
?>
