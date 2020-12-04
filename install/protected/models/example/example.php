<?php
/**
 * ModelCore Class For Example Model
 */
class Example extends ModelCore
{
    /**
     * Get All Example Data
     *
     * @return array|null List Of Example Data
     */
    public function getAll(): ?array
    {
        $data = $this->store->getAllExamples();

        return $this->getVOArray($data);
    }
}
