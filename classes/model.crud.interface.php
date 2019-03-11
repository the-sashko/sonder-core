<?php
/**
 * Model CRUD Interface
 */
interface ModelCRUD
{
    public function formHandler(
        array $formData = [],
        int $id = -1
    ) : array;
}
?>