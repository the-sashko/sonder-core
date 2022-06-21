<?php

namespace Sonder\Plugins\Annotation\Classes;

use Sonder\Plugins\Annotation\AnnotationException;
use Sonder\Plugins\Annotation\Interfaces\IAnnotationEntity;

#[IAnnotationEntity]
final class AnnotationEntity implements IAnnotationEntity
{
    /**
     * @param string $_name
     * @param string|null $_value
     * @throws AnnotationException
     */
    public function __construct(
        private readonly string $_name,
        private readonly ?string $_value = null
    ) {
        if (empty($this->_name)) {
            throw new AnnotationException(
                AnnotationException::MESSAGE_NAME_IS_EMPTY,
                AnnotationException::CODE_NAME_IS_EMPTY
            );
        }
    }

    /**
     * @return string
     */
    final public function getName(): string
    {
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
