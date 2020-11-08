<?php
/**
 * Plugin For Handling Errors And Exceptions
 */
class ErrorPlugin
{
    /**
     * Output Format In HTML Format
     */
    const OUTPUT_FORMAT_HTML = 'html';

    /**
     * Output Format In JSON Format
     */
    const OUTPUT_FORMAT_JSON = 'json';

    /**
     * HTTP Bad Request Error Code
     */
    const HTTP_BAD_REQUEST = 400;

    /**
     * HTTP Network Authentication Required Error Code
     */
    const HTTP_NETWORK_AUTHENTICATION_REQUIRED = 511;

    /**
     * List OF HTTP Error Codes
     */
    const HTTP_ERROR_LIST = [
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

    /**
     * Default Error Message
     */
    const DEFAULT_ERROR_MESSAGE = 'Unknown Error';

    /**
     * Error HTML Template File Path
     */
    const HTML_ERROR_TEMPLATE_PATH = __DIR__.'/tpl/error_html.phtml';

    /**
     * Error Text Template File Path
     */
    const TEXT_ERROR_TEMPLATE_PATH = __DIR__.'/tpl/error_text.phtml';

    /**
     * Exception HTML Template File Path
     */
    const EXCEPTION_TEMPLATE_PATH = __DIR__.'/tpl/exception.phtml';

    /**
     * Handle HTTP Error
     *
     * @param int $code HTTP Responce Code
     *
     * @return bool Is Successfully Handled HTTP Error
     */
    public function handleHttpError(int $code): bool
    {
        if ($code < static::HTTP_BAD_REQUEST) {
            return false;
        }

        if ($code > static::HTTP_NETWORK_AUTHENTICATION_REQUIRED) {
            return false;
        }

        http_response_code($code);

        return true;
    }

    /**
     * Get HTTP Error Message
     *
     * @param int $code HTTP Responce Code
     *
     * @return string HTTP Error Message
     */
    public function getHttpErrorMessage(int $code): string
    {
        if (!array_key_exists($code, static::HTTP_ERROR_LIST)) {
            return static::DEFAULT_ERROR_MESSAGE;
        }

        if (function_exists('__t')) {
            return (string) __t(static::HTTP_ERROR_LIST[$code]);
        }

        return static::HTTP_ERROR_LIST[$code];
    }

    /**
     * Display Error
     *
     * @param int         $code           HTTP Responce Code
     * @param string      $message        Error Message
     * @param string      $file           PHP File That Contain Error
     * @param int         $line           Line In PHP File That Contain Error
     * @param array|null  $debugBacktrace Backtrace Of Calls That Contain Error
     * @param string|null $outputFormat   Display Error Format
     *
     * @return bool Is Successfully Displayed Error
     */
    public function displayError(
        int     $code,
        string  $message,
        string  $file,
        int     $line,
        ?array  $debugBacktrace = null,
        ?string $outputFormat   = null
    ): bool
    {
        if ($outputFormat  == static::OUTPUT_FORMAT_HTML) {
            return $this->_displayHtmlError(
                $code,
                $message,
                $file,
                $line,
                $debugBacktrace
            );
        }

        if ($outputFormat == static::OUTPUT_FORMAT_JSON) {
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
     * Display Error In HTML Format
     *
     * @param int        $code           HTTP Responce Code
     * @param string     $message        Error Message
     * @param string     $file           PHP File That Contain Error
     * @param int        $line           Line In PHP File That Contain Error
     * @param array|null $debugBacktrace Backtrace Of Calls That Contain Error
     *
     * @return bool Is Successfully Displayed Error
     */
    private function _displayHtmlError(
        int    $code,
        string $message,
        string $file,
        int    $line,
        ?array $debugBacktrace
    ): bool
    {
        $isDisplay = (bool) ini_get('display_errors');

        if (!$isDisplay) {
            echo 'Internal Server Error!';

            return false;
        }

        include static::HTML_ERROR_TEMPLATE_PATH;

        return true;
    }

    /**
     * Display Error In JSON Format
     *
     * @param int        $code           HTTP Responce Code
     * @param string     $message        Error Message
     * @param string     $file           PHP File That Contain Error
     * @param int        $line           Line In PHP File That Contain Error
     * @param array|null $debugBacktrace Backtrace Of Calls That Contain Error
     *
     * @return bool Is Successfully Displayed Error
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

        $isDisplay = (bool) ini_get('display_errors');

        $output['status'] = 'error';
        $output['data']   = [];

        $output['data']['message'] = 'Internal Server Error!';

        if ($isDisplay) {
            $output['data']['error'] = [
                'code'    => $code,
                'message' => $message
            ];

            $output['data']['file'] = [
                'file' => $file,
                'line' => $line
            ];

            $output['data']['trace'] = $debugBacktrace;
        }

        header('Content-Type: application/json');
        echo json_encode($output);

        return true;
    }

    /**
     * Display Error In Text Format
     *
     * @param int        $code           HTTP Responce Code
     * @param string     $message        Error Message
     * @param string     $file           PHP File That Contain Error
     * @param int        $line           Line In PHP File That Contain Error
     * @param array|null $debugBacktrace Backtrace Of Calls That Contain Error
     *
     * @return bool Is Successfully Displayed Error
     */
    private function _displayTextError(
        int    $code,
        string $message,
        string $file,
        int    $line,
        ?array $debugBacktrace
    ): bool
    {
        $isDisplay = (bool) ini_get('display_errors');

        if (!$isDisplay) {
            echo 'Internal Server Error!';

            return false;
        }

        include static::TEXT_ERROR_TEMPLATE_PATH;

        return true;
    }
}
