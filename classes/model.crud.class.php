<?php
/**
 * Core Model CRUD Class
 */
abstract class ModelCRUDCore extends ModelCore
{
    public function getByID(int $id = -1) : array
    {
        return $this->object->getByID($id);
    }

    public function getByPage(int $page = 1) : array
    {
        return $this->object->getAllByPage($page);
    }

    abstract public function create(array $data = []) : array;

    abstract public function updateByID(
        array $data = [],
        int $id = -1
    ) : array;

    public function removeByID(int $id = -1) : bool
    {
        return $this->object->removeByID($id);
    }
}
?>