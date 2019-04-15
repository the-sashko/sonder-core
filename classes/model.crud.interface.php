<?php
/**
 * Model CRUD Interface
 */
interface ModelCRUD
{
    /**
     * Handler For Model Form That Creating Or Editing Model Item
     *
     * @param array $formData Data From Form
     * @param int   $id       Model Item ID
     *
     * @return array Result Of Processing Form
     */
    public function formHandler(
        array $formData = [],
        int   $id       = -1
    ) : array;
}
?>