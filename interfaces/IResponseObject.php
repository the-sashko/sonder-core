<?php

namespace Sonder\Interfaces;

use Attribute;
use Sonder\Enums\ContentTypesEnum;
use Sonder\Enums\HttpCodesEnum;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
interface IResponseObject
{
    /**
     * @return int
     */
    public function getHttpCode(): int;

    /**
     * @return string
     */
    public function getContentTypeHeader(): string;

    /**
     * @return string|null
     */
    public function getContent(): ?string;

    /**
     * @param IHttpCodesEnum $httpCode
     * @return void
     */
    public function setHttpCode(
        IHttpCodesEnum $httpCode = HttpCodesEnum::DEFAULT
    ): void;

    /**
     * @param IContentTypesEnum $contentType
     * @return void
     */
    public function setContentType(
        IContentTypesEnum $contentType = ContentTypesEnum::DEFAULT
    ): void;

    /**
     * @param string|null $content
     * @return void
     */
    public function setContent(?string $content = null): void;
}
