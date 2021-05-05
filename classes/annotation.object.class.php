<?php
/**
 * Class For Working With Annotation Data
 */
class AnnotationObject extends ValuesObject
{
    /**
     * Get Annotation Name
     *
     * @return string|null Annotation Name
     */
    public function getName(): ?string
    {
        if (!$this->has('name')) {
            return null;
        }

        return $this->get('name');
    }

    /**
     * Get Annotation Value
     *
     * @return string|null Annotation Value
     */
    public function getValue(): ?string
    {
        if (!$this->has('value')) {
            return null;
        }

        return $this->get('value');
    }
}
