<?php

namespace Sonder\Enums;

use Sonder\Core\Interfaces\ICoreEnum;
use Sonder\Interfaces\IHttpMethodsEnum;

#[ICoreEnum]
#[IHttpMethodsEnum]
enum HttpMethodsEnum: string implements IHttpMethodsEnum
{
    case CONNECT = 'connect';
    case DELETE = 'delete';
    case GET = 'get';
    case HEAD = 'head';
    case OPTIONS = 'options';
    case PATCH = 'patch';
    case POST = 'post';
    case PUT = 'put';
    case TRACE = 'trace';

    final public const DEFAULT = HttpMethodsEnum::GET;

    final public function isConnect(): bool
    {
        return $this == HttpMethodsEnum::CONNECT;
    }

    final public function isDelete(): bool
    {
        return $this == HttpMethodsEnum::DELETE;
    }

    final public function isGet(): bool
    {
        return $this == HttpMethodsEnum::GET;
    }

    final public function isHead(): bool
    {
        return $this == HttpMethodsEnum::HEAD;
    }

    final public function isOptions(): bool
    {
        return $this == HttpMethodsEnum::OPTIONS;
    }

    final public function isPatch(): bool
    {
        return $this == HttpMethodsEnum::PATCH;
    }

    final public function isPost(): bool
    {
        return $this == HttpMethodsEnum::POST;
    }

    final public function isPut(): bool
    {
        return $this == HttpMethodsEnum::PUT;
    }

    final public function isTrace(): bool
    {
        return $this == HttpMethodsEnum::TRACE;
    }
}
