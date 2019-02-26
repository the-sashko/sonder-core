<?php
/**
 * Core Model CRUD Class
 */
abstract class ModelCRUDCore extends ModelCore
{
    public function getByID(int $id = -1) : ValuesObject
    {
        $values = $this->object->getByID(
            $this->object->getDefaultTableName(),
            $id
        );

        return $this->getVO($values);
    }

    public function getByPage(int $page = 1) : array
    {
        $values = $this->object->getAllByPage(
            $this->object->getDefaultTableName(),
            [],
            $page
        );

        return $this->getVOArray($values);
    }

    abstract public function create(array $data = []) : array;

    abstract public function updateByID(
        array $data = [],
        int $id = -1
    ) : array;

    public function removeByID(int $id = -1) : bool
    {
        return $this->object->removeByID(
            $this->object->getDefaultTableName(),
            $id
        );
    }
}
?>