<?php

namespace Sonder\Core;

use Exception;

class ResponseObject
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
     * @var string
     */
    private string $_contentType;

    /**
     * @var string|null
     */
    private ?string $_content = null;

    /**
     * @var bool
     */
    private bool $_isCached = false;

    /**
     * @var int
     */
    private int $_httpCode;

    /**
     * @var RedirectObject|null
     */
    public ?RedirectObject $redirect = null;

    public function __construct()
    {
        $this->redirect = new RedirectObject();
        $this->_httpCode = static::DEFAULT_HTTP_CODE;
        $this->_contentType = static::DEFAULT_CONTENT_TYPE;
    }

    /**
     * @return string
     */
    final public function getContentType(): string
    {
        return $this->_contentType;
    }

    /**
     * @return string|null
     */
    final public function getContent(): ?string
    {
        return $this->_content;
    }

    /**
     * @return bool
     */
    final public function getIsCached(): bool
    {
        return $this->_isCached;
    }

    /**
     * @return int
     */
    final public function getHttpCode(): int
    {
        return $this->_httpCode;
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
            !in_array($contentType, array_keys(static::CONTENT_TYPES))
        ) {
            throw new Exception('Unsupported Content Type');
        }

        $this->_contentType = $contentType;
    }

    final public function getHttpHeader(): void
    {
        //TODO
    }

    /**
     * @param string|null $content
     */
    final public function setContent(?string $content = null): void
    {
        $this->_content = $content;
    }

    /**
     * @param bool $isCached
     */
    final public function setIsCached(bool $isCached = false): void
    {
        $this->_isCached = $isCached;
    }

    /**
     * @param int $httpCode
     *
     * @throws Exception
     */
    final public function setHttpCode(int $httpCode = 200): void
    {
        if (
            $httpCode != static::DEFAULT_HTTP_CODE &&
            !in_array($httpCode, array_keys(static::HTTP_ERRORS))
        ) {
            throw new Exception(
                sprintf('Unsupported HTTP Code: %d', $httpCode)
            );
        }

        $this->_httpCode = $httpCode;
    }
}
