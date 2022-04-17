<?php

namespace Sonder\Plugins\Annotation\Classes;

use Sonder\Plugins\Annotation\Exceptions\AnnotationEntityException;
use Sonder\Plugins\Annotation\Exceptions\AnnotationException;
use Sonder\Plugins\Annotation\Interfaces\IAnnotationEntity;

final class AnnotationEntity implements IAnnotationEntity
{
    /**
     * @var string|null
     */
    private ?string $_name;

    /**
     * @var string|null
     */
    private ?string $_value;

    /**
     * @param string|null $name
     * @param string|null $value
     */
    public function __construct(?string $name = null, ?string $value = null)
    {
        $this->_name = $name;
        $this->_value = $value;
    }

    /**
     * @return string
     *
     * @throws AnnotationEntityException
     */
    final public function getName(): string
    {
        if (empty($this->_name)) {
            throw new AnnotationEntityException(
                AnnotationEntityException::MESSAGE_ENTITY_NAME_IS_EMPTY,
                AnnotationException::CODE_ENTITY_NAME_IS_EMPTY
            );
        }

        return $this->_name;
    }

    /**
     * @return string|null
     */
    final public function getValue(): ?string
    {
        return $this->_value;
    }
}
