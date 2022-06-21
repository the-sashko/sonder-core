<?php

namespace Sonder\Enums;

use Sonder\Core\Interfaces\ICoreEnum;
use Sonder\Interfaces\IHttpCodesEnum;

#[ICoreEnum]
#[IHttpCodesEnum]
enum HttpCodesEnum: int implements IHttpCodesEnum
{
    case OK = 200;
    case MOVED_PERMANENTLY = 301;
    case FOUND = 302;
    case BAD_REQUEST = 400;
    case UNAUTHORIZED = 401;
    case PAYMENT_REQUIRED = 402;
    case FORBIDDEN = 403;
    case NOT_FOUND = 404;
    case METHOD_NOT_ALLOWED = 405;
    case NOT_ACCEPTABLE = 406;
    case PROXY_AUTHENTICATION_REQUIRED = 407;
    case REQUEST_TIMEOUT = 408;
    case CONFLICT = 409;
    case GONE = 410;
    case LENGTH_REQUIRED = 411;
    case PRECONDITION_FAILED = 412;
    case PAYLOAD_TOO_LARGE = 413;
    case URI_TOO_LONG = 414;
    case UNSUPPORTED_MEDIA_TYPE = 415;
    case RANGE_NOT_SATISFIABLE = 416;
    case EXPECTATION_FAILED = 417;
    case I_AM_A_TEAPOT = 418;
    case MISDIRECTED_REQUEST = 421;
    case UNPROCESSABLE_ENTITY = 422;
    case LOCKED = 423;
    case FAILED_DEPENDENCY = 424;
    case TOO_EARLY = 425;
    case UPGRADE_REQUIRED = 426;
    case PRECONDITION_REQUIRED = 428;
    case TOO_MANY_REQUESTS = 429;
    case REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
    case UNAVAILABLE_FOR_LEGAL_REASONS = 451;
    case INTERNAL_SERVER_ERROR = 500;
    case NOT_IMPLEMENTED = 501;
    case BAD_GATEWAY = 502;
    case SERVICE_UNAVAILABLE = 503;
    case GATEWAY_TIMEOUT = 504;
    case HTTP_VERSION_NOT_SUPPORTED = 505;
    case VARIANT_ALSO_NEGOTIATES = 506;
    case INSUFFICIENT_STORAGE = 507;
    case LOOP_DETECTED = 508;
    case NOT_EXTENDED = 510;
    case NETWORK_AUTHENTICATION_REQUIRED = 511;

    final public const DEFAULT = HttpCodesEnum::OK;

    /**
     * @return string
     */
    final public function getMessage(): string
    {
        return match ($this) {
            HttpCodesEnum::OK => 'OK',
            HttpCodesEnum::MOVED_PERMANENTLY => 'Moved Permanently',
            HttpCodesEnum::FOUND => 'Found',
            HttpCodesEnum::BAD_REQUEST => 'Bad Request',
            HttpCodesEnum::UNAUTHORIZED => 'Unauthorized',
            HttpCodesEnum::PAYMENT_REQUIRED => 'Payment Required',
            HttpCodesEnum::FORBIDDEN => 'Forbidden',
            HttpCodesEnum::NOT_FOUND => 'Not Found',
            HttpCodesEnum::METHOD_NOT_ALLOWED => 'Method Not Allowed',
            HttpCodesEnum::NOT_ACCEPTABLE => 'Not Acceptable',
            HttpCodesEnum::PROXY_AUTHENTICATION_REQUIRED => 'Proxy Authentication Required',
            HttpCodesEnum::REQUEST_TIMEOUT => 'Request Timeout',
            HttpCodesEnum::CONFLICT => 'Conflict',
            HttpCodesEnum::GONE => 'Gone',
            HttpCodesEnum::LENGTH_REQUIRED => 'Length Required',
            HttpCodesEnum::PRECONDITION_FAILED => 'Precondition Failed',
            HttpCodesEnum::PAYLOAD_TOO_LARGE => 'Payload Too Large',
            HttpCodesEnum::URI_TOO_LONG => 'URI Too Long',
            HttpCodesEnum::UNSUPPORTED_MEDIA_TYPE => 'Unsupported Media Type',
            HttpCodesEnum::RANGE_NOT_SATISFIABLE => 'Range Not Satisfiable',
            HttpCodesEnum::EXPECTATION_FAILED => 'Expectation Failed',
            HttpCodesEnum::I_AM_A_TEAPOT => 'I am a teapot',
            HttpCodesEnum::MISDIRECTED_REQUEST => 'Misdirected Request',
            HttpCodesEnum::UNPROCESSABLE_ENTITY => 'Unprocessable Entity',
            HttpCodesEnum::LOCKED => 'Locked',
            HttpCodesEnum::FAILED_DEPENDENCY => 'Failed Dependency',
            HttpCodesEnum::TOO_EARLY => 'Too Early',
            HttpCodesEnum::UPGRADE_REQUIRED => 'Upgrade Required',
            HttpCodesEnum::PRECONDITION_REQUIRED => 'Precondition Required',
            HttpCodesEnum::TOO_MANY_REQUESTS => 'Too Many Requests',
            HttpCodesEnum::REQUEST_HEADER_FIELDS_TOO_LARGE => 'Request Header Fields Too Large',
            HttpCodesEnum::UNAVAILABLE_FOR_LEGAL_REASONS => 'Unavailable for Legal Reasons',
            HttpCodesEnum::INTERNAL_SERVER_ERROR => 'Internal Server Error',
            HttpCodesEnum::NOT_IMPLEMENTED => 'Not Implemented',
            HttpCodesEnum::BAD_GATEWAY => 'Bad Gateway',
            HttpCodesEnum::SERVICE_UNAVAILABLE => 'Service Unavailable',
            HttpCodesEnum::GATEWAY_TIMEOUT => 'Gateway Timeout',
            HttpCodesEnum::HTTP_VERSION_NOT_SUPPORTED => 'HTTP Version Not Supported',
            HttpCodesEnum::VARIANT_ALSO_NEGOTIATES => 'Variant Also Negotiates',
            HttpCodesEnum::INSUFFICIENT_STORAGE => 'Insufficient Storage',
            HttpCodesEnum::LOOP_DETECTED => 'Loop Detected',
            HttpCodesEnum::NOT_EXTENDED => 'Not Extended',
            HttpCodesEnum::NETWORK_AUTHENTICATION_REQUIRED => 'Network Authentication Required'
        };
    }
}
