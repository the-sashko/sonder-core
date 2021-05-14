<?php
use PHPUnit\Framework\TestCase;

/**
 * Class For Testing ErrorPlugin Class Methods
 */
class ErrorPluginTest extends TestCase
{
    const INVALID_ERROR_CODES_SAMPLE = [
        -34,
        0,
        199,
        200,
        199,
        260,
        399,
        700,
        1000,
        9000
    ];

    const HTTP_ERRORS_SAMPLE = [
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

    const ERROR_MESSAGE_CODE_SAMPLE = 1234;

    const ERROR_MESSAGE_MESSAGE_SAMPLE = 'Test Error Message';

    const ERROR_MESSAGE_FILE_NAME_SAMPLE = 'test_file.php';

    const ERROR_MESSAGE_FILE_LINE_SAMPLE = 123;

    const ERROR_MESSAGE_BACKTRACE_SAMPLE = [
        'foo',
        'bar',
        'test'
    ];

    const TEXT_ERROR_FILE_PATH = __DIR__.'/../samples/errors/error.txt';

    const JSON_ERROR_FILE_PATH = __DIR__.'/../samples/errors/error.json';

    const HTML_ERROR_FILE_PATH = __DIR__.'/../samples/errors/error.html';

    public function testHandleHttpError()
    {
        $plugin = $this->_getPlugin();

        foreach (static::INVALID_ERROR_CODES_SAMPLE as $invalidErrorCode) {
            $this->assertFalse($plugin->handleHttpError($invalidErrorCode));
            $this->assertFalse(http_response_code());
        }

        foreach (array_keys(static::HTTP_ERRORS_SAMPLE) as $errorCode) {
            $this->assertTrue($plugin->handleHttpError($errorCode));
            $this->assertEquals(http_response_code(), $errorCode);
        }
    }

    public function testGetHttpErrorMessage()
    {
        $plugin = $this->_getPlugin();

        foreach (static::INVALID_ERROR_CODES_SAMPLE as $invalidErrorCode) {
            $this->assertEquals(
                $plugin->getHttpErrorMessage($invalidErrorCode),
                ErrorPlugin::DEFAULT_ERROR_MESSAGE
            );
        }

        foreach (static::HTTP_ERRORS_SAMPLE as $errorCode => $errorMessage) {
            $this->assertEquals(
                $plugin->getHttpErrorMessage($errorCode),
                $errorMessage
            );
        }
    }

    public function testDisplayError()
    {
        $plugin = $this->_getPlugin();

        ob_start();

        $plugin->displayError(
            static::ERROR_MESSAGE_CODE_SAMPLE,
            static::ERROR_MESSAGE_MESSAGE_SAMPLE,
            static::ERROR_MESSAGE_FILE_NAME_SAMPLE,
            static::ERROR_MESSAGE_FILE_LINE_SAMPLE,
            static::ERROR_MESSAGE_BACKTRACE_SAMPLE,
            'text'
        );

        $error = ob_get_contents();

        $this->assertEquals(
            $error,
            file_get_contents(static::TEXT_ERROR_FILE_PATH)
        );

        ob_end_clean();

        ob_start();

        $plugin->displayError(
            static::ERROR_MESSAGE_CODE_SAMPLE,
            static::ERROR_MESSAGE_MESSAGE_SAMPLE,
            static::ERROR_MESSAGE_FILE_NAME_SAMPLE,
            static::ERROR_MESSAGE_FILE_LINE_SAMPLE,
            static::ERROR_MESSAGE_BACKTRACE_SAMPLE,
            'json'
        );

        $error = ob_get_contents();

        $this->assertEquals(
            $error,
            file_get_contents(static::JSON_ERROR_FILE_PATH)
        );

        ob_end_clean();

        ob_start();

        $plugin->displayError(
            static::ERROR_MESSAGE_CODE_SAMPLE,
            static::ERROR_MESSAGE_MESSAGE_SAMPLE,
            static::ERROR_MESSAGE_FILE_NAME_SAMPLE,
            static::ERROR_MESSAGE_FILE_LINE_SAMPLE,
            static::ERROR_MESSAGE_BACKTRACE_SAMPLE,
            'html'
        );

        $error = ob_get_contents();

        $this->assertEquals(
            $error,
            file_get_contents(static::HTML_ERROR_FILE_PATH)
        );

        ob_end_clean();
    }

    private function _getPlugin(): ErrorPlugin
    {
        if (empty($this->_plugin)) {
            $this->_plugin = (new CommonCore)->getPlugin('error');
        }

        return $this->_plugin;
    }
}
