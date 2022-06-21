<?php

namespace Sonder\Enums;

use Sonder\Core\Interfaces\ICoreEnum;
use Sonder\Interfaces\IContentTypesEnum;

#[ICoreEnum]
#[IContentTypesEnum]
enum ContentTypesEnum: string implements IContentTypesEnum
{
    case TEXT = 'text/plain';
    case HTML = 'text/html';
    case JSON = 'application/json';

    final public const DEFAULT = ContentTypesEnum::HTML;

    private const HTTP_HEADER_PATTERN = 'Content-Type: %s';

    /**
     * @return string
     */
    final public function getHttpHeader(): string
    {
        return sprintf(
            ContentTypesEnum::HTTP_HEADER_PATTERN,
            $this->value
        );
    }
}
