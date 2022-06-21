<?php

namespace Sonder\Core;

use Sonder\Enums\ContentTypesEnum;
use Sonder\Enums\HttpCodesEnum;
use Sonder\Interfaces\IContentTypesEnum;
use Sonder\Interfaces\IHttpCodesEnum;
use Sonder\Interfaces\IRedirectObject;
use Sonder\Interfaces\IResponseObject;

#[IResponseObject]
final class ResponseObject implements IResponseObject
{
    /**
     * @var IRedirectObject
     */
    #[IRedirectObject]
    public IRedirectObject $redirect;

    /**
     * @param IContentTypesEnum $_contentType
     * @param IHttpCodesEnum $_httpCode
     * @param string|null $_content
     */
    final public function __construct(
        #[IContentTypesEnum]
        private IContentTypesEnum $_contentType = ContentTypesEnum::DEFAULT,
        #[IHttpCodesEnum]
        private IHttpCodesEnum $_httpCode = HttpCodesEnum::DEFAULT,
        private ?string $_content = null
    ) {
        $this->redirect = new RedirectObject();
    }

    /**
     * @return array
     */
    final public function __serialize(): array
    {
        return [
            'http_code' => $this->_httpCode->value,
            'content_type' => $this->_contentType->value,
            'content' => base64_encode((string)$this->_content),
            'redirect' => base64_encode(serialize($this->redirect))
        ];
    }

    /**
     * @param array $values
     * @return void
     */
    final public function __unserialize(array $values): void
    {
        $this->_httpCode = $this->_httpCode::from(
            (int)$values['http_code']
        );

        $this->_contentType = $this->_contentType::from(
            $values['content_type']
        );

        $this->_content = base64_decode((string)$values['content']);

        $this->redirect = unserialize(
            base64_decode((string)$values['redirect'])
        );
    }

    /**
     * @return int
     */
    final public function getHttpCode(): int
    {
        return $this->_httpCode->value;
    }

    /**
     * @return string
     */
    final public function getContentTypeHeader(): string
    {
        return $this->_contentType->getHttpHeader();
    }

    /**
     * @return string|null
     */
    final public function getContent(): ?string
    {
        return $this->_content;
    }

    /**
     * @param IHttpCodesEnum $httpCode
     * @return void
     */
    final public function setHttpCode(
        IHttpCodesEnum $httpCode = HttpCodesEnum::DEFAULT
    ): void {
        $this->_httpCode = $httpCode;
    }

    /**
     * @param IContentTypesEnum $contentType
     * @return void
     */
    final public function setContentType(
        IContentTypesEnum $contentType = ContentTypesEnum::DEFAULT
    ): void {
        $this->_contentType = $contentType;
    }

    /**
     * @param string|null $content
     * @return void
     */
    final public function setContent(?string $content = null): void
    {
        $this->_content = $content;
    }
}
