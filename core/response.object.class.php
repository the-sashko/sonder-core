<?php

namespace Sonder\Core;

use Exception;

final class ResponseObject
{
    const CONTENT_TYPES = [
        'html' => 'text/html',
        'json' => 'application/json'
    ];

    const HTTP_ERRORS = [
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I am a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Too Early',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required'
    ];

    const DEFAULT_CONTENT_TYPE = 'html';

    const DEFAULT_HTTP_CODE = 200;

    /**
     * @var int
     */
    private int $_httpCode;

    /**
     * @var string
     */
    private string $_contentType;

    /**
     * @var string|null
     */
    private ?string $_content = null;

    /**
     * @var RedirectObject
     */
    public RedirectObject $redirect;

    final public function __construct()
    {
        $this->_httpCode = ResponseObject::DEFAULT_HTTP_CODE;
        $this->_contentType = ResponseObject::DEFAULT_CONTENT_TYPE;
        $this->redirect = new RedirectObject();
    }

    /**
     * @return array
     */
    final public function __serialize(): array
    {
        return [
            'http_code' => $this->_httpCode,
            'content_type' => $this->_contentType,
            'content' => base64_encode($this->_content),
            'redirect' => base64_encode(serialize($this->redirect))
        ];
    }

    /**
     * @param array $values
     */
    final public function __unserialize(array $values): void
    {
        $this->_httpCode = (int)$values['http_code'];
        $this->_contentType = $values['content_type'];
        $this->_content = base64_decode($values['content']);
        $this->redirect = unserialize(base64_decode($values['redirect']));
    }

    /**
     * @return int
     */
    final public function getHttpCode(): int
    {
        return $this->_httpCode;
    }

    /**
     * @return string
     */
    final public function getContentTypeHeader(): string
    {
        $contentType = ResponseObject::CONTENT_TYPES[$this->_contentType];

        return sprintf('Content-Type: %s', $contentType);
    }

    /**
     * @return string|null
     */
    final public function getContent(): ?string
    {
        return $this->_content;
    }

    /**
     * @param int $httpCode
     *
     * @throws Exception
     */
    final public function setHttpCode(int $httpCode = 200): void
    {
        if (
            $httpCode != ResponseObject::DEFAULT_HTTP_CODE &&
            !in_array($httpCode, array_keys(ResponseObject::HTTP_ERRORS))
        ) {
            throw new Exception(
                sprintf('Unsupported HTTP Code: %d', $httpCode)
            );
        }

        $this->_httpCode = $httpCode;
    }

    /**
     * @param string|null $contentType
     *
     * @throws Exception
     */
    final public function setContentType(?string $contentType = null): void
    {
        if (
            empty($contentType) ||
            !in_array($contentType, array_keys(ResponseObject::CONTENT_TYPES))
        ) {
            throw new Exception('Unsupported Content Type');
        }

        $this->_contentType = $contentType;
    }

    /**
     * @param string|null $content
     */
    final public function setContent(?string $content = null): void
    {
        $this->_content = $content;
    }
}
