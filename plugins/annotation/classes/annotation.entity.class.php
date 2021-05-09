<?php
namespace Core\Plugins\Annotation\Classes;

use Core\Plugins\Annotation\Interfaces\IAnnotationEntity;

use Core\Plugins\Annotation\Exceptions\AnnotationEntityException;

class AnnotationEntity implements IAnnotationEntity
{
    private $_name = null;

    private $_value = null;

    public function __construct(?string $name = null, ?string $value = null)
    {
        $this->_name  = $name;
        $this->_value = $value;
    }

    public function getName(): string
    {
        if (empty($this->_name)) {
            throw new AnnotationEntityException(
                AnnotationEntityException::MESSAGE_ENTITY_NAME_IS_EMPTY,
                AnnotationEntityException::CODE_ENTITY_NAME_IS_EMPTY
            );
        }

        return $this->_name;
    }

    public function getValue(): ?string
    {
        return $this->_value;
    }
}
