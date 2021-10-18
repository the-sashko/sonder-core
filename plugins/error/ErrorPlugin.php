<?php

namespace Sonder\Plugins;

use function Sonder\__t;

final class ErrorPlugin
{
    const OUTPUT_FORMAT_HTML = 'html';

    const OUTPUT_FORMAT_JSON = 'json';

    const HTTP_BAD_REQUEST = 400;

    const HTTP_NETWORK_AUTHENTICATION_REQUIRED = 511;

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

    const DEFAULT_ERROR_MESSAGE = 'Unknown Error';

    const HTML_ERROR_TEMPLATE_PATH = __DIR__ . '/templates/error_html.phtml';

    const TEXT_ERROR_TEMPLATE_PATH = __DIR__ . '/templates/error_text.phtml';

    /**
     * @param int $code
     *
     * @return bool
     */
    final public function handleHttpError(int $code): bool
    {
        if ($code < ErrorPlugin::HTTP_BAD_REQUEST) {
            return false;
        }

        if ($code > ErrorPlugin::HTTP_NETWORK_AUTHENTICATION_REQUIRED) {
            return false;
        }

        http_response_code($code);

        return true;
    }

    /**
     * @param int $code
     *
     * @return string
     *
     * @throws Language\Exceptions\LanguageException
     */
    final public function getHttpErrorMessage(int $code): string
    {
        if (!array_key_exists($code, ErrorPlugin::HTTP_ERRORS)) {
            return ErrorPlugin::DEFAULT_ERROR_MESSAGE;
        }

        if (function_exists('__t')) {
            return __t(ErrorPlugin::HTTP_ERRORS[$code]);
        }

        return ErrorPlugin::HTTP_ERRORS[$code];
    }

    /**
     * @param int $code
     * @param string $message
     * @param string $file
     * @param int $line
     * @param array|null $debugBacktrace
     * @param string|null $outputFormat
     *
     * @return bool
     */
    final public function displayError(
        int     $code,
        string  $message,
        string  $file,
        int     $line,
        ?array  $debugBacktrace = null,
        ?string $outputFormat = null
    ): bool
    {
        if ($outputFormat == ErrorPlugin::OUTPUT_FORMAT_HTML) {
            return $this->_displayHtmlError(
                $code,
                $message,
                $file,
                $line,
                $debugBacktrace
            );
        }

        if ($outputFormat == ErrorPlugin::OUTPUT_FORMAT_JSON) {
            return $this->_displayJsonError(
                $code,
                $message,
                $file,
                $line,
                $debugBacktrace
            );
        }

        return $this->_displayTextError(
            $code,
            $message,
            $file,
            $line,
            $debugBacktrace
        );
    }

    /**
     * @param int $code
     * @param string $message
     * @param string $file
     * @param int $line
     * @param array|null $debugBacktrace
     *
     * @return bool
     */
    private function _displayHtmlError(
        int    $code,
        string $message,
        string $file,
        int    $line,
        ?array $debugBacktrace
    ): bool
    {
        $isDisplay = (bool)ini_get('display_errors');

        if (!$isDisplay) {
            echo 'Internal Server Error!';

            return false;
        }

        include ErrorPlugin::HTML_ERROR_TEMPLATE_PATH;

        return true;
    }

    /**
     * @param int $code
     * @param string $message
     * @param string $file
     * @param int $line
     * @param array|null $debugBacktrace
     *
     * @return bool
     */
    private function _displayJsonError(
        int    $code,
        string $message,
        string $file,
        int    $line,
        ?array $debugBacktrace
    ): bool
    {
        $output = [];

        $isDisplay = (bool)ini_get('display_errors');

        $output['status'] = 'error';
        $output['data'] = [];

        $output['data']['message'] = 'Internal Server Error!';

        if ($isDisplay) {
            $output['data']['error'] = [
                'code' => $code,
                'message' => $message
            ];

            $output['data']['file'] = [
                'file' => $file,
                'line' => $line
            ];

            $output['data']['trace'] = $debugBacktrace;
        }

        if (!defined('PHP_UNIT')) {
            header('Content-Type: application/json');
        }

        echo json_encode($output);

        return true;
    }

    /**
     * @param int $code
     * @param string $message
     * @param string $file
     * @param int $line
     * @param array|null $debugBacktrace
     *
     * @return bool
     */
    private function _displayTextError(
        int    $code,
        string $message,
        string $file,
        int    $line,
        ?array $debugBacktrace
    ): bool
    {
        $isDisplay = (bool)ini_get('display_errors');

        if (!$isDisplay) {
            echo 'Internal Server Error!';

            return false;
        }

        include ErrorPlugin::TEXT_ERROR_TEMPLATE_PATH;

        return true;
    }
}
