<?php

namespace Sonder\Plugins;

use Sonder\Plugins\Error\OutputFormatEnum;
use Sonder\Plugins\Error\IErrorPlugin;

final class ErrorPlugin implements IErrorPlugin
{
    private const HTML_ERROR_TEMPLATE_PATH = __DIR__ . '/templates/error_html.phtml';

    private const TEXT_ERROR_TEMPLATE_PATH = __DIR__ . '/templates/error_text.phtml';

    final public function __construct(private readonly string $_outputFormat) {}

    /**
     * @param int $code
     * @param string $message
     * @param string $file
     * @param int $line
     * @param array|null $debugBacktrace
     * @param int $httpResponseCode
     * @return bool
     */
    final public function displayError(
        int $code,
        string $message,
        string $file,
        int $line,
        ?array $debugBacktrace,
        int $httpResponseCode = 500
    ): bool {
        $outputFormat = OutputFormatEnum::tryFrom($this->_outputFormat);

        if (empty($outputFormat)) {
            $outputFormat = OutputFormatEnum::DEFAULT;
        }

        http_response_code($httpResponseCode);

        if ($outputFormat == OutputFormatEnum::HTML) {
            return $this->_displayHtmlError(
                $code,
                $message,
                $file,
                $line,
                $debugBacktrace
            );
        }

        if ($outputFormat == OutputFormatEnum::JSON) {
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
     * @return bool
     */
    private function _displayHtmlError(
        int $code,
        string $message,
        string $file,
        int $line,
        ?array $debugBacktrace
    ): bool {
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
     * @return bool
     */
    private function _displayJsonError(
        int $code,
        string $message,
        string $file,
        int $line,
        ?array $debugBacktrace
    ): bool {
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
     * @return bool
     */
    private function _displayTextError(
        int $code,
        string $message,
        string $file,
        int $line,
        ?array $debugBacktrace
    ): bool {
        $isDisplay = (bool)ini_get('display_errors');

        if (!$isDisplay) {
            echo 'Internal Server Error!';

            return false;
        }

        include ErrorPlugin::TEXT_ERROR_TEMPLATE_PATH;

        return true;
    }
}
