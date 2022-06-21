<?php

namespace Sonder\Exceptions;

use Attribute;
use Exception;
use Sonder\Core\Interfaces\ICoreException;
use Sonder\Enums\HttpCodesEnum;

#[ICoreException]
#[Attribute(Attribute::TARGET_CLASS)]
class BaseException extends Exception implements ICoreException
{
    final protected const DEFAULT_HTTP_RESPONSE_CODE = HttpCodesEnum::INTERNAL_SERVER_ERROR;

    /**
     * @var int|null
     */
    protected ?int $httpResponseCode = null;

    /**
     * @return int
     */
    final public function getHttpResponseCode(): int
    {
        if (empty($this->httpResponseCode)) {
            return static::DEFAULT_HTTP_RESPONSE_CODE->value;
        }

        $httpResponseCode = HttpCodesEnum::tryFrom($this->httpResponseCode);

        if (empty($httpResponseCode)) {
            $httpResponseCode = static::DEFAULT_HTTP_RESPONSE_CODE;
        }

        return $httpResponseCode->value;
    }
}
